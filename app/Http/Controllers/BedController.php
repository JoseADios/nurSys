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
            new Middleware('permission:bed.view', only: ['index']),
            new Middleware('permission:bed.update', only: ['update'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beds = Bed::with('admission.patient')
            ->with('admission.doctor')
            ->with([
                'admission' => function ($query) {
                    $query->active();
                }
            ])
            ->orderBy('room', 'asc')
            ->orderBy('number', 'asc')
            ->get();

        return Inertia::render('Beds/Index', [
            'beds' => $beds,
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
    public function show(Bed $bed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bed $bed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bed $bed)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:available,cleaning,out_of_service',
        ]);

        $bed->update($validated);

        return back()->with('success', 'El registro fue actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bed $bed)
    {
        //
    }
}
