<?php

namespace App\Http\Controllers;

use App\Models\EliminationRecord;
use App\Models\TemperatureDetail;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class EliminationRecordController extends Controller
{
    use AuthorizesRequests;

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
        $this->authorize('create', [TemperatureDetail::class, $request->temperature_record_id]);

        EliminationRecord::create([
            'temperature_record_id' => $request->temperature_record_id,
            'nurse_id' => Auth::id(),
            'evacuations' => $request->evacuations,
            'urinations' => $request->urinations,
            'created_at' => now(),
        ]);

        return back()->with('flash.toast', 'Registros agregados exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EliminationRecord $eliminationRecord)
    {
        $request->validate([
            'evacuations' => 'required|integer',
            'urinations' => 'required|string',
        ]);

        $eliminationRecord->update($request->all());

        return back()->with('flash.toast', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
