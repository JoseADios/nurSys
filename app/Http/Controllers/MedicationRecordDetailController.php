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
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MedicationRecordDetailController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicationRecordDetail.view', only: ['index', 'show']),
            new Middleware('permission:medicationRecordDetail.create', only: ['store']),
            new Middleware('permission:medicationRecordDetail.update', only: ['update']),
            new Middleware('permission:medicationRecordDetail.delete', only: ['destroy']),
        ];
    }   /**
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
        $medicationRecord = MedicationRecord::findOrFail($request->medication_record_id);
        $this->authorize('create', [MedicationRecordDetail::class, $medicationRecord]);



        $request->validate([
            'medication_record_id' => 'required|exists:medication_records,id',
            'drug' => 'required|string',
            'dose' => 'required|numeric|gt:0',
            'dose_metric' => 'required|string',
            'route' => 'required|string',
            'fc' => 'required|integer|gt:0',
            'start_time' => 'required',
        ], [
            'dose.gt' => 'La dosis debe ser mayor a 0.',
            'fc.gt' => 'La frecuencia (fc) debe ser mayor a 0.',
        ]);




        $start_time_24 = Carbon::parse($request->start_time);

        $detail = MedicationRecordDetail::create([
            'medication_record_id' => $request->medication_record_id,
            'drug' => $request->drug,
            'dose' => $request->dose,
            'dose_metric' => $request->dose_metric,
            'saline_solution' => $request->saline_solution,
            'route' => $request->route,
            'fc' => $request->fc,
            'interval_in_hours' => $request->interval_in_hours,
            'nebulization_time' => $request->nebulization_time,
            'start_time' => $start_time_24,
            'active' => true,
            'created_at' => now(),
            'nebulized' => $request->nebulized,
            'medical_order_detail_id' => $request->selectedOrderId,
        ]);

        // Obtén el valor de fc
        $fc = $request->fc;
        $start_time = Carbon::parse($request->start_time);

        $interval_in_hours = $request->interval_in_hours;
        $nebulization_time = $request->nebulization_time;

        for ($i = 0; $i < $fc; $i++) {
            $scheduled_time = $start_time->copy()->addHours($i * $interval_in_hours)->addMinutes($i * $nebulization_time);

            MedicationNotification::create([
                'medication_record_detail_id' => $detail->id, // Usa el ID del detalle
                'scheduled_time' => $scheduled_time,
                "active" => 1,
            ]);
        }

        return back()->with('flash.toast', 'Detalle de Ficha de Medicamento creada correctamente');
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
    public function edit(MedicationRecordDetail $medicationRecordDetail,Request $request)
    {

        // Verificar si Ya Existe una notifiacion con medicamentos administrados
        $existingnotification = MedicationNotification::where('medication_record_detail_id', $medicationRecordDetail->id)->first();
        $dose = DrugDose::all();
        $route = DrugRoute::all();
   $admission_id = $request->integer('admission_id');

        $Applied = $existingnotification->applied;
        if ($Applied == 1) {

            return back()->with('flash.toast', 'Ya Existe una notifiacion con medicamentos administrados.')->with('flash.toastStyle', 'danger');

        }
        if ($medicationRecordDetail->active == 0) {
            return back()->with('flash.toast', 'Ya Existe una notifiacion con medicamentos administrados.')->with('flash.toastStyle', 'danger');

        }
        return Inertia::render('MedicationRecordDetail/Edit', [
            'medicationRecordDetail' => $medicationRecordDetail,
            'dose' => $dose,
            'routes' => $route,
            'admission_id' => $admission_id,
        ]);


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicationRecordDetail $medicationRecordDetail)
    {

        $this->authorize('update', $medicationRecordDetail);
        if ($request->has('active')) {
            $medicationRecordDetail->update($request->all());

            $notifications = $medicationRecordDetail->medicationNotification;

            foreach ($notifications as $notification) {
                $notification->update(['active' => $request->active]);
            }

            return back()->with('flash.toast', 'Detalle Ficha de Medicamento actualizado correctamente');
        } else if ($request->has('suspended_at')) {

            if ($request->suspended_at) {
                $this->restore($medicationRecordDetail->id);
            } else {
                $this->suspend($medicationRecordDetail->id);

            }
        } else {


            $request->validate([

                'fc' => 'required|integer|gt:0',
                'interval_in_hours' => 'required|integer|gt:0',
                'dose' => 'required|integer|gt:0',
            ]);

            $fc = $request->fc;
            $interval_in_hours = $request->interval_in_hours;
            $nebulization_time = $request->nebulization_time;
            $start_time = $request->start_time;

            if ($start_time) {
                $start_time = Carbon::createFromFormat('H:i', $start_time)->toDateTimeString();
                $request->merge(['start_time' => $start_time]); // Update the request with the formatted datetime
            }

            $notifications = $medicationRecordDetail->medicationNotification()->count();
            $lastNotification = $medicationRecordDetail
                ->medicationNotification()
                ->latest('scheduled_time')
                ->first();

            $lastNotificationform = $lastNotification ? Carbon::parse($lastNotification->scheduled_time) : null;

            // Validar la frecuencia al saber el nuevo valor si hay mas notificaciones eliminar las que sobran y si faltan crearlas.
            if ($fc > $notifications) {

                $notifications_add = $fc - $notifications;

                for ($i = 1; $i < $notifications_add; $i++) {
                    $scheduled_time = $lastNotificationform->copy()->addHours($i * $request->interval_in_hours)->addMinutes($i * $request->nebulization_time);
                    try {
                        $medicationRecordDetail->medicationNotification()->create([
                            'medication_record_detail_id' => $medicationRecordDetail->id,
                            'scheduled_time' => $scheduled_time,

                            "active" => 1,
                            "nurse_id" => Auth::id(),
                        ]);
                    } catch (\Throwable $th) {
                        Log::error('error en crear' . $th->getMessage());
                    }
                }

            } elseif ($fc < $notifications) {
                try {
                    $notifications_delete = $notifications - $fc;
                    $medicationRecordDetail->medicationNotification()->latest()->take($notifications_delete)->get()->each(function ($notification) {
                        $notification->delete();
                    });
                } catch (\Throwable $th) {
                    Log::error('error en eliminar' . $th->getMessage());
                }

            } else {
                Log::error('no hubo cambios delete/create');
            }

            // Si intervalo en horas se cambia actualizar hora programada para todas las notificaciones relacionadas.
            if ($medicationRecordDetail->interval_in_hours != $interval_in_hours || $medicationRecordDetail->nebulization_time != $nebulization_time || $medicationRecordDetail->start_time != $start_time) {
                try {
                    $notificationsi = $medicationRecordDetail->medicationNotification()->get();

                    if ($medicationRecordDetail->interval_in_hours != $interval_in_hours || $medicationRecordDetail->nebulization_time != $interval_in_hours && $medicationRecordDetail->start_time == $start_time) {
                        // Actualizar solo en base al intervalo en horas
                        $formatted_start_time = Carbon::parse($start_time);
                        foreach ($notificationsi as $index => $notification) {
                            $scheduled_time = $formatted_start_time
                                ->copy()
                                ->addHours($index * $interval_in_hours)
                                ->addMinutes($index * $nebulization_time)
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
                                ->addMinutes($index * $nebulization_time)
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
                                 ->addMinutes($index * $nebulization_time)
                                ->toDateTimeString();

                            $notification->update(['scheduled_time' => $scheduled_time]);
                        }
                    }
                } catch (\Throwable $th) {
                    Log::error('Error al actualizar notificaciones: ' . $th->getMessage());
                }
            } else {
                Log::error('no hubo cambios');
            }

            $medicationRecordDetail->update($request->all());
            return Redirect::route('medicationRecords.show', [
            'medicationRecord' => $medicationRecordDetail->medication_record_id,
            'admission_id'     => $medicationRecordDetail->medicationRecord->admission_id,
        ])->with('flash.toast', 'Detalle Ficha de Medicamento actualizado correctamente');
        }
    }

    private function restore($id)
    {
        $medicationRecordDetail = MedicationRecordDetail::findOrFail($id);
        $medicationRecordDetail->update(['suspended_at' => null]);
        return Redirect::route('medicationRecords.show', $medicationRecordDetail->medication_record_id)->with('flash.toast', 'Detalle Ficha de Medicamento restaurada correctamente');
    }

    private function suspend($id)
    {
        $medicationRecordDetail = MedicationRecordDetail::findOrFail($id);

        $this->authorize('suspend', $medicationRecordDetail);

        $medicationRecordDetail->update(['suspended_at' => now()]);
        return Redirect::route('medicationRecords.show', $medicationRecordDetail->medication_record_id)->with('flash.toast', 'Detalle Ficha de Medicamento suspendida correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRecordDetail $medicationRecordDetail,Request $request)
    {
        $this->authorize('delete', $medicationRecordDetail);
  $admission_id = $request->input('admission_id');
        $hasNotifications = MedicationNotification::where('medication_record_detail_id', $medicationRecordDetail->id)->where('applied', 1)->get();
        if (!Auth::user()->hasRole('admin')) {
            if ($hasNotifications->isNotEmpty()) {
                return back()->with('flash.toast', 'No se puede eliminar este Detalle de Ficha de Medicamentos porque tiene notificaciones aplicadas.')->with('flash.toastStyle', 'danger');
            }
        }


        $medicationNotifications = $medicationRecordDetail->medicationNotification()->get();
        foreach ($medicationNotifications as $notification) {
            $notification->update(['active' => 0]);


        }

        $medicationRecordDetail->update(['active' => 0]);
       return Redirect::route('medicationRecords.show', [
            'medicationRecord' => $medicationRecordDetail->medication_record_id,
            'admission_id'     => $admission_id,
        ])->with('flash.toast', 'Detalle Ficha de Medicamento eliminada correctamente');
    }
}
