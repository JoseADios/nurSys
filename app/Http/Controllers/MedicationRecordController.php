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


        $query = MedicationRecord::query();
        $admission = Admission::with('patient', 'bed')->get();

        if ($showDeleted) {
            $query->where('active',false);
        }else{
            $query->where('active',true);
        }

        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('medication_records.updated_at')
                ->latest('medication_records.created_at');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('admission_id', 'like', '%' . $search . '%')
                  ->orWhere('doctor_id', 'like', '%' . $search . '%')
                  ->orWhere('diagnosis', 'like', '%' . $search . '%')
                  ->orWhere('diet', 'like', '%' . $search . '%')
                  ->orWhere('referrals', 'like', '%' . $search . '%')
                  ->orWhere('pending_studies', 'like', '%' . $search . '%')
                  ->orWhere('doctor_sign', 'like', '%' . $search . '%');
            });
        }


        $medicationRecords = $query->with('admission')->orderByDesc('created_at')->paginate(10);

        return Inertia::render('MedicationRecords/Index', [
            'medicationRecords' => $medicationRecords,
            'filters' => [
                'search' => $search,
                'show_deleted' => $showDeleted,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ],
            'admission' => $admission,
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
        $allMedicalOrders = MedicalOrder::where('active',true)->where('admission_id',$medicationRecord->admission->id)->with('medicalOrderDetail')->get();



        $drug = Drug::all();
        $route = DrugRoute::all();
        $dose = DrugDose::all();
        $showDeleted = $request->boolean('showDeleted');

            if ($showDeleted || !$medicationRecord->active) {

                    $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->where('active',false)->with('medicationNotification')->orderBy('created_at', 'desc')->get();

                Log::info("message");


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
        if ($request->has('suspended_at')) {

            if ($request->suspended_at == true) {
                $this->Enable($medicationRecord->id);
            } else {
                $this->Disable($medicationRecord->id);
            }
        }else if ($request->has('active')) {
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


        return redirect()->route('medicationRecords.index')
                         ->with('success', 'Medication record updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
     public function destroy(MedicationRecord $medicationRecord)
     {

         $medicationRecord->update(['active' => 0]);
         $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->get();


    foreach ($details as $detail) {
        $detail->update(['active' => 0]);
    }


    $notificationIds = $details->pluck('id');
    MedicationNotification::whereIn('medication_record_detail_id', $notificationIds)
        ->update(['active' => 0]);

     return Redirect::route('medicationRecords.index');
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

        private function Disable($id){
            Log::info('disable');
            $record = MedicationRecord::findOrFail($id);

            $medicationRecordDetails = MedicationRecordDetail::where('medication_record_id', $id)->get();

            foreach ($medicationRecordDetails as $detail) {
                $detail->update(['suspended_at' => now()]);
            }


            $medicationNotificationIds = $medicationRecordDetails->pluck('id');


            $medicationNotifications = MedicationNotification::whereIn('medication_record_detail_id', $medicationNotificationIds)->get();


            foreach ($medicationNotifications as $notification) {
                $notification->update(['suspended_at' => now()]);
            }

            $record->suspended_at = now();
            $record->save();
        }

        private function Enable($id)
        {

            Log::info('enable');

                $record = MedicationRecord::findOrFail($id);

                $medicationRecordDetails = MedicationRecordDetail::where('medication_record_id', $id)->get();

                foreach ($medicationRecordDetails as $detail) {
                    $detail->update(['suspended_at' => null]);
                }


                $medicationNotificationIds = $medicationRecordDetails->pluck('id');


                $medicationNotifications = MedicationNotification::whereIn('medication_record_detail_id', $medicationNotificationIds)->get();


                foreach ($medicationNotifications as $notification) {
                    $notification->update(['suspended_at' => null]);
                }

                $record->suspended_at = null;
                $record->save();




            return redirect()->back()->with('success', 'Registro habilitado con éxito.');
           }






}
