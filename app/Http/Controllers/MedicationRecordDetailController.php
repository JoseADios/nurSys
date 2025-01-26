<?php

namespace App\Http\Controllers;

use App\Models\MedicationRecord;
use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use App\Models\Drug;
use App\Models\DrugDose;
use App\Models\DrugRoute;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


class MedicationRecordDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($medicationRecordId)
    {
        $medicationRecord = MedicationRecord::findOrFail($medicationRecordId);

        $drug = Drug::all();
        $dose = DrugDose::all();
        $route = DrugRoute::all();


        return Inertia::render('MedicationRecordDetail/Create', [
            'medicationRecord' => $medicationRecord,
            'id' => $medicationRecord->id,
            'drug' => $drug,
            'dose' => $dose,
            'routeOptions' => $route
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $start_time_24 = Carbon::parse($request->start_time)->format("H:i");
        // Primero guarda el detalle y obtén su ID

        $dose_formatted = $request->dose . $request->dose_metric;



        $detail = MedicationRecordDetail::create([
            'medication_record_id' => $request->medication_record_id,
            'drug' => $request->drug,
            'dose' => $dose_formatted,
            'route' => $request->route,
            'fc' => $request->fc,
            'interval_in_hours' => $request->interval_in_hours,
           'start_time' =>$start_time_24,
            'active' => true,
            'created_at' => now(),

        ]);


        // Obtén el valor de fc
        $fc = $request->fc;
        $start_time = Carbon::parse($request->start_time);

        $interval_in_hours = $request->interval_in_hours;

            for ($i = 0; $i < $fc; $i++) {
                $scheduled_time = $start_time->copy()->addHours($i * $interval_in_hours);
                    Log::info($scheduled_time);
                MedicationNotification::create([
                    'medication_record_detail_id' => $detail->id, // Usa el ID del detalle
                    'scheduled_time' => $scheduled_time,
                    "active"=> 1,
                    "nurse_id" => Auth::id(),


                ]);
            }

        return redirect()
            ->route('medicationRecords.show', $request->medication_record_id)
            ->with('success', 'Detalle y notificaciones agregados exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicationRecordDetail $medicationRecordDetail)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicationRecordDetail $medicationRecordDetail)
    {

             // Verificar si Ya Existe una notifiacion con medicamentos administrados
             $existingnotification = MedicationNotification::where('medication_record_detail_id', $medicationRecordDetail->id)->first();


             $Applied = $existingnotification->applied;
             if ($Applied == 1) {
                 // Si ya existe, redirigir con un mensaje de error
                 return redirect()->route('medicationRecords.show', $medicationRecordDetail->medication_record_id)->withErrors([
                     'medication_record_detail_id' => 'Ya Existe una notifiacion con medicamentos administrados.',
                 ])->withInput();
             }
            return Inertia::render('MedicationRecordDetail/Edit', [
                'medicationRecordDetail' => $medicationRecordDetail
            ]);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicationRecordDetail  $medicationRecordDetail)
    {
        if ($request->has('active')) {
            $this->restore($medicationRecordDetail->id);

        }else{

        $fc = $request->fc;
        $interval_in_hours = $request->interval_in_hours;
        $start_time = $request->start_time;
        $notifications = $medicationRecordDetail->medicationNotification()->count();
        $lastNotification = $medicationRecordDetail
        ->medicationNotification()
        ->latest('scheduled_time')
        ->first();

$lastNotificationform = $lastNotification ? Carbon::parse($lastNotification->scheduled_time) : null;

    // Validar la frecuencia al saber el nuevo valor si hay mas notificaciones eliminar las que sobran y si faltan crearlas.
    if($fc > $notifications){

        $notifications_add = $fc - $notifications;

        for ($i = 1; $i < $notifications_add; $i++) {


            $scheduled_time = $lastNotificationform->copy()->addHours($i * $request->interval_in_hours);

          try {
            $medicationRecordDetail->medicationNotification()->create([
                'medication_record_detail_id' => $medicationRecordDetail->id,
                'scheduled_time' => $scheduled_time,

                "active"=> 1,
                "nurse_id" => Auth::id(),


            ]);
          } catch (\Throwable $th) {
            Log::error('error en crear'.$th->getMessage());
          }

        }

    } elseif ($fc < $notifications) {

        try {
            $notifications_delete = $notifications - $fc;
            $medicationRecordDetail->medicationNotification()->latest()->take($notifications_delete)->get()->each(function($notification){
                $notification->delete();
            });
        } catch (\Throwable $th) {
            Log::error('error en eliminar'.$th->getMessage());
          }

    }
    else{
      Log::error('no hubo cambios delete/create');
    }

        // Si intervalo en horas se cambia actualizar hora programada para todas las notificaciones relacionadas.
        if ($medicationRecordDetail->interval_in_hours != $interval_in_hours || $medicationRecordDetail->start_time != $start_time) {
            try {
                $notificationsi = $medicationRecordDetail->medicationNotification()->get();

                if ($medicationRecordDetail->interval_in_hours != $interval_in_hours && $medicationRecordDetail->start_time == $start_time) {
                        // Actualizar solo en base al intervalo en horas
                    $formatted_start_time = Carbon::parse($start_time);
                    foreach ($notificationsi as $index => $notification) {
                        $scheduled_time = $formatted_start_time
                            ->copy()
                            ->addHours($index * $interval_in_hours)
                            ->toDateTimeString();

                        $notification->update(['scheduled_time' => $scheduled_time]);
                    }
                } elseif ($medicationRecordDetail->start_time != $start_time && $medicationRecordDetail->interval_in_hours == $interval_in_hours) {
                    // Actualizar solo en base a la hora de inicio
                    $formatted_start_time = Carbon::parse($start_time);
                    foreach ($notificationsi as $index => $notification) {
                        $scheduled_time = $formatted_start_time
                            ->copy()
                            ->addHours($index * $interval_in_hours)
                            ->toDateTimeString();

                        $notification->update(['scheduled_time' => $scheduled_time]);
                    }
                } elseif ($medicationRecordDetail->interval_in_hours != $interval_in_hours && $medicationRecordDetail->start_time != $start_time) {
                    // Ambas variables cambiaron, aplicar actualizaciones y registrar en el log
                    $formatted_start_time = Carbon::parse($start_time);
                    foreach ($notificationsi as $index => $notification) {
                        $scheduled_time = $formatted_start_time
                            ->copy()
                            ->addHours($index * $interval_in_hours)
                            ->toDateTimeString();

                        $notification->update(['scheduled_time' => $scheduled_time]);
                    }
                }
            } catch (\Throwable $th) {
                Log::error('Error al actualizar notificaciones: ' . $th->getMessage());
            }
        }else{
            Log::error('no hubo cambios');
          }
          // Si se cambia hora de inicio actualizar hora programada para todas las notificaciones relacionadas.




        $medicationRecordDetail->update($request->all());
        return Redirect::route('medicationNotification.show', $medicationRecordDetail->id);
    }


    }

    private function restore($id)
    {
        $medicationRecordDetail = MedicationRecordDetail::findOrFail($id);


        $medicationNotifications = $medicationRecordDetail->medicationNotification()->get();
        foreach ($medicationNotifications as $notification) {
            $notification->update(['active' => 1]);
        }


        $medicationRecordDetail->update(['active' => true]);

        return Redirect::route('medicationRecords.show', $medicationRecordDetail->medication_record_id);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRecordDetail $medicationRecordDetail)
    {



        $medicationNotifications = $medicationRecordDetail->medicationNotification()->get();
        foreach ($medicationNotifications as $notification) {
            $notification->update(['active' => 0]);
        }


        $medicationRecordDetail->update(['active' => false]);

        return Redirect::route('medicationRecords.show', $medicationRecordDetail->medication_record_id);
    }
}
