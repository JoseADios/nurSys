<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class AdmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admissions = Admission::with(['bed', 'patient', 'doctor'])
            ->where('active', '=', 1)
            ->orderBy('created_at', 'desc')
            ->get();

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
        $admissions = Admission::where('in_process', 1);
        $bedsFilled = $admissions->pluck('bed_id')->toArray();
        $patientsAct = $admissions->pluck('patient_id')->toArray();

        $beds = Bed::whereNotIn('id', $bedsFilled)->get();

        $patients = Patient::whereNotIn('id', $patientsAct)->get();

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
        $request->validate([
            'patient_id' => 'required',
            'admission_dx' => 'required',
        ]);

        // show errors
        if ($request->has('errors')) {
            return back()->withErrors($request->get('errors'));
        }

        // validar que no exista, patient, in_process
        $admissionExist = Admission::where('patient_id', $request->patient_id)
            ->where('in_process', 1)->get();

        if (!$admissionExist->isEmpty()) {
            dd($admissionExist);
            return back()->with('error', 'Ya existe un ingreso en proceso para este paciente');
        }

        Admission::create(
            [
                'bed_id' => $request->bed_id,
                'patient_id' => $request->patient_id,
                'recepcionist_id' => Auth::id(),
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
        $patient = $admission->patient;
        $bed = $admission->bed;
        $doctor = $admission->doctor;

        return Inertia::render('Admissions/Show', [
            'admission' => $admission,
            'patient' => $patient,
            'bed' => $bed,
            'doctor' => $doctor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admission $admission)
    {
        $patients = Patient::all();
        $doctors = User::all();
        $bedsFilled = Admission::where('in_process', 1)->pluck('bed_id');
        $beds = Bed::whereNotIn('id', $bedsFilled)->get();

        return Inertia::render('Admissions/Edit', [
            'admission' => $admission,
            'patients' => $patients,
            'doctors' => $doctors,
            'beds' => $beds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admission $admission)
    {
        $request->validate([
            'patient_id' => 'required',
        ]);

        if ($request->has('errors')) {
            return back()->withErrors($request->get('errors'));
        }

        if ($request->in_process) {
            // validar que no exista otro patient con el process active
            $admissionExist = Admission::where('patient_id', $request->patient_id)
                ->where('in_process', 1)->get();

            if ($admissionExist) {
                return back()->with('error', 'Ya existe otro registro de ingreso en proceso para otro paciente, de el alta al otro para activar este.');
            }
        }

        $admission->update($request->all());
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        $admission->update(['active' => 0]);

        // desactivar todas las ordenes médicas relacionadas
        DB::table('medical_orders')
            ->where('admission_id', $admission->id)
            ->update(['active' => 0]);

        // medication records
        DB::table('medication_records')
            ->where('admission_id', $admission->id)
            ->update(['active' => 0]);

        // temperature record
        DB::table('temperature_records')
            ->where('admission_id', $admission->id)
            ->update(['active' => 0]);

        // nurse record
        DB::table('nurse_records')
            ->where('admission_id', $admission->id)
            ->update(['active' => 0]);

        return Redirect::route('admissions.index');
    }
}
