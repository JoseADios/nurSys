<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\Regime;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MedicalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicalOrders = MedicalOrder::where('active', true)
            ->with('admission.patient', 'admission.bed', 'doctor')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc')->get();

        return Inertia::render('MedicalOrders/Index', [
            'medicalOrders' => $medicalOrders,
        ]);
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
    public function show(MedicalOrder $medicalOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalOrder $medicalOrder)
    {
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();
        $patient = $medicalOrder->admission->patient;
        $bed = $medicalOrder->admission->bed;
        $doctor = $medicalOrder->admission->doctor;
        $regimes = Regime::all();
        $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)
            ->where('active', true)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')->get();

        return Inertia::render('MedicalOrders/Edit', [
            'medicalOrder' => $medicalOrder,
            'details' => $details,
            'admissions' => $admissions,
            'patient' => $patient,
            'bed' => $bed,
            'doctor' => $doctor,
            'regimes' => $regimes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalOrder $medicalOrder)
    {
        $medicalOrder->update($request->all());

        return back()->with('succes', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrder $medicalOrder)
    {
        //
    }
}
