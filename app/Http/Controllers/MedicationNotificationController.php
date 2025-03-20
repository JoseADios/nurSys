<?php

namespace App\Http\Controllers;

use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class MedicationNotificationController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicationNotification.view', only: ['index', 'show']),
            new Middleware('permission:medicationNotification.create', only: ['edit', 'store']),
            new Middleware('permission:medicationNotification.update', only: ['update']),
            new Middleware('permission:medicationNotification.delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
{
    $MedicationRecordDetail = MedicationRecordDetail::find($id);
    $MedicationNotificacion = MedicationNotification::where('medication_record_detail_id', $id)->get();

    if ($MedicationRecordDetail->active == 0) {
        return redirect()->route('medicationRecords.show', $MedicationRecordDetail->medication_record_id)->withErrors([
            'medication_record_detail_id' => 'Ya Existe una notifiacion con medicamentos administrados.',
        ])->withInput();
     }
    return Inertia::render('MedicationNotification/show', [
        'details' => $MedicationRecordDetail,
        'notifications' => $MedicationNotificacion,
    ]);
}



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicationNotification $medication_notification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request)
    {
        $medication_notification = MedicationNotification::find($id);
        $detail = MedicationRecordDetail::find($medication_notification->medication_record_detail_id);

        if ($request->has('markAsAdministered')) {

            if ($detail->active == 1 && $detail->suspended_at == null) {
                $medication_notification->update([
                    'nurse_id' => Auth::id(),
                    'administered_time' => now(),
                    'applied' => true
                ]);

                return redirect()->back()->with('toast.flash.success', 'Medicamento administrado correctamente.');
            }


            //dd('entro al if',$medication_notification);

        } elseif($request->has('revert')){
            if ($detail->active == 1 && $detail->suspended_at == null) {
                $medication_notification->update([
                    'nurse_id' => Auth::id(),
                    'administered_time' =>now(),
                    'applied'=>false
                ]);
                return redirect()->back()->with('toast.flash.success', 'Medicamento administrado correctamente.');

                }
            }
        else {
            $medication_notification->update($request->all());
            return redirect()->back()->with('toast.flash.success', 'Medicamento administrado correctamente.');

        }


    }


        //Validar el caso de que el request solo tenga el campo applied y que sea true, actualizar la hora de administered_time y el nurse_id con el usuario authenticado.
        //else update normal.



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationNotification $medication_notification)
    {

        $medication_notification->update(['active' => 0]);

        return Redirect::route('medicationNotification.show', $medication_notification);
    }
}
