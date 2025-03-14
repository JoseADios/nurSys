<?php

namespace App\Http\Controllers;

use App\Models\Drug;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class DrugController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Drugs = Drug::all();
        return Inertia::render('Drugs/Index', [
            'Drugs' => $Drugs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Drugs/Create');
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

            // Verificar si ya existe un Medicamento con ese nombre
            $existingdrug = Drug::where('name', $request->name)->first();

            if ($existingdrug) {

                return redirect()->back()->withErrors([
                    'name' => 'Ya Existe un Medicamento con ese nombre.',
                ])->withInput();
            }


            $Drug = Drug::create([
                'name' => $request->name,
                'description' => $request->description,

            ]);


            return redirect()->back()->with('success', 'Medicamento creado correctamente');

    }
    public function filterDrugs(Request $request)
    {
        $query = Drug::query();

        if ($request->filled('filters.name')) {
            $query->where('name', 'LIKE', '%' . $request->input('filters.name') . '%');
        }

        $drugs = $query->get();
        $paginatedDrugs = new \Illuminate\Pagination\LengthAwarePaginator(
            $drugs->forPage($request->page, 10),
            $drugs->count(),
            10,
            $request->page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginatedDrugs;
    }


    /**
     * Display the specified resource.
     */
    public function show(Drug $drug)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $drug = Drug::find($id);
        return Inertia::render('Drugs/Edit', [
            'Drugs' => $drug,
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

        $drug = Drug::find($id);
        $drug->update($validated);


        return redirect()->route('Drugs.index')->with('success', 'Medicamento Actualizado correctamente');

       } catch (\Throwable $th) {
        Log::error($th);
        return redirect()->route('Drugs.index')->with('error', 'Error al Actualizar el medicamento');

       }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drug $drug)
    {
        //
    }
}
