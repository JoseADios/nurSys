<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class NurseRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = NurseRecord::with('nurse', 'admission.patient')
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc');

        if ($request->has('admission_id')) {
            $query->where('admission_id', $request->admission_id);
        }

        $nurseRecords = $query->get();

        return Inertia::render('NurseRecords/Index', [
            'nurseRecords' => $nurseRecords,
            'admission_id' => intval($request->admission_id),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admission_id = $request->has('admission_id') ? $request->admission_id : null;

        $admissions = Admission::where('active', true)
            ->with('patient', 'bed')
            ->get();
        return Inertia::render('NurseRecords/Create', [
            'admissions' => $admissions,
            'admission_id' => $admission_id
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        $details = NurseRecordDetail::where('nurse_record_id', operator: $nurseRecord->id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('NurseRecords/Edit', [
            'nurseRecord' => $nurseRecord,
            'admissions' => $admissions,
            'patient' => $patient,
            'nurse' => $nurse,
            'bed' => $bed,
            'details' => $details,
            'errors' => !empty($errors) ? $errors : []
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NurseRecord $nurseRecord)
    {
        $nurseRecord->update($request->all());

        return Redirect::route('nurseRecords.edit', [
            'nurseRecord' => $nurseRecord->id,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NurseRecord $nurseRecord)
    {
        //
    }
}
