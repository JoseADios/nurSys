<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class AdmissionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Admission::class);

        $admissions = Admission::with(['bed', 'patient', 'doctor'])
            ->where('active', '=', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $admissions->each(function ($admission) {
            $admission->days_admitted = intval($admission->created_at->diffInDays(now()));
        });

        return Inertia::render('Admissions/Index', [
            'admissions' => $admissions,
            'can' => [
                'create' => Gate::allows('create', Admission::class),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Admission::class);

        $selectedPatient = null;
        if ($request->query('patient_id')) {
            $selectedPatient = $request->query('patient_id');
        }

        $doctors = User::all();
        $beds = Bed::all()->filter->isAvailable();
        $patients = Patient::all()->filter->isAvailable();

        return Inertia::render('Admissions/Create', [
            'doctors' => $doctors,
            'beds' => $beds,
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Admission::class);

        $request->merge(['receptionist_id' => Auth::id()]);
        $validated = $request->validate([
            'bed_id' => 'numeric|nullable',
            'patient_id' => 'required|numeric',
            'doctor_id' => 'numeric|required',
            'admission_dx' => 'required|string|max:255',
            'final_dx' => 'string|max:255|nullable',
            'comment' => 'nullable|string|max:255',
            'receptionist_id' => 'required|numeric', // Añadir esta línea
        ]);

        // validar que el paciente no tenga otra admission in_process
        $patient = Patient::find($request->patient_id);

        if (!$patient->isAvailable()) {
            return back()->with('error', 'Ya existe un ingreso en proceso para este paciente');
        }

        Admission::create($validated);
        return Redirect::route('admissions.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admission $admission)
    {
        $this->authorize('view', $admission);
        $user = User::find(Auth::id());

        $patient = $admission->patient;
        $bed = $admission->bed;
        $doctor = $admission->doctor;
        $daysIngressed = intval($admission->created_at->diffInDays(now()));

        return Inertia::render('Admissions/Show', [
            'admission' => $admission,
            'patient' => $patient,
            'bed' => $bed,
            'daysIngressed' => $daysIngressed,
            'doctor' => $doctor,
            'can' => [
                'create' => Gate::allows('create', Admission::class),
                'update' => Gate::allows('update', $admission),
                'delete' => Gate::allows('delete', $admission),
                'createOrder' => $user->hasRole(['admin']) || ($user->hasRole(['doctor']) && $admission->doctor_id == $user->id),
                'createNurseRecord' => $user->hasRole(['nurse', 'admin']),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admission $admission)
    {

        $this->authorize('update', $admission);

        $patients = Patient::all()->filter->isAvailable();
        $patients->add(Patient::find($admission->patient_id));
        $doctors = User::all();
        $beds = Bed::all()->filter->isAvailable();
        $beds->add(Bed::find($admission->bed_id));

        return Inertia::render('Admissions/Edit', [
            'admission' => $admission,
            'patients' => $patients,
            'doctors' => $doctors,
            'beds' => $beds,
            'previousUrl' => URL::previous(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Admission $admission)
    {
        $this->authorize('update', $admission);


        if ($request->has('in_process') ) {
            $validated = $request->validate([
                'in_process' => 'boolean',
            ]);

            if ($admission->in_process == false && $request->in_process) {
                $patient = Patient::find($request->patient_id);
                $bed = Bed::find($admission->bed_id);

                if (!$patient->isAvailable() || !$bed->isAvailable()) {
                    return back()->with('error', 'Ya existe otro registro de ingreso en proceso para este paciente o la cama seleccionada esta ocupada, dé el alta al otro para activar este.');
                }
            }
        } else {
            $validated = $request->validate([
                'bed_id' => 'numeric|nullable',
                'patient_id' => 'required|numeric',
                'doctor_id' => 'numeric|required',
                'admission_dx' => 'required|string|max:255',
                'final_dx' => 'string|max:255|nullable',
                'comment' => 'nullable|string|max:255',
            ]);
        }

        $admission->update($validated);
        return Redirect::route('admissions.show', $admission->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        $this->authorize('delete', $admission);

        $admission->update(['active' => 0, 'in_process' => 0]);

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

    public function restore(Admission $admission)
    {
        $this->authorize('delete', $admission);

        $admission->update(['active' => true, 'in_process' => 0]);

        // activar todas las ordenes médicas relacionadas
        DB::table('medical_orders')
            ->where('admission_id', $admission->id)
            ->update(['active' => true]);

        // medication records
        DB::table('medication_records')
            ->where('admission_id', $admission->id)
            ->update(['active' => true]);

        // temperature record
        DB::table('temperature_records')
            ->where('admission_id', $admission->id)
            ->update(['active' => true]);

        // nurse record
        DB::table('nurse_records')
            ->where('admission_id', $admission->id)
            ->update(['active' => true]);

        return Redirect::route('admissions.index');
    }
}
