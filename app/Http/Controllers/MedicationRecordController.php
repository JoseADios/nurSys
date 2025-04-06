<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use App\Models\Diet;
use App\Models\Drug;
use App\Models\DrugRoute;
use App\Models\DrugDose;
use App\Models\MedicalOrderDetail;
use App\Models\MedicalOrder;
use Inertia\Inertia;
use App\Models\MedicationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class MedicationRecordController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicationRecord.view', only: ['index', 'show']),
            new Middleware('permission:medicationRecord.create', only: ['store']),
            new Middleware('permission:medicationRecord.update', only: ['update']),
            new Middleware('permission:medicationRecord.delete', only: ['destroy']),
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
        $days = $request->integer('days');

        $query = MedicationRecord::query()
            ->select('medication_records.*', 'patients.first_name', 'patients.first_surname', 'patients.second_surname')
            ->join('admissions', 'medication_records.admission_id', '=', 'admissions.id')
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->where('medication_records.active', !$showDeleted);

        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->WhereRaw('admissions.id LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('diagnosis LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('diet LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%']);
            });
        }
        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('medication_records.updated_at')
                ->latest('medication_records.created_at');
        }
        if ($days) {
            $query->where('medication_records.created_at', '>=', now()->subDays($days));
        }


        $medicationRecords = $query->with('admission.bed', 'admission.patient', 'admission')->orderByDesc('created_at')->paginate(10);

        return Inertia::render('MedicationRecords/Index', [
            'medicationRecords' => $medicationRecords,
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ],
        ]);
    }
    //
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $admission_id = $request->integer('admission_id');
        $diet = Diet::all();

        // Pasar los datos a la vista
        return Inertia::render('MedicationRecords/Create', [
            'admission_id' => $admission_id,  // Enviar todos los registros de Admission
            'diet' => $diet,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', [MedicationRecord::class, $request->admission_id]);

        // Validación de los datos de entrada
        $request->validate([
            'admission_id' => 'required|exists:admissions,id', // Validamos que exista en la tabla admissions
            'diagnosis' => 'required|string',
            'diet' => 'required|string',
        ]);

        // Verificar si ya existe un MedicationRecord para la admisión especificada
        $existingRecord = MedicationRecord::where('admission_id', $request->admission_id)->where('active', true)->first();

        if ($existingRecord) {

            return redirect()->back()->withErrors([
                'admission_id' => 'Ya Existe una ficha de Medicamentos con ese numero de Admision.',
            ])->withInput();
        }

        // Crear el MedicationRecord usando los datos validados
        $medicationRecord = MedicationRecord::create([
            'admission_id' => $request->admission_id,

            'diagnosis' => $request->diagnosis,
            'diet' => $request->diet,
        ]);

        // Redirigir o retornar una respuesta exitosa
        return redirect()->route('medicationRecords.show', $medicationRecord->id)->with('flash.toast', 'Registro guardado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicationRecord $medicationRecord, Request $request)
    {
        try {
            $medicationRecord->load(['admission.patient', 'admission.bed', 'admission.doctor', 'medicationRecordDetail', 'admission.medicalOrders']);
            $allMedicalOrders = MedicalOrder::where('active', true)
                ->where('admission_id', $medicationRecord->admission->id)
                ->with([
                    'medicalOrderDetail' => function ($query) {
                        $query->whereNull('suspended_at')->where('active', true);
                    }
                ])
                ->get();


            $drug = Drug::all();
            $route = DrugRoute::all();
            $dose = DrugDose::all();
            $showDeleted = $request->boolean('showDeleted');

            if ($showDeleted || !$medicationRecord->active) {

                $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->where('active', false)->with('medicationNotification')->orderBy('created_at', 'desc')->get();




            } else {
                $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->where('active', true)->with('medicationNotification')->orderBy('created_at', 'desc')->get();

            }






            return Inertia::render('MedicationRecords/Show', [
                'medicationRecord' => $medicationRecord,
                'details' => $details,
                'orders' => $allMedicalOrders,
                'drug' => $drug,
                'dose' => $dose,
                'routeOptions' => $route,
                'filters' => [
                    'show_deleted' => $showDeleted,
                ],
            ]);


        } catch (\Exception $e) {
            return redirect()->route('MedicationRecords/Show')->with('error', $e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicationRecord $medicationRecord)
    {
        return Inertia::render('MedicationRecords/Edit', [
            'medicationRecord' => $medicationRecord,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicationRecord $medicationRecord)
    {

        $this->authorize('update', $medicationRecord);

        if ($request->has('active')) {
            $this->restore($medicationRecord->id);
        } else {
            $validated = $request->validate([
                'diagnosis' => 'required|string|max:255',
                'diet' => 'required|string|max:255',
                'referrals' => 'required|string|max:255',
                'pending_studies' => 'required|string|max:255',
            ]);
            $medicationRecord->update($validated);
        }


        return back()->with('flash.toast', 'Registro actualizado correctamente');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRecord $medicationRecord)
    {
        $this->authorize('delete', $medicationRecord);

        $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->get();

        $hasNotifications = MedicationNotification::whereIn('medication_record_detail_id', function ($query) use ($medicationRecord) {
            $query->select('id')
                ->from('medication_record_details')
                ->where('medication_record_id', $medicationRecord->id);
        })
            ->where('applied', 1)
            ->get();


        if ($hasNotifications->isNotEmpty()) {
            return Redirect::back()->withErrors(['message' => 'No se puede eliminar esta Ficha de Medicamentos porque tiene notificaciones aplicadas.']);
        }


        $medicationRecord->update(['active' => 0]);


        foreach ($details as $detail) {
            $detail->update(['active' => 0]);
        }

        MedicationNotification::whereIn('medication_record_detail_id', $details->pluck('id'))
            ->update(['active' => 0]);

        return redirect()->to(route('medicationRecords.show', $medicationRecord));

    }



    private function restore($id)
    {

        $record = MedicationRecord::findOrFail($id);

        $medicationRecordDetails = MedicationRecordDetail::where('medication_record_id', $id)->get();

        foreach ($medicationRecordDetails as $detail) {
            $detail->update(['active' => 1]);
        }


        $medicationNotificationIds = $medicationRecordDetails->pluck('id');


        $medicationNotifications = MedicationNotification::whereIn('medication_record_detail_id', $medicationNotificationIds)->get();


        foreach ($medicationNotifications as $notification) {
            $notification->update(['active' => 1]);
        }

        $record->active = true;
        $record->save();

        return redirect()->back()->with('success', 'Registro habilitado con éxito.');
    }









}
