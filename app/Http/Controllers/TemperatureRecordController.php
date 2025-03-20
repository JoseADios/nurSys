<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\EliminationRecord;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Services\FirmService;
use App\Services\TurnService;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
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
            new Middleware('permission:temperatureRecord.create', only: ['store']),
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

        $admission_id = $request->has('admission_id') ? $request->integer('admission_id') : null;

        if ($admission_id) {
            $admission = Admission::find($admission_id);
            $response = Gate::inspect('create', [TemperatureRecord::class, $admission]);

            if (!$response->allowed()) {
                return back()->with('flash.toast', $response->message())->with('flash.toastStyle', 'danger');
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
        $admission_id = $request->input('admission_id');

        if ($admission_id) {
            $this->authorize('create', [TemperatureRecord::class, $admission_id]);
        }

        dd('no hice nada');

        $temperatureRecord = TemperatureRecord::create([
            'admission_id' => $admission_id,
            'nurse_id' => Auth::id(),
        ]);

        if ($admission_id) {
            return Redirect::route(
                'temperatureRecords.show',
                [
                    'temperatureRecord' => $temperatureRecord->id,
                    'admission_id' => $admission_id
                ]
            )->with('flash.toast', 'Registro de temperatura creado correctamente');

        } else {
            return Redirect::route('temperatureRecords.show', $temperatureRecord->id)->with('flash.toast', 'Registro de temperatura creado correctamente');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TemperatureRecord $temperatureRecord, Request $request)
    {
        $this->authorize('view', $temperatureRecord);

        $admission_id = $request->query('admission_id');

        $temperatureRecord->load([
            'admission.bed',
            'admission.patient',
            'nurse',
            'temperatureDetails.nurse',
            'eliminationRecords'
        ]);

        $eliminationsRecords = $temperatureRecord->eliminationRecords;
        $temperatureDetails = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)->with('nurse')
        ->orderBy('updated_at', 'asc')->orderBy('id', 'asc')->get();

        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $currentDateRange = $turnService->getDateRangeForTurn($currentTurn);
        $details = [];

        foreach ($temperatureDetails as $temperature) {
            // Obtener el turno de la fecha de la temperatura
            $temperatureTurn = $turnService->getCurrentTurnForDate($temperature->updated_at);
            $dateRange = $turnService->getDateRangeForTurn($temperatureTurn);

            // Buscar el registro de eliminación dentro del mismo turno
            $elimination = $eliminationsRecords->first(function ($el) use ($dateRange) {
                return Carbon::parse($el->updated_at)->between($dateRange['start'], $dateRange['end']);
            });

            // Agregar los datos combinados
            $details[] = [
                'temperature' => $temperature->temperature,
                'urinations' => $elimination ? $elimination->urinations : 0,
                'evacuations' => $elimination ? $elimination->evacuations : 0,
                'nurse' => [
                    'name' => $temperature->nurse->name,
                    'last_name' => $temperature->nurse->last_name,
                ],
                'updated_at' => $temperature->created_at
            ];
        }

        // verificar si puede crear elimination
        $responseCreateElimination = Gate::inspect('create', [EliminationRecord::class, $temperatureRecord->id]);
        $canCreateElimination = $responseCreateElimination->allowed();

        // verificar si puede crear detalles
        $responseCreateDetail = Gate::inspect('create', [TemperatureDetail::class, $temperatureRecord->id]);
        $canCreateDetail = $responseCreateDetail->allowed();

        $lastTemperature = TemperatureDetail::where('temperature_record_id', $temperatureRecord->id)
            ->orderBy('updated_at', 'desc')
            ->whereBetween('updated_at',[$currentDateRange['start'], $currentDateRange['end']])
            ->first();

        $lastEliminations = EliminationRecord::where('temperature_record_id', $temperatureRecord->id)
            ->orderBy('updated_at', 'desc')
            ->first();

        // verificar si puede actualizar temperatura
        $canUpdateDetail = false;
        if ($lastTemperature) {
            $responseUpdateDetail = Gate::inspect('update', [TemperatureDetail::class, $lastTemperature]);
            $canUpdateDetail = $responseUpdateDetail->allowed();
            $lastTemperature = $canUpdateDetail ? $lastTemperature : null;
        }

        // verificar si puede actualizar elimination
        $canUpdateElimination = false;
        if ($lastEliminations) {
            $responseUpdateDetail = Gate::inspect('update', [EliminationRecord::class, $lastEliminations]);
            $canUpdateElimination = $responseUpdateDetail->allowed();
        }

        // verificar si puede editar la firma del registro
        $responseUpdateRecord = Gate::inspect('updateSignature', $temperatureRecord);
        $canUpdateSignature = $responseUpdateRecord->allowed();

        return Inertia::render('TemperatureRecords/Show', [
            'temperatureRecord' => $temperatureRecord,
            'admission_id' => $admission_id,
            'details' => $details,
            'lastTemperature' => $lastTemperature,
            'lastEliminations' => $lastEliminations,
            'canCreateElimination' => $canCreateElimination,
            'canUpdateElimination' => $canUpdateElimination,
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
            'nurse_id' => 'numeric|nullable',
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

        return back()->with('flash.toast', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TemperatureRecord $temperatureRecord)
    {
        $this->authorize('delete', $temperatureRecord);
        $temperatureRecord->update(['active' => 0]);
        return Redirect::route('temperatureRecords.index')->with('flash.toast', 'Registro de temperatura eliminado correctamente');
    }
}
