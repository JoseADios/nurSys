<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use App\Services\FirmService;
use Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NurseRecordController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:nurseRecord.view', only: ['index', 'show']),
            new Middleware('permission:nurseRecord.create', only: ['edit', 'store']),
            new Middleware('permission:nurseRecord.update', only: ['update']),
            new Middleware('permission:nurseRecord.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $showDeleted = $request->boolean('showDeleted');
        $admissionId = $request->integer('admission_id');
        $days = $request->integer('days');
        $sortField = $request->input('sortField');
        $in_process = $request->input('in_process', 'true');
        $sortDirection = $request->input('sortDirection', 'asc');


        $query = NurseRecord::query()
            ->with('nurse', 'admission.patient', 'admission.bed')
            ->select('nurse_records.*')
            ->leftJoin('admissions', 'nurse_records.admission_id', '=', 'admissions.id')
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->leftJoin('beds', 'admissions.bed_id', '=', 'beds.id')
            ->join('users', 'nurse_records.nurse_id', '=', 'users.id')
            ->where('nurse_records.active', !$showDeleted);

        if ($in_process === 'true') {
            $query->whereNull('admissions.discharged_date');
        } elseif ($in_process === 'false') {
            $query->whereNotNull('admissions.discharged_date');
        }

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->whereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(users.name, " ", COALESCE(users.last_name, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('nurse_records.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('admissions.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(beds.room, " ", beds.number) LIKE ?', ['%' . $search . '%']);
                ;
            });
        }

        if ($admissionId) {
            $query->where('nurse_records.admission_id', intval(value: $admissionId));
        }

        if ($days) {
            $query->where('nurse_records.created_at', '>=', now()->subDays($days));
        }

        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('nurse_records.created_at');
        }

        $nurseRecords = $query->paginate(10);

        $nurseRecords->getCollection()->transform(function ($record) {
            if ($record->admission->discharged_date != null) {
                $record->in_process = false;
            } else {
                $record->in_process = true;
            }
            return $record;
        });

        return Inertia::render('NurseRecords/Index', [
            'nurseRecords' => $nurseRecords,
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'admission_id' => $admissionId,
                'days' => $days,
                'in_process' => $in_process,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ]
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admission_id = $request->has('admission_id') ? $request->admission_id : null;

        if ($admission_id) {
            $admission = Admission::find($admission_id);
            $response = Gate::inspect('create', [NurseRecord::class, $admission]);

            if (!$response->allowed()) {
                return back()->with('flash.toast', 'No se pueden crear registros en un ingreso dado de alta');
            }
        }

        return Inertia::render('NurseRecords/Create', [
            'admission_id' => intval($admission_id),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $admission = Admission::find($request->admission_id);
        $has_admission_id = $request->input('has_admission_id');

        $this->authorize('create', [NurseRecord::class, $admission]);
        $this->authorize('canCreateInTurn', [NurseRecord::class, $request->admission_id]);

        $nurseRecord = NurseRecord::create([
            'admission_id' => $request->admission_id,
            'nurse_id' => Auth::id(),
            'created_at' => now(),
        ]);

        if ($has_admission_id) {
            return Redirect::route(
                'nurseRecords.show',
                [
                    'nurseRecord' => $nurseRecord->id,
                    'admission_id' => $admission->id,
                ]
            )->with('flash.toast', 'Registro de enfermería creado');
        } else {
            return Redirect::route('nurseRecords.show', $nurseRecord->id)->with('flash.toast', 'Registro de enfermería creado');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(NurseRecord $nurseRecord, Request $request)
    {
        $this->authorize('view', $nurseRecord);

        $showDeleted = $request->boolean('showDeleted');
        $admission_id = $request->integer('admission_id');
        $patient = $nurseRecord->admission->patient;
        $nurse = $nurseRecord->nurse;
        $bed = $nurseRecord->admission->bed;

        $response = Gate::inspect('update', $nurseRecord);
        $canUpdateRecord = $response->allowed();

        $queryDetails = NurseRecordDetail::query()
            ->where('nurse_record_id', operator: $nurseRecord->id)
            ->orderBy('created_at', 'desc');

        if ($showDeleted) {
            $queryDetails->where('active', false);
        } else {
            $queryDetails->where('active', true);
        }

        $details = $queryDetails->get();

        return Inertia::render('NurseRecords/Show', [
            'nurseRecord' => $nurseRecord,
            'patient' => $patient,
            'nurse' => $nurse,
            'bed' => $bed,
            'admission_id' => $admission_id,
            'canUpdateRecord' => $canUpdateRecord,
            'details' => $details,
            'showDeleted' => $showDeleted,
            'errors' => !empty($errors) ? $errors : [],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NurseRecord $nurseRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NurseRecord $nurseRecord)
    {
        $this->authorize('update', $nurseRecord);
        $this->authorize('canCreateInTurn', [NurseRecord::class, $request->admission_id]);

        $firmService = new FirmService;

        $validated = $request->validate([
            'admission_id' => 'numeric|nullable',
            'nurse_id' => 'numeric|nullable',
            'nurse_sign' => 'string|nullable',
            'active' => 'boolean',
        ]);

        if ($request->signature) {
            $fileName = $firmService
                ->createImag($request->nurse_sign, $nurseRecord->nurse_sign);
            $validated['nurse_sign'] = $fileName;
        }

        $nurseRecord->update($validated);

        return back()->with('flash.toast', 'Registro actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NurseRecord $nurseRecord)
    {
        $this->authorize('delete', $nurseRecord);

        $nurseRecord->update(['active' => 0]);
        return back()->with('flash.toast', 'Registro de enfermería eliminado');
    }
}
