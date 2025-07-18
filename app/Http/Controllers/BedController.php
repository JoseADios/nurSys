<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class BedController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:bed.update', only: ['update']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beds = Bed::with([
            'admission' => function ($query) {
                $query->active()->with(['patient', 'doctor']);
            }
        ])
            ->orderBy('room', 'asc')
            ->orderBy('number', 'asc')
            ->get();

        // Calcular el recuento de camas por estado
        $bedStatusCounts = [
            'available' => $beds->where('status', 'available')->whereNull('admission')->count(),
            'occupied' => $beds->whereNotNull('admission')->count(),
            'cleaning' => $beds->where('status', 'cleaning')->count(),
            'out_of_service' => $beds->where('status', 'out_of_service')->count(),
        ];

        return Inertia::render('Beds/Index', [
            'beds' => $beds,
            'bedStatusCounts' => $bedStatusCounts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return abort(404);

    }

    /**
     * Display the specified resource.
     */
    public function show(Bed $bed)
    {
        return abort(404);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bed $bed)
    {
        return abort(404);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:available,cleaning,out_of_service,occupied',
        ]);

        $bed->update([
            'status' => $validated['status']
        ]);

        return back()->with('flash.toast', 'El registro fue actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bed $bed)
    {
        return abort(404);

    }
}
