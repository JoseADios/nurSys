<?php

namespace App\Http\Controllers;

use App\Models\TemperatureDetail;
use App\Services\TurnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemperatureDetailController extends Controller
{
    protected $turnService;
    /**
     * Display a listing of the resource.
     */
    public function __construct(TurnService $turnService) {
        $this->turnService = $turnService;
    }

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
        $currentTurn = $this->turnService->getCurrentTurn();

        $lastTemperature = TemperatureDetail::where('temperature_record_id', $request->temperature_record_id)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay()->addHours($currentTurn[0]),
                Carbon::now()->startOfDay()->addHours($currentTurn[1]),
            ])
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
