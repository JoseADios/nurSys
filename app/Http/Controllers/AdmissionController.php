<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $admissions = Admission::with('bed', 'patient')->get();
        return Inertia::render('Admissions/Index', [
            'admissions' => $admissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admissions/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required',
            'recepcionist_id' => 'required',
        ]);

        Admission::create($request->all());
        return Redirect::route('admissions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admission $admission)
    {
        //
        return Inertia::render('Admissions/Show', [
            'admission' => $admission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admission $admission)
    {
        return Inertia::render('Admissions/Edit', [
            'admission' => $admission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admission $admission)
    {
        $request->validate([
            'patient_id' => 'required',
            'recepcionist_id' => 'required',
        ]);

        $admission->update($request->all());
        return Redirect::route('admissions.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        //
    }
}
