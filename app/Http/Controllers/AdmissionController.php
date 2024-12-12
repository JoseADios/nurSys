<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $doctors = User::all();
        $beds = Bed::all();
        $patients = Patient::all();

        return Inertia::render('Admissions/Create', [
            'doctors' => $doctors,
            'beds' => $beds,
            'patients' => $patients,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'patient_id' => 'required',
        //     'recepcionist_id' => 'required',
        // ]);

        $recepcionist_id = Auth::id();

        Admission::create(
            [
                'bed_id' => $request->bed_id,
                'patient_id' => $request->patient_id,
                'recepcionist_id' => $recepcionist_id,
                'doctor_id' => $request->doctor_id,
                'admission_dx' => $request->admission_dx,
                'final_dx' => $request->final_dx,
                'comment' => $request->comment,
                'created_at' => now(),
            ]
        );
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
