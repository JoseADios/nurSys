<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use App\Services\FirmService;
use App\Services\TurnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class NurseRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $admissionId = $request->input('admission_id');

        $query = NurseRecord::with('nurse', 'admission.patient')
            ->where('active', true)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('created_at', 'like', '%' . $search . '%')
                    ->orWhereHas('admission.patient', function ($patientQuery) use ($search) {
                        $patientQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('first_surname', 'like', '%' . $search . '%')
                            ->orWhere('second_surname', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('nurse', function ($nurseQuery) use ($search) {
                        $nurseQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    });
            });
        }

        if (!empty($admissionId)) {
            $query->where('admission_id', intval($admissionId));
        }

        $nurseRecords = $query->paginate(10);

        return Inertia::render('NurseRecords/Index', [
            'nurseRecords' => $nurseRecords,
            'admission_id' => intval($admissionId),
            'filters' => ['search' => $search],
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admission_id = $request->has('admission_id') ? $request->admission_id : null;
        $name = $request->name;
        $room = $request->room;
        $bed = $request->bed;

        $admQuery = Admission::query()
            ->where('admissions.active', true)
            ->whereNull('admissions.discharged_date')
            ->with('patient', 'bed')
            ->leftJoin('patients', 'admissions.patient_id', '=', 'patients.id')
            ->leftJoin('beds', 'admissions.bed_id', '=', 'beds.id')
            ->select(
                'admissions.id',
                'admissions.bed_id',
                'admissions.patient_id',
                'admissions.created_at',
                'patients.first_name',
                'patients.first_surname',
                'patients.second_surname',
                'beds.id',
                'beds.number',
                'beds.room',
                'beds.floor',
            );

        if ($name) {
            $admQuery->whereRaw("CONCAT(patients.first_name, ' ', patients.first_surname, ' ', patients.second_surname) like ?", ['%' . $name . '%']);
        }

        if ($room) {
            $admQuery->whereLike('beds.room', '%' . $room . '%');
        }

        if ($bed) {
            $admQuery->whereLike('beds.number', '%' . $bed . '%');
        }

        $admissions = $admQuery->get();

        // dump($admissions);

        return Inertia::render('NurseRecords/Create', [
            'admissions' => $admissions,
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

        $nurseRecordsInTurn = NurseRecord::where('nurse_id', Auth::id())
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->first();


        if ($nurseRecordsInTurn) {
            return back()->with('error', 'Ya tienes un registro creado en este mismo turno');
        }

        $nurseRecord = NurseRecord::create([
            'admission_id' => $request->admission_id,
            'nurse_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return Redirect::route('nurseRecords.edit', $nurseRecord->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(NurseRecord $nurseRecord)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NurseRecord $nurseRecord)
    {
        $patient = $nurseRecord->admission->patient;
        $nurse = $nurseRecord->nurse;
        $bed = $nurseRecord->admission->bed;
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();
        $details = NurseRecordDetail::where('nurse_record_id', operator: $nurseRecord->id)->orderBy('created_at', 'desc')
            ->where('active', true)->get();

        return Inertia::render('NurseRecords/Edit', [
            'nurseRecord' => $nurseRecord,
            'admissions' => $admissions,
            'patient' => $patient,
            'nurse' => $nurse,
            'bed' => $bed,
            'details' => $details,
            'errors' => !empty($errors) ? $errors : [],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NurseRecord $nurseRecord)
    {
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
