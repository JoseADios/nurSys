<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TemperatureRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = TemperatureRecord::with( 'admission.patient', 'admission.bed', 'nurse')
        ->orderBy('updated_at', 'desc')
        ->orderBy('created_at', 'desc');

        $temperatureRecords = $query->get();

        return Inertia::render('TemperatureRecords/Index', [
            'temperatureRecords' => $temperatureRecords,
            'admission_id' => intval($request->admission_id),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admission_id = $request->has('admission_id') ? $request->admission_id : null;

        return Inertia::render('TemperatureRecords/Create', [
            'admission_id' => $admission_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $temperatureRecord = TemperatureRecord::create([
            'admission_id' => $request->admission_id,
            'nurse_id' => Auth::id(),
            'impression_diagnosis' => $request->impression_diagnosis,
        ]);

        return Redirect::route('temperatureRecords.show', $temperatureRecord->id);
    }

    /**
     * Display the specified resource.
     */
    public function show($id, $admission_id = null)
    {
        if ($admission_id) {
            $temperatureRecord = TemperatureRecord::where('admission_id', $admission_id)->first();
        } else {
            $temperatureRecord = TemperatureRecord::find($id);
        }

        if (!$temperatureRecord) {
            return Redirect::route('temperatureRecords.create', [
                'admission_id' => $admission_id
            ]);
        }

        $temperatureRecord->load(['admission.bed', 'admission.patient', 'nurse']);
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();
        $details = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)->get();

        return Inertia::render('TemperatureRecords/Show', [
            'temperatureRecord' => $temperatureRecord,
            'admissions' => $admissions,
            'details' => $details,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TemperatureRecord $temperatureRecord)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TemperatureRecord $temperatureRecord)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureRecord $temperatureRecord)
    {
        //
    }
}
