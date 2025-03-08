<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Patient;
use App\Models\TemperatureRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
    public function index(Request $request)
    {
        $this->authorize('viewAny', Admission::class);

        $search = $request->input('search');
        $query = Admission::query()
            ->with(['bed', 'patient', 'doctor'])
            ->where('active', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('created_at', 'like', '%' . $search . '%')

                    ->orWhereHas('patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('first_surname', 'like', '%' . $search . '%')
                            ->orWhere('second_surname', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('bed', function ($bedQuery) use ($search) {
                        $bedQuery->where('number', 'like', '%' . $search . '%')
                            ->orWhere('room', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                        $doctorQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    });
            });
        }
        $admissions = $query->select(['id', 'patient_id', 'bed_id', 'doctor_id', 'created_at', 'discharged_date', 'active'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->through(function ($admission) {
                if ($admission->discharged_date) {
                    $admission->days_admitted = intval($admission->created_at->diffInDays($admission->discharged_date));
                } else {
                    $admission->days_admitted = intval($admission->created_at->diffInDays(now()));
                }
                return $admission;
            });

        return Inertia::render('Admissions/Index', [
            'admissions' => $admissions,
            'can' => [
                'create' => Gate::allows('create', Admission::class),
            ],
            'filters' => ['search' => $search],
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
            $selectedPatient = $request->integer('patient_id');
        }

        $selectedbed = null;
        if ($request->query('bed_id')) {
            $selectedbed = $request->integer('bed_id');
        }

        $beds = Bed::all()->filter->isAvailable()->values()->toArray();
        $patients = Patient::all()->filter->isAvailable();

        return Inertia::render('Admissions/Create', [
            'beds' => $beds,
            'patients' => $patients,
            'selectedPatient' => $selectedPatient,
            'selectedbed' => $selectedbed,
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
        if ($request->bed_id) {
            $bed = Bed::find($request->bed_id);
            if (!$bed->isAvailable()) {
                return back()->with('error', 'La cama seleccionada no está disponible');
            }
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
        $admission->load('receptionist');

        $patient = $admission->patient;
        $bed = $admission->bed;
        $doctor = $admission->doctor;
        $daysIngressed = intval($admission->created_at->diffInDays(now()));
        $temperatureRecordId = TemperatureRecord::where('admission_id', $admission->id)
            ->where('active', true)->first('id');


        return Inertia::render('Admissions/Show', [
            'admission' => $admission,
            'patient' => $patient,
            'bed' => $bed,
            'daysIngressed' => $daysIngressed,
            'doctor' => $doctor,
            'temperatureRecordId' => $temperatureRecordId,
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
        $beds = Bed::all()->filter->isAvailable()->values();
        $selectedBed = Bed::find($admission->bed_id);

        if ($selectedBed) {
            $beds->add($selectedBed);
        }

        $beds->toArray();

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

        if ($request->has('discharged_date')) {
            $validated = $request->validate([
                'discharged_date' => 'nullable|string',
            ]);

            if ($request->discharged_date) {
                $validated['discharged_date'] = now();
            } else {
                $validated['discharged_date'] = null;
            }

            $bed = Bed::find($admission->bed_id);

            if ($admission->discharged_date != null && $request->discharged_date == null) {
                $patient = Patient::find($request->patient_id);

                if (!$patient->isAvailable() || !$bed->isAvailable()) {
                    return back()->with('error', 'Ya existe otro registro de ingreso en proceso para este paciente o la cama seleccionada esta ocupada, dé el alta al otro para activar este.');
                }
            } elseif ($admission->discharged_date == null && $request->discharged_date != null) {
                $bed->status = 'cleaning';
                $bed->save();
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

        if ($request->bed_id && ($request->bed_id !== $admission->bed_id)) {
            $bed = Bed::find($request->bed_id);
            if (!$bed->isAvailable()) {
                return back()->with('error', 'La cama seleccionada no está disponible');
            }
        }

        $admission->update($validated);
        return Redirect::route('admissions.show', $admission->id)->banner('Ingreso actualizado exitosamente.');
        ;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        $this->authorize('delete', $admission);

        $admission->update(['active' => 0, 'discharged_date' => now()]);

        $bed = Bed::find($admission->bed_id);
        $bed->status = 'cleaning';
        $bed->save();

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

        $admission->update(['active' => true]);

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


    /**
     * function to filter admissions
     * @param  $request
     * @return \Illuminate\Database\Eloquent\Collection<int, Admission>
     */
    public function getFilteredAdmissions(Request $request)
    {
        $selectedAdm = null;
        $admission_id = $request->input('admission_id');
        $doesntHaveTemperatureR = $request->boolean('doesntHaveTemperatureR');
        $filters = $request->input('filters', []);

        $query = Admission::query()
            ->with('patient', 'bed')
            ->active()
            ->filterByName($filters['name'] ?? null)
            ->filterByRoom($filters['room'] ?? null)
            ->filterByBed($filters['bed'] ?? null)
        ;

        if ($admission_id) {
            $selectedAdm = Admission::with('patient', 'bed')->find($admission_id);
        }

        if ($doesntHaveTemperatureR === true) {
            $query->whereDoesntHave('TemperatureRecord', function (Builder $query) {
                $query->where('active', true);
            });
        }

        $admissions = $query->paginate(15);

        if ($selectedAdm && !$admissions->contains('id', $selectedAdm->id)) {
            $admissionsCollection = $admissions->getCollection();
            $admissionsCollection->add($selectedAdm);
            $sortedAdmissions = $admissionsCollection->sortByDesc('id')->values();
            $admissions->setCollection($sortedAdmissions);
        }

        return response()->json($admissions);
    }
}
