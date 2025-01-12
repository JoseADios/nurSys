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
class MedicationNotificationController extends Controller
{
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
        if ($request->has('markAsAdministered')) {
            $medication_notification->update([
                'nurse_id'=> Auth::id(),
                'administered_time'=> now(),
                'applied'=> true
                ]
            );

            //dd('entro al if',$medication_notification);

        }
        else {
            $medication_notification->update($request->all());
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
