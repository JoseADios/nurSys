<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Ars;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PatientController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:patient.view', only: ['index', 'show']),
            new Middleware('permission:patient.create', only: ['create', 'store']),
            new Middleware('permission:patient.update', only: [ 'edit', 'update']),
            new Middleware('permission:patient.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $showDeleted = $request->boolean('showDeleted');
        $days = $request->integer('days');
        $sortField = $request->input('sortField');
        $sortDirection = $request->input('sortDirection', 'asc');

        $query = Patient::query();

        if ($showDeleted) {
            $query->where('patients.active', false);
        } else {
            $query->where('patients.active', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(patients.first_name, ' ', patients.first_surname, ' ', patients.second_surname) like ?", ['%' . $search . '%'])
                    ->orWhere('patients.phone', 'like', '%' . $search . '%')
                    ->orWhere('patients.identification_card', 'like', '%' . $search . '%')
                    ->orWhere('patients.nationality', 'like', '%' . $search . '%');
            });
        }

        if ($days) {
            $query->where('patients.created_at', '>=', now()->subDays($days));
        }

        if ($sortField === 'is_hospitalized') {
            $patients = $query->leftJoin('admissions', 'patients.id', '=', 'admissions.patient_id')
                ->select('patients.*')
                ->groupBy('patients.id')
                ->orderByRaw('MAX(admissions.discharged_date IS NULL) ' . $sortDirection)->paginate(10);
        } elseif ($sortField) {
            $patients = $query->orderBy($sortField, $sortDirection)->paginate(10);
        } else {
            $patients = $query->orderBy('patients.updated_at', 'desc')->paginate(10);
        }

        $patients->getCollection()->transform(function ($patient) {
            $patient->is_hospitalized = !$patient->isAvailable();
            return $patient;
        });

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'days' => $days,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nationalities = Nationality::all();
        $maritalSatuses = MaritalStatus::all();
        $arss = Ars::all();

        return Inertia::render('Patients/Create', [
            'nationalities' => $nationalities,
            'maritalSatuses' => $maritalSatuses,
            'arss' => $arss,
            'previousUrl' => URL::previous(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'first_surname' => 'required|string|max:255',
            'second_surname' => 'required|string|max:255',
            'phone' => 'required|string',
            'identification_card' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'birthdate' => 'required|date',
            'position' => 'required|string|max:255',
            'marital_status' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'ars' => 'nullable|string|max:255',
        ]);

        Patient::create($validated);

        return Redirect::route('patients.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        $inProcessAdmssion = null;
        if (!$patient->isAvailable()) {
            $inProcessAdmssion = Admission::where('patient_id', $patient->id)
                ->whereNull('discharged_date')
                ->value('id');
        }

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'inProcessAdmssion' => $inProcessAdmssion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        $nationalities = Nationality::all();
        $maritalSatuses = MaritalStatus::all();
        $arss = Ars::all();

        return Inertia::render('Patients/Edit', [
            'patient' => $patient,
            'nationalities' => $nationalities,
            'maritalSatuses' => $maritalSatuses,
            'arss' => $arss,
            'previousUrl' => URL::previous(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Patient $patient)
    {
        if ($request->has('active')) {

            $validated = $request->validate([
                'active' => 'boolean|required'
            ]);

            $admissions = Admission::where('patient_id', $patient->id)
                ->where('active', false)
                ->get();

            if ($admissions->isNotEmpty()) {
                foreach ($admissions as $admission) {
                    app('App\Http\Controllers\AdmissionController')->restore($admission);
                }
            }
        } else {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'first_surname' => 'required|string|max:255',
                'second_surname' => 'required|string|max:255',
                'phone' => 'required|string',
                'identification_card' => 'required|string|max:255',
                'nationality' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'birthdate' => 'required|date',
                'position' => 'required|string|max:255',
                'marital_status' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'ars' => 'nullable|string|max:255',
            ]);
        }

        $patient->update($validated);

        return back()->with('success', 'Registro actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $patient->update(['active' => 0]);

        $admissions = Admission::where('patient_id', $patient->id)
            ->where('active', true)
            ->get();

        if ($admissions->isNotEmpty()) {
            foreach ($admissions as $admission) {
                app('App\Http\Controllers\AdmissionController')->destroy($admission);
            }
        }

        return Redirect::route('patients.index');
    }
}
