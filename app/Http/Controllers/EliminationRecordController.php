<?php

namespace App\Http\Controllers;

use App\Models\EliminationRecord;
use App\Models\TemperatureDetail;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EliminationRecordController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

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
        $this->authorize('create', [EliminationRecord::class, $request->temperature_record_id]);

        $validated = $request->validate([
            'temperature_record_id' => 'required|numeric',
            'evacuations' => 'required|max:99|min:0|numeric',
            'urinations' => 'required|max_digits:2'
        ]);

        $validated['nurse_id'] = Auth::id();
        $validated['created_at'] = now();

        EliminationRecord::create($validated);

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
        $this->authorize('update', $eliminationRecord);

        $validated = $request->validate([
            'evacuations' => 'required|max:99|min:0|numeric',
            'urinations' => 'required|max_digits:2',
        ]);

        $eliminationRecord->update($validated);

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
