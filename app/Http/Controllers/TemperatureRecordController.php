<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Services\FirmService;
use App\Services\TurnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;

class TemperatureRecordController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $showDeleted = $request->boolean('showDeleted');
        $admissionId = $request->integer('admission_id');
        $days = $request->integer('days');

        $query = TemperatureRecord::query()
            ->with([
                'admission.patient',
                'admission.bed',
                'nurse'
            ])
            ->select('temperature_records.*')
            ->join('admissions', 'temperature_records.admission_id', '=', 'admissions.id')
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->join('beds', 'admissions.bed_id', '=', 'beds.id')
            ->join('users', 'temperature_records.nurse_id', '=', 'users.id')
            ->where('temperature_records.active', !$showDeleted)
            ->latest('temperature_records.updated_at')
            ->latest('temperature_records.created_at');

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->whereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(users.name, " ", COALESCE(users.last_name, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(beds.room, "-", beds.number) LIKE ?', ['%' . $search . '%']);
            });
        }

        if ($admissionId) {
            $query->where('temperature_records.admission_id', $admissionId);
        }

        if ($days) {
            $query->where('temperature_records.created_at', '>=', now()->subDays($days));
        }

        return Inertia::render('TemperatureRecords/Index', [
            'temperatureRecords' => $query->paginate(10),
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'admission_id' => $admissionId,
                'days' => $days,
            ]
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
        $existingRecord = TemperatureRecord::where('admission_id', $request->admission_id)
            ->where('active', 1)
            ->first();
        if ($existingRecord) {
            return Redirect::back()->withErrors(['admission_id' => 'A temperature record for this admission already exists.']);
        }

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
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        if ($admission_id) {
            $temperatureRecord = TemperatureRecord::where('admission_id', $admission_id)
                ->where('active', 1)
                ->first();
        } else {
            $temperatureRecord = TemperatureRecord::find($id);
        }

        if (!$temperatureRecord) {
            return Redirect::route('temperatureRecords.create', [
                'admission_id' => $admission_id
            ]);
        }

        $canCreateDetail = true;

        $lastTemperature = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->orderBy('created_at', 'desc')
            ->first();


        if ($lastTemperature) {
            $canCreateDetail = false;

            if ($lastTemperature->nurse_id != Auth::id()) {
                $lastTemperature = null;
            }
        }

        $temperatureRecord->load(['admission.bed', 'admission.patient', 'nurse']);
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();
        $details = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->with(['nurse' => function ($query) {
                $query->select('id', 'name', 'last_name');
            }])
            ->orderBy('created_at', 'asc')
            ->get(['temperature', 'evacuations', 'urinations', 'nurse_id', 'created_at']);

        return Inertia::render('TemperatureRecords/Show', [
            'temperatureRecord' => $temperatureRecord,
            'admissions' => $admissions,
            'details' => $details,
            'lastTemperature' => $lastTemperature,
            'canCreateDetail' => $canCreateDetail,
            'previousUrl' => URL::previous(),
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
        $firmService = new FirmService;
        $validated = $request->validate([
            'admission_id' => 'numeric',
            'impression_diagnosis' => 'string',
            'nurse_sign' => 'string',
            'active' => 'boolean'
        ]);

        if ($request->signature) {
            $fileName = $firmService
                ->createImag($request->nurse_sign, $temperatureRecord->nurse_sign);
            $validated['nurse_sign'] = $fileName;
        }

        $temperatureRecord->update($validated);


        return back()->with('succes', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureRecord $temperatureRecord)
    {
        $temperatureRecord->update(['active' => 0]);
        return Redirect::route('temperatureRecords.index');
    }
}
