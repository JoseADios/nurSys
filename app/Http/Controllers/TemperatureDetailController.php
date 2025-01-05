<?php

namespace App\Http\Controllers;

use App\Models\TemperatureDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TemperatureDetailController extends Controller
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
        // validar que en un mismo turno no se ingresen dos temperaturas

        // obtener la ultima temperatura por fecha de creacion

        // $lastTemperature = TemperatureDetail::where('temperature_record_id', $request->temperature_record_id)
        //     ->orderBy('created_at', 'desc')->first();

        // if (Carbon::parse($lastTemperature->created_at)->diffInHours(Carbon::parse(now())) > 5) {
        //     return back()->with('error', 'Ya hay una temperatura creada en el mismo turno');
        // }

        // modificar para ver turnos por hora y no diferencia entre fechas de creacion de los registros

        /*
        1. tener las horas de los turnos 24-8, 8-16, 16-24
        2. calcular el turno actual
        3. hacer una consulta que me devuelva el registro que se encuentre con el created_at dentro del turno actual
        4. Si existe uno no permitir crear otro y permitir la actualizacion del mismo
        5. So no existe ninguno permitir la creacion
        */

        $turns = [
            '24-8' => [24, 8],
            '8-16' => [8, 16],
            '16-24' => [16, 24],
        ];

        $currentHour = Carbon::now()->hour;

        $currentTurn = null;

        foreach ($turns as $key => $turn) {
            if ($currentHour >= $turn[0] and $currentHour < $turn[1]) {
                $currentTurn = $key;
                break;
            }
        }

        $lastTemperature = TemperatureDetail::where('temperature_record_id', $request->temperature_record_id)
            ->whereBetween('created_at', [
                Carbon::now()->startOfDay()->addHours($turns[$currentTurn][0]),
                Carbon::now()->startOfDay()->addHours($turns[$currentTurn][1]),
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
