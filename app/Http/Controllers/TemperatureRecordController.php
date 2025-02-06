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
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class TemperatureRecordController extends Controller implements HasMiddleware
{
    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:temperatureRecord.view', only: ['index', 'show']),
            new Middleware('permission:temperatureRecord.create', only: ['edit', 'store']),
            new Middleware('permission:temperatureRecord.update', only: ['update']),
            new Middleware('permission:temperatureRecord.delete', only: ['destroy']),
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

        $query = TemperatureRecord::query()
            ->with([
                'admission.patient',
                'admission.bed',
                'nurse'
            ])
            ->select('temperature_records.*')
            ->join('admissions', 'temperature_records.admission_id', '=', 'admissions.id')
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->leftJoin('beds', 'admissions.bed_id', '=', 'beds.id')
            ->join('users', 'temperature_records.nurse_id', '=', 'users.id')
            ->where('temperature_records.active', !$showDeleted);

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

        if ($sortField == 'in_process') {
            $query->orderByRaw('CASE WHEN admissions.discharged_date IS NULL THEN 0 ELSE 1 END ' . $sortDirection);
        } elseif ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('temperature_records.updated_at')
                ->latest('temperature_records.created_at');
        }

        $temperatureRecords = $query->paginate(10);

        $temperatureRecords->getCollection()->transform(function ($record) {
            if ($record->admission->discharged_date != null) {
                $record->in_process = false;
            } else {
                $record->in_process = true;
            }
            return $record;
        });

        return Inertia::render('TemperatureRecords/Index', [
            'temperatureRecords' => $temperatureRecords,
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
        $dateRange = $turnService->getDateRangeForTurn($turnService->getCurrentTurn());

        $temperatureRecord = $admission_id
            ? TemperatureRecord::where('admission_id', $admission_id)->where('active', 1)->first()
            : TemperatureRecord::find($id);

        if (!$temperatureRecord) {
            return Redirect::route('temperatureRecords.create', ['admission_id' => $admission_id]);
        }

        $temperatureRecord->load(['admission.bed', 'admission.patient', 'nurse']);

        $lastTemperature = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->orderBy('created_at', 'desc')
            ->first();

        $canCreateDetail = !$lastTemperature;

        if ($lastTemperature && $lastTemperature->nurse_id != Auth::id()) {
            $lastTemperature = null;
        }

        $allTemperatureRecords = TemperatureRecord::where('active', true)
            ->whereNot('admission_id', $temperatureRecord->admission_id)
            ->pluck('admission_id');

        $admissions = Admission::where('active', true)
            ->with('patient', 'bed')
            ->whereNotIn('id', $allTemperatureRecords)
            ->get();

        $details = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->with(['nurse:id,name,last_name']) // Especificar solo las columnas necesarias
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
            'admission_id' => 'numeric|nullable',
            'impression_diagnosis' => 'string|nullable',
            'nurse_sign' => 'string|nullable',
            'active' => 'boolean|nullable'
        ]);

        // si encuentra uno relacionado al ingreso nuevo o al ingreso actual
        if ($request->has('admission_id')) {
            $admission = Admission::find($request->admission_id);
        } else {
            $admission = $temperatureRecord->admission;
        }

        $tempRecordAdm = TemperatureRecord::where('admission_id', $admission->id)
            ->whereNot('id', $temperatureRecord->id)
            ->where('active', true)->get();

        if (!$tempRecordAdm->isEmpty()) {
            return back()->withErrors('error', 'El ingreso al que quiere asignar este registro ya posee una hoja de temperatura');
        }

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
