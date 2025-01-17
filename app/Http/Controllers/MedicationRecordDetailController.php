<?php

namespace App\Http\Controllers;

use App\Models\MedicationRecord;
use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

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
        return Inertia::render('MedicationRecordDetail/Create', [
            'medicationRecord' => $medicationRecord,
            'id' => $medicationRecord->id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $start_time_24 = Carbon::parse($request->start_time)->format("H:i");
        // Primero guarda el detalle y obtén su ID
        $detail = MedicationRecordDetail::create([
            'medication_record_id' => $request->medication_record_id,
            'drug' => $request->drug,
            'dose' => $request->dose,
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
                    'nurse_sign' => "a",
                    "active"=> 1,
                    'nurse_id' => 1,


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
        return Inertia::render('MedicationRecordDetail/Edit', [
            'medicationRecordDetail' => $medicationRecordDetail
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicationRecordDetail  $medicationRecordDetail)
    {


        $fc = $request->fc;
        $interval_in_hours = $request->interval_in_hours;
        $start_time = $request->start_time;


        $notifications = $medicationRecordDetail->medicationNotification()->count();
        $lastNotification = $medicationRecordDetail
            ->medicationNotification()
            ->latest('scheduled_time')
            ->first();

        $lastNotificationform = $lastNotification ? Carbon::parse($lastNotification->scheduled_time) : null;


        // Si intervalo en horas se cambia actualizar hora programada para todas las notificaciones relacionadas.
        if ($medicationRecordDetail->interval_in_hours != $interval_in_hours) {


           try{
            $notificationsi = $medicationRecordDetail->medicationNotification()->get();
            foreach ($notificationsi as $index => $notification) {
                $scheduled_time = $lastNotificationform
                    ->copy()
                    ->addHours($index * $interval_in_hours)
                    ->toDateTimeString();

                $notification->update(['scheduled_time' => $scheduled_time]);
            }

           }  catch (\Throwable $th) {
                Log::error('error en interval'.$th->getMessage());
              }


        }


          // Si se cambia hora de inicio actualizar hora programada para todas las notificaciones relacionadas.
        if ($medicationRecordDetail->start_time != $start_time) {


            try{

                $start_time = Carbon::parse($start_time);

                $notificationsi = $medicationRecordDetail->medicationNotification()->get();
                foreach ($notificationsi as $index => $notification) {


                    $scheduled_time = $start_time
                        ->copy()
                        ->addHours($index * $interval_in_hours)
                        ->toDateTimeString();

                    $notification->update(['scheduled_time' => $scheduled_time]);
                }

               }  catch (\Throwable $th) {
                    Log::error('error en interval'.$th->getMessage());
                  }
        }


        //  // Validar la frecuencia al saber el nuevo valor si hay mas notificaciones eliminar las que sobran y si faltan crearlas.
        if($fc > $notifications){

            $notifications_add = $fc - $notifications;

            for ($i = 0; $i < $notifications_add; $i++) {


                $scheduled_time = $lastNotificationform->copy()->addHours($i * $request->interval_in_hours);

              try {
                $medicationRecordDetail->medicationNotification()->create([
                    'medication_record_detail_id' => $medicationRecordDetail->id,
                    'scheduled_time' => $scheduled_time,
                    'nurse_sign' => "a",
                    "active"=> 1,
                    'nurse_id' => 1,


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
          Log::error('no hubo cambios');
        }


        $medicationRecordDetail->update($request->all());

        return Redirect::route('medicationNotification.show', $medicationRecordDetail->id);










    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRecordDetail $medicationRecordDetail)
    {
        //
    }
}
