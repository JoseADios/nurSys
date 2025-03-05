<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Services\FirmService;
use App\Services\TurnService;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TemperatureRecordController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:temperatureRecord.view', only: ['index', 'show']),
            new Middleware('permission:temperatureRecord.create', only: ['show', 'store']),
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
        $in_process = $request->input('in_process', 'true');
        $sortField = $request->input('sortField');
        $sortDirection = $request->input('sortDirection', 'asc');

        $query = TemperatureRecord::query()
            ->with([
                'admission.patient',
                'admission.bed',
                'nurse'
            ])
            ->select('temperature_records.*')
            ->leftJoin('admissions', 'temperature_records.admission_id', '=', 'admissions.id')
            ->leftJoin('patients', 'admissions.patient_id', '=', 'patients.id')
            ->leftJoin('beds', 'admissions.bed_id', '=', 'beds.id')
            ->leftJoin('users', 'temperature_records.nurse_id', '=', 'users.id')
            ->where('temperature_records.active', !$showDeleted);


        if ($in_process === 'true') {
            $query->whereNull('admissions.discharged_date');
        } elseif ($in_process === 'false') {
            $query->whereNotNull('admissions.discharged_date');
        }

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query->whereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(users.name, " ", COALESCE(users.last_name, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('temperature_records.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('admissions.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(beds.room, " ", beds.number) LIKE ?', ['%' . $search . '%']);
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
            $response = Gate::inspect('create', [TemperatureRecord::class, $admission]);

            if (!$response->allowed()) {
                return back()->with('error', $response->message());
            }
        }
        return Inertia::render('TemperatureRecords/Create', [
            'admission_id' => $admission_id,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $admission = Admission::find($request->admission_id);

        $this->authorize('create', [TemperatureRecord::class, $admission]);

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
    public function show(TemperatureRecord $temperatureRecord, Request $request)
    {
        $admission_id = $request->query('admission_id');

        $temperatureRecord->load(['admission.bed', 'admission.patient', 'nurse']);

        // verificar si puede crear detalles
        $responseCreateDetail = Gate::inspect('create', [TemperatureDetail::class, $temperatureRecord->id]);
        $canCreateDetail = $responseCreateDetail->allowed();

        // ultimo detalle de temperatura
        $lastTemperature = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->orderBy('created_at', 'asc')
            ->first();

        // verificar si puede actualizar detalles
        $responseUpdateDetail = Gate::inspect('update', [TemperatureDetail::class, $lastTemperature]);

        if (!$responseUpdateDetail->allowed()) {
            $lastTemperature = null;
        }

        if ($lastTemperature !== null) {
            $canCreateDetail = false;
        }

        // verificar si puede editar la firma del registro
        $responseUpdateRecord = Gate::inspect('updateSignature', $temperatureRecord);
        $canUpdateSignature = $responseUpdateRecord->allowed();

        $details = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->with(['nurse:id,name,last_name']) // Especificar solo las columnas necesarias
            ->orderBy('updated_at', 'asc')
            ->get(['temperature', 'evacuations', 'urinations', 'nurse_id', 'updated_at']);

        return Inertia::render('TemperatureRecords/Show', [
            'temperatureRecord' => $temperatureRecord,
            'details' => $details,
            'admission_id' => $admission_id,
            'lastTemperature' => $lastTemperature,
            'canCreateDetail' => $canCreateDetail,
            'canUpdateSignature' => $canUpdateSignature,
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
        $this->authorize('update', $temperatureRecord);

        $firmService = new FirmService;

        $validated = $request->validate([
            'admission_id' => 'numeric|nullable',
            'impression_diagnosis' => 'string|nullable',
            'nurse_sign' => 'string|nullable',
            'active' => 'boolean|nullable'
        ]);

        // si se modifica el ingreso
        if ($request->has('admission_id') && $request->admission_id !== $temperatureRecord->admission_id) {
            $this->authorize('updateAdmission', [TemperatureRecord::class, $request->admission_id]);
        }

        // si se modifica la firma
        if ($request->has('nurse_sign') && $request->nurse_sign !== $temperatureRecord->nurse_sign) {
            $this->authorize('updateSignature', $temperatureRecord);

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
        $this->authorize('delete', $temperatureRecord);
        $temperatureRecord->update(['active' => 0]);
        return Redirect::route('temperatureRecords.index');
    }
}
