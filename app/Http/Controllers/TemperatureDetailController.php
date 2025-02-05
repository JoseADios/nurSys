<?php

namespace App\Http\Controllers;

use App\Models\TemperatureDetail;
use App\Services\TurnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TemperatureDetailController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:temperatureDetail.create', only: ['store']),
            new Middleware('permission:temperatureDetail.update', only: ['update']),
        ];
    }

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
        $turnService = new TurnService();
        $dateRange = $turnService->getDateRangeForTurn($turnService->getCurrentTurn());

        $lastTemperature = TemperatureDetail::where('temperature_record_id', $request->temperature_record_id)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->orderBy('created_at', 'asc')
            ->first();

        if ($lastTemperature) {
            return back()->with('error', 'Ya hay una temperatura creada en el mismo turno');
        }

        TemperatureDetail::create([
            'temperature_record_id' => $request->temperature_record_id,
            'temperature' => $request->temperature,
            'evacuations' => $request->evacuations,
            'urinations' => $request->urinations,
            'created_at' => now(),
            'nurse_id' => Auth::id(),
        ]);

        return back()->with('succes', 'Temperatura agregada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(TemperatureDetail $temperatureDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemperatureDetail $temperatureDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TemperatureDetail $temperatureDetail)
    {
        $request->validate([
            'temperature' => 'required|numeric',
            'evacuations' => 'required|integer',
            'urinations' => 'required|string',
        ]);
        $temperatureDetail->update($request->all());

        return back()->with('succes', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureDetail $temperatureDetail)
    {
        //
    }
}
