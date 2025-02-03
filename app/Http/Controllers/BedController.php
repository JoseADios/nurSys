<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $beds = Bed::with(['admission' => function ($query) {
            $query->whereNull('discharged_date');
        }])
            ->orderBy('room', 'asc')
            ->orderBy('number', 'asc')
            ->get()
            ->map(function ($bed) {
                $bed->admission_id = optional($bed->admission)->id;
                return $bed;
            });

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
            'out_of_service' => 'required|boolean'
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
