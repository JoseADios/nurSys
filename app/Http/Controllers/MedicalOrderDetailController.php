<?php

namespace App\Http\Controllers;

use App\Models\MedicalOrderDetail;
use Illuminate\Http\Request;

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
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrderDetail $medicalOrderDetail)
    {
        //
    }
}
