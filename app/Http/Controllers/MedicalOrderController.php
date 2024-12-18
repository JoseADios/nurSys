<?php

namespace App\Http\Controllers;

use App\Models\MedicalOrder;
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
            ->with('admission.patient', 'admission.bed')
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalOrder $medicalOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrder $medicalOrder)
    {
        //
    }
}
