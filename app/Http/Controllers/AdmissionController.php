<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\MedicalOrder;
use App\Models\NurseRecord;
use App\Models\Patient;
use App\Models\TemperatureRecord;
use App\Models\User;
use App\Models\MedicationRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Services\FirmService;
use Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdmissionController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:admission.view', only: ['index', 'show']),
            new Middleware('permission:admission.create', only: ['store']),
            new Middleware('permission:admission.update', only: ['update']),
            new Middleware('permission:admission.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('showDeleted');
        $search = $request->input('search');
        $sortField = $request->input('sortField');
        $sortDirection = $request->input('sortDirection', 'asc');
        $beds_available = $request->input('beds_available');
        $days = $request->integer('days');

        $query = Admission::query()->with('patient', 'bed', 'doctor')->select([
            'admissions.id',
            'admissions.patient_id',
            'admissions.bed_id',
            'admissions.doctor_id',
            'admissions.created_at',
            'admissions.discharged_date',
            'admissions.active'
        ])
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->leftJoin('beds', 'admissions.bed_id', '=', 'beds.id')
            ->join('users', 'admissions.doctor_id', '=', 'users.id')
            ->where('admissions.active', !$showDeleted);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->whereRaw('DATE(admissions.created_at) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(users.name, " ", COALESCE(users.last_name, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(beds.room, " ", beds.number) LIKE ?', ['%' . $search . '%']);
            });
        }

        if ($beds_available === '1') {
            $query->whereNotNull('admissions.bed_id');

        } elseif ($beds_available === '2') {
            $query->whereNull('admissions.bed_id');

        }

        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('admissions.updated_at')
                ->latest('admissions.created_at');
        }
        if ($days) {
            $query->where('admissions.created_at', '>=', now()->subDays($days));
        }

        $admissions = $query->paginate(10);

        $admissions->getCollection()->transform(function ($admission) {
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
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
                'beds_available' => $beds_available,
            ],
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

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
        $bed = null;
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

        $admission = Admission::create($validated);
        if ($bed) {
            $bed->update(['status' => 'occupied']);
        }
        return Redirect::route('admissions.show', $admission->id)->with('flash.toast', 'Ingreso registrado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Admission $admission)
    {
        $user = Auth::user();
        $admission->load('receptionist');

        $patient = $admission->patient;
        $bed = $admission->bed;
        $doctor = $admission->doctor;
        $daysIngressed = intval($admission->created_at->diffInDays(now()));

        $temperatureRecordId = TemperatureRecord::where('admission_id', $admission->id)
            ->where('active', true)->first('id');
        $medicationRecord = MedicationRecord::where('admission_id', $admission->id)->first();

        $createOrder = Gate::allows('create', [MedicalOrder::class, $admission->id]);
        $createNurseRecord = Gate::allows('create', [NurseRecord::class, $admission]);

        return Inertia::render('Admissions/Show', [
            'admission' => $admission,
            'medicationRecord' => $medicationRecord,
            'patient' => $patient,
            'bed' => $bed,
            'daysIngressed' => $daysIngressed,
            'doctor' => $doctor,
            'temperatureRecordId' => $temperatureRecordId,
            'can' => [
                // 'create' => Gate::allows('create', Admission::class),
                'update' => Gate::allows('update', $admission),
                'delete' => Gate::allows('delete', $admission),
                'createOrder' => $createOrder,
                'createNurseRecord' => $createNurseRecord,
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

        $newBedToUpdate = null;
        $anteriorBedToUpdate = null;

        // si se esta dando de alta
        if ($request->has('discharge') && $request->discharge == true) {
            $firmService = new FirmService;
            $validated = $request->validate([
                'discharged_date' => 'required|string',
                'doctor_sign' => 'string|required',
                'final_dx' => 'string|required',
            ]);

            $validated['discharged_date'] = now();

            $fileName = $firmService->createImag($request->doctor_sign, $admission->doctor_sign);
            $validated['doctor_sign'] = $fileName;

            $newBedToUpdate = Bed::find($admission->bed_id);
            if ($newBedToUpdate) {
                $newBedToUpdate->status = 'cleaning';
            }

            // actualizacion normal
        } else {
            $validated = $request->validate([
                'bed_id' => 'numeric|nullable',
                'patient_id' => 'required|numeric',
                'doctor_id' => 'numeric|required',
                'admission_dx' => 'required|string|max:255',
                'discharged_date' => 'nullable|string|max:255',
                'doctor_sign' => 'nullable|string|max:255',
                'final_dx' => 'string|max:255|nullable',
                'comment' => 'nullable|string|max:255',
            ]);

            if ($validated['patient_id']) {
                $patient = Patient::find($request->patient_id);

                if ($patient->id !== $admission->patient_id && !$patient->isAvailable()) {
                    return back()->with('flash.toast', 'Ya existe otro registro de ingreso en proceso para este paciente')->with('flash.toastStyle', 'danger');
                }
            }

            if ($validated['bed_id']) {
                $newBedToUpdate = Bed::find($validated['bed_id']);

                // tenia otra, se cambia
                if ($admission->bed_id && $newBedToUpdate->id !== $admission->bed_id && $newBedToUpdate) {
                    if (!$newBedToUpdate->isAvailable()) {
                        return back()->with('flash.toast', 'La cama seleccionada no está disponible')->with('flash.toastStyle', 'danger');
                    }
                    // poner la anterior en limpieza y la nueva ocupada
                    $newBedToUpdate->status= 'occupied';
                    $anteriorBedToUpdate = Bed::findOrFail($admission->bed_id);
                    $anteriorBedToUpdate->status= 'cleaning';

                    // si no tenia cama asignada
                } elseif ($admission->bed_id == null) {
                    $newBedToUpdate->status= 'occupied';
                }

            // si viene vacio
            } else {
                // si tenia una
                if ($admission->bed_id !== null) {
                    $newBedToUpdate = Bed::find($admission->bed_id);
                    $newBedToUpdate->status= 'cleaning';
                }
            }

            // si quiere poner el ingreso en progreso
            if ($request->has('discharged_date') && $request->discharged_date == null && $admission->discharged_date != null) {
                // eliminar la img de la firma
                if ($admission->doctor_sign && Storage::disk('public')->exists($admission->doctor_sign)) {
                    Storage::disk('public')->delete($admission->doctor_sign);
                }
            }
        }

         // si se esta cambiando la
         if (Auth::user()->hasRole('nurse')) {
            if ($request->has('bed_id') && $request->bed_id !== $admission->bed_id) {
                $this->authorize('setBed', $admission);
            }
        }

        $admission->update($validated);
        if ($newBedToUpdate) {
            $newBedToUpdate->save();
        }
        if ($anteriorBedToUpdate) {
            $anteriorBedToUpdate->save();
        }

        return back()->with('flash.toast', 'Ingreso actualizado exitosamente.');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admission $admission)
    {
        $this->authorize('delete', $admission);

        $admission->update(['active' => 0, 'discharged_date' => now()]);
        $bed = Bed::find($admission->bed_id);
        if ($bed) {

            $bed->status = 'cleaning';
            $bed->save();
        }

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

        return back()->with('flash.toast', 'Ingreso eliminado correctamente');
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

        return back()->with('flash.toast', 'Ingreso restaurado correctamente');
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
        $doesntHaveMedicationR = $request->boolean('doesntHaveMedicationR');
        $doesntHaveMedicalOrder = $request->boolean('doesntHaveMedicalOrder');
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
        if ($doesntHaveMedicationR === true) {
            $query->whereDoesntHave('MedicationRecord', function (Builder $query) {
                $query->where('active', true);
            });
        }
        if ($doesntHaveMedicalOrder === true) {
            $query->whereDoesntHave('medicalOrders', function (Builder $query) {
                $query->where('active', true);
            });
        }

        $admissions = $query->paginate(15);

        if ($selectedAdm) {
            $admissionsCollection = $admissions->getCollection();

            if (!$admissionsCollection->contains('id', $selectedAdm->id)) {
            $admissionsCollection->prepend($selectedAdm);
            }

            $admissionsCollection = $admissionsCollection->sortByDesc(function ($admission) use ($selectedAdm) {
            return $admission->id === $selectedAdm->id ? PHP_INT_MAX : $admission->id;
            })->values();

            $admissions->setCollection($admissionsCollection);
        }

        return response()->json($admissions);
    }
}
