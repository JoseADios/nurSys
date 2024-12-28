<?php

namespace App\Http\Controllers;

use App\Models\TemperatureRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemperatureRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TemperatureRecord::with( 'admission.patient', 'admission.bed')
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc');

        if ($request->has('admission_id')) {
            $query->where('admission_id', $request->admission_id);
        }

        $temperatureRecords = $query->get();

        return Inertia::render('TemperatureRecords/Index', [
            'temperatureRecords' => $temperatureRecords,
            'admission_id' => intval($request->admission_id),
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
    public function show(TemperatureRecord $temperatureRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemperatureRecord $temperatureRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TemperatureRecord $temperatureRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureRecord $temperatureRecord)
    {
        //
    }
}
