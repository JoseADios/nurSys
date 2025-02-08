<?php

namespace App\Http\Controllers;

use App\Models\MedicalOrderDetail;
use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
class MedicalOrderDetailController extends Controller
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MedicalOrderDetail::create([
            'medical_order_id' => $request->medical_order_id,
            'order' =>  $request->order,
            'regime' => $request->regime,
            'created_at' => now()
        ]);


        return back()->with('success', 'Detalle agregado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalOrderDetail $medicalOrderDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalOrderDetail $medicalOrderDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalOrderDetail $medicalOrderDetail)
    {
       $medicalOrderDetail->update($request->all());
        $medicationRecordDetail = MedicationRecordDetail::where('medical_order_detail_id', $medicalOrderDetail->id)->first();

        if ($medicationRecordDetail) {
            $medicationRecordDetail->update(['active' => 0]);
            Log::info("MedicationRecordDetail actualizado: ", ['id' => $medicationRecordDetail->id, 'active' => 0]);
        } else {
            Log::warning("No se encontró un MedicationRecordDetail para MedicalOrderDetail ID: " . $medicalOrderDetail->id);
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrderDetail $medicalOrderDetail)
    {
        $medicalOrderDetail->update(['active' => 0]);
        return Redirect::route('medicalOrders.edit', $medicalOrderDetail->medical_order_id);
    }
}
