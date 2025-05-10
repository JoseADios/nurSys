<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Ars;
use App\Models\MaritalStatus;
use App\Models\Nationality;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Validator;

class PatientController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:patient.view', only: ['index', 'show']),
            new Middleware('permission:patient.create', only: ['create', 'store']),
            new Middleware('permission:patient.update', only: ['edit', 'update']),
            new Middleware('permission:patient.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('showDeleted');
        $days = $request->integer('days');
        $search = $request->input('search');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $identificationCard = $request->input('identificationCard');
        $nationality = $request->input('nationality');
        $email = $request->input('email');
        $hospitalized = $request->input('hospitalized');
        $sortField = $request->input('sortField');
        $sortDirection = $request->input('sortDirection', 'asc');

        $query = Patient::query()
            ->leftJoin('admissions', 'patients.id', '=', 'admissions.patient_id')
            ->select('patients.*', \DB::raw('COALESCE(COUNT(CASE WHEN admissions.active = TRUE AND admissions.discharged_date IS NULL THEN 1 END), 0) AS hospitalized'))
            ->groupBy('patients.id', 'admissions.active');

        if ($showDeleted) {
            $query->where('patients.active', false);
        } else {
            $query->where('patients.active', true);
        }

        if ($name) {
            $query->whereRaw("CONCAT(patients.first_name, ' ', patients.first_surname, ' ', patients.second_surname) like ?", ['%' . $name . '%']);
        }
        if ($phone) {
            $query->whereLike('patients.phone', '%' . $phone . '%');
        }
        if ($identificationCard) {
            $query->whereLike('patients.identification_card', '%' . $identificationCard . '%');
        }
        if ($nationality) {
            $query->whereLike('patients.nationality', '%' . $nationality . '%');
        }
        if ($email) {
            $query->whereLike('patients.email', '%' . $email . '%');
        }

        if ($days) {
            $query->where('patients.created_at', '>=', now()->subDays($days));
        }
        if ($hospitalized === 'true') {
            $query->havingRaw('hospitalized > 0');
        } elseif ($hospitalized === 'false') {
            $query->havingRaw('hospitalized = 0');
        }

        if ($sortField === 'is_hospitalized') {
            $query->orderByRaw('hospitalized ' . $sortDirection);
        } elseif ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('patients.updated_at', 'desc');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(patients.first_name, ' ', patients.first_surname, ' ', patients.second_surname) like ?", ['%' . $search . '%'])
                    ->orWhere('patients.phone', 'like', '%' . $search . '%')
                    ->orWhere('patients.identification_card', 'like', '%' . $search . '%')
                    ->orWhere('patients.nationality', 'like', '%' . $search . '%');
            });
        }
        ;

        $patients = $query->paginate(10);

        $patients->getCollection()->transform(function ($patient) {
            $patient->is_hospitalized = $patient->hospitalized > 0;
            return $patient;
        });

        return Inertia::render('Patients/Index', [
            'patients' => $patients,
            'filters' => [
                'search' => $search,
                'showDeleted' => $showDeleted,
                'days' => $days,
                'name' => $name,
                'phone' => $phone,
                'identificationCard' => $identificationCard,
                'nationality' => $nationality,
                'email' => $email,
                'hospitalized' => $hospitalized,
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
            'second_surname' => 'nullable|string|max:255',
            'phone' => 'required|string|size:14',
            'nationality' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:patients',
            'birthdate' => 'required|date',
            'position' => 'nullable|string|max:255',
            'marital_status' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'ars' => 'nullable|string|max:255',
        ]);

        Validator::make($request->all(), [
            'identification_card' => 'nullable|string|unique:patients|size:12',
        ])->sometimes('identification_card', 'required|string|unique:patients|size:12', function ($input) {
            // Verificar si el usuario es mayor de edad
            return Carbon::parse($input->birthdate)->age >= 18;
        })->validate();

        $patient = Patient::create($validated);

        return Redirect::route('patients.show', $patient->id)->with('flash.toast', 'Paciente creado exitosamente');
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

        // obtener los ultimos 5 ingresos
        $admissions = Admission::where('patient_id', $patient->id)
        ->with('doctor', 'bed')
        ->orderByDesc('created_at')
        ->get();

        // dd($admissions);

        return Inertia::render('Patients/Show', [
            'patient' => $patient,
            'admissions' => $admissions,
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
                'second_surname' => 'nullable|string|max:255',
                'phone' => 'required|string|size:14',
                'identification_card' => 'nullable|string|size:12|unique:patients,identification_card,' . $patient->id,
                'nationality' => 'required|string|max:255',
                'email' => 'nullable|email|max:255|unique:patients,email,' . $patient->id,
                'birthdate' => 'required|date',
                'position' => 'nullable|string|max:255',
                'marital_status' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'ars' => 'nullable|string|max:255',
            ]);

            Validator::make($request->all(), [
                'identification_card' => 'nullable|string|unique:patients,identification_card,' . $patient->id,
            ])->sometimes('identification_card', 'required|string|unique:patients,identification_card,' . $patient->id, function ($input) {
                return Carbon::parse($input->birthdate)->age >= 18;
            })->validate();
        }

        $patient->update($validated);

        return back()->with('flash.toast', 'Paciente actualizado exitosamente');
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

        return back()->with('flash.toast', 'Paciente desactivado exitosamente');
    }

    public function filterPatients(Request $request)
    {
        $query = Patient::query();

        if ($request->filled('filters.name')) {
            $query->whereRaw(
                "CONCAT(first_name, ' ', first_surname, ' ', COALESCE(second_surname, '')) LIKE ?",
                ['%' . $request->input('filters.name') . '%']
            );
        }

        $patients = $query->get()->filter->isAvailable();
        $paginatedPatients = new \Illuminate\Pagination\LengthAwarePaginator(
            $patients->forPage($request->page, 10),
            $patients->count(),
            10,
            $request->page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $paginatedPatients;
    }
}
