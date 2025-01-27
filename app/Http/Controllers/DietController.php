<?php

namespace App\Http\Controllers;

use App\Models\Diet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DietController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Diet = Diet::all();
        return Inertia::render('Diet/Index', [
            'Diet' => $Diet,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Diet/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
           // Validación de los datos de entrada
           $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        // Verificar si ya existe una Dieta con ese nombre
        $existingdiet = Diet::where('name', $request->name)->first();

        if ($existingdiet) {

            return redirect()->back()->withErrors([
                'name' => 'Ya Existe una Dieta con ese nombre.',
            ])->withInput();
        }


        $Diet = Diet::create([
            'name' => $request->name,
            'description' => $request->description,

        ]);


        return redirect()->route('medicationRecords.create')->with('success', 'Medicamento creado correctamente');


    }


    /**
     * Display the specified resource.
     */
    public function show(Diet $diet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $diet = Diet::find($id);
        return Inertia::render('Diet/Edit', [
            'Diet' => $diet,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated =  $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
            ]);

            $diet = Diet::find($id);
            $diet->update($validated);


            return redirect()->route('Diet.index')->with('success', 'Dieta Actualizado correctamente');

           } catch (\Throwable $th) {
            Log::error($th);
            return redirect()->route('Diet.index')->with('error', 'Error al Actualizar la dieta');

           }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Diet $diet)
    {
        //
    }
}
