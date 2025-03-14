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
use App\Services\FirmService;
use Illuminate\Database\Eloquent\Builder;

class MedicationRecordController extends Controller
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicationRecords.view', only: ['index', 'show']),
            new Middleware('permission:medicationRecords.create', only: ['edit', 'store']),
            new Middleware('permission:medicationRecords.update', only: ['update']),
            new Middleware('permission:medicationRecords.delete', only: ['destroy']),
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

        $query = MedicationRecord::query()->select('medication_records.*')
        ->join('admissions', 'medication_records.admission_id', '=', 'admissions.id')
        ->where('medication_records.active', !$showDeleted);





        if ($search) {
            $query->where(function (Builder $q) use ($search) {
                $q->WhereRaw('admissions.id LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('diagnosis LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('diet LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('referrals LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('pending_studies LIKE ?', ['%' . $search . '%'])
                  ->orWhereRaw('doctor_sign LIKE ?', ['%' . $search . '%']);
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


        $medicationRecords = $query->with('admission.bed','admission.patient','admission')->orderByDesc('created_at')->paginate(10);

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
    public function create()
    {


        $diet = Diet::all();
        $admission = Admission::with('patient', 'bed', 'doctor')
        ->whereDoesntHave('medicationRecord')
        ->get();




        // Pasar los datos a la vista
        return Inertia::render('MedicationRecords/Create', [
            'admission' => $admission,  // Enviar todos los registros de Admission
            'diet' => $diet,
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([
            'admission_id' => 'required|exists:admissions,id', // Validamos que exista en la tabla admissions
            'diagnosis' => 'required|string',
            'diet' => 'required|string',
            'referrals' => 'nullable|string',
            'pending_studies' => 'nullable|string',
            'doctor_sign' => 'required|string',
        ]);

        // Verificar si ya existe un MedicationRecord para la admisión especificada
        $existingRecord = MedicationRecord::where('admission_id', $request->admission_id)->first();

        if ($existingRecord) {
            // Si ya existe, redirigir con un mensaje de error
            return redirect()->back()->withErrors([
                'admission_id' => 'Ya Existe una ficha de Medicamentos con ese numero de Admision.',
            ])->withInput();
        }

        // Crear el MedicationRecord usando los datos validados
        $medicationRecord = MedicationRecord::create([
            'admission_id' => $request->admission_id,
            'doctor_id' => Auth::id(),
            'diagnosis' => $request->diagnosis,
            'diet' => $request->diet,
            'referrals' => $request->referrals,
            'pending_studies' => $request->pending_studies,
            'doctor_sign' => $request->doctor_sign,
        ]);

        // Redirigir o retornar una respuesta exitosa
        return redirect()->route('medicationRecords.index')->with('success', 'Medication Record created successfully');
    }



    /**
     * Display the specified resource.
     */
    public function show(MedicationRecord $medicationRecord,Request $request)
    {
        try{
        $medicationRecord->load(['admission.patient','admission.bed','doctor','medicationRecordDetail','admission.medicalOrders']);
        $allMedicalOrders = MedicalOrder::where('active', true)
        ->where('admission_id', $medicationRecord->admission->id)
        ->with(['medicalOrderDetail' => function ($query) {
            $query->whereNull('suspended_at');
        }])
        ->get();


        $drug = Drug::all();
        $route = DrugRoute::all();
        $dose = DrugDose::all();
        $showDeleted = $request->boolean('showDeleted');

            if ($showDeleted || !$medicationRecord->active) {

                    $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->where('active',false)->with('medicationNotification')->orderBy('created_at', 'desc')->get();




                }else{
                    $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->where('active',true)->with('medicationNotification')->orderBy('created_at', 'desc')->get();

                }






                return Inertia::render('MedicationRecords/Show', [
                    'medicationRecord' => $medicationRecord,
                    'details' => $details,
                    'order' => $allMedicalOrders,
                    'drug' =>$drug,
                    'dose' => $dose,
                    'routeOptions' => $route,
                    'filters' => [
                        'show_deleted' => $showDeleted,
                    ],
                ]);


    }catch(\Exception $e){
    return redirect()->route('MedicationRecords/Show')->with('error',$e);
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
        $firmService = new FirmService;

       if ($request->has('signature')) {
        $fileName = $firmService->createImag($request->doctor_sign, $medicationRecord->doctor_sign);
        $medicationRecord->update(['doctor_sign' => $fileName]);

        return back()->with('flash.toast', 'Registro actualizado correctamente');

    }

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
