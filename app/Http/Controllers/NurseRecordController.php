<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use App\Services\FirmService;
use App\Services\TurnService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NurseRecordController extends Controller implements HasMiddleware
{

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
        $sortDirection = $request->input('sortDirection', 'asc');


        $query = NurseRecord::query()
            ->with('nurse', 'admission.patient', 'admission.bed')
            ->select('nurse_records.*')
            ->leftJoin('admissions', 'nurse_records.admission_id', '=', 'admissions.id')
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->leftJoin('beds', 'admissions.bed_id', '=', 'beds.id')
            ->join('users', 'nurse_records.nurse_id', '=', 'users.id')
            ->where('nurse_records.active', !$showDeleted);


        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->whereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(users.name, " ", COALESCE(users.last_name, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('nurse_records.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('admissions.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(beds.room, " ", beds.number) LIKE ?', ['%' . $search . '%']);;
            });
        }

        if ($admissionId) {
            $query->where('nurse_records.admission_id', intval(value: $admissionId));
        }

        if ($days) {
            $query->where('nurse_records.created_at', '>=', now()->subDays($days));
        }

        if ($sortField == 'in_process') {
            $query->orderByRaw('CASE WHEN admissions.discharged_date IS NULL THEN 0 ELSE 1 END ' . $sortDirection);
        } elseif ($sortField) {
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
            'admission_id' => intval($admissionId),
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'admission_id' => $admissionId,
                'days' => $days,
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

        return Inertia::render('NurseRecords/Create', [
            'admission_id' => intval($admission_id),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validar que en este turno no exista una hoja del mismo usuario
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);
        $admission_id = $request->admission_id;

        $nurseRecordsInTurn = NurseRecord::where('nurse_id', Auth::id())
            ->where('admission_id', $admission_id)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->first();

        $prueba = NurseRecord::find(4);
        if ($nurseRecordsInTurn) {
            dd('no puedes', $nurseRecordsInTurn, $prueba);
            return back()->with('error', 'Ya tienes un registro creado en este mismo turno');
        }

        $nurseRecord = NurseRecord::create([
            'admission_id' => $request->admission_id,
            'nurse_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return Redirect::route('nurseRecords.show', $nurseRecord->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(NurseRecord $nurseRecord)
    {
        // AGREGAR AL METODO NUEVO QUE SE AGREGUE EL INGRESO QUE TIENE EL REGISTRO POR DEFECTO

        $patient = $nurseRecord->admission->patient;
        $nurse = $nurseRecord->nurse;
        $bed = $nurseRecord->admission->bed;

        $details = NurseRecordDetail::where('nurse_record_id', operator: $nurseRecord->id)->orderBy('created_at', 'desc')
            ->where('active', true)->get();

        return Inertia::render('NurseRecords/Show', [
            'nurseRecord' => $nurseRecord,
            'patient' => $patient,
            'nurse' => $nurse,
            'bed' => $bed,
            'details' => $details,
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
        // todo: validar el turno

        $firmService = new FirmService;


        $validated = $request->validate([
            'admission_id' => 'numeric',
            'nurse_sign' => 'string',
            'active' => 'boolean',
        ]);

        if ($request->signature) {
            $fileName = $firmService
                ->createImag($request->nurse_sign, $nurseRecord->nurse_sign);
            $validated['nurse_sign'] = $fileName;
        }

        $nurseRecord->update($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NurseRecord $nurseRecord)
    {
        $nurseRecord->update(['active' => 0]);
        return Redirect::route('nurseRecords.index');
    }
}
