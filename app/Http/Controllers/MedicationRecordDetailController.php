<?php

namespace App\Http\Controllers;

use App\Models\MedicationRecord;
use App\Models\MedicationRecordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
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
    public function createdetail($medicationRecordId)
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
        MedicationRecordDetail::create([
            'medication_record_id' => $request->medication_record_id,
            'drug' =>  $request->drug,
            'dose' => $request->dose,
            'route'=> $request->route,
            'fc'=> $request->fc,
            'interval_in_hours'=> $request->interval_in_hours,
            'active' => true,
            'created_at' => now()
        ]);

       return redirect()->route('medicationRecords.show',$request->medication_record_id)->with('success', 'Detalle agregado exitosamente');
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
        $medicationRecordDetail->update($request->all());
        return Redirect::route('medicationRecordDetails.edit', $medicationRecordDetail->medication_record_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRecordDetail $medicationRecordDetail)
    {
        //
    }
}
