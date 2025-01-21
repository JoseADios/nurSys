<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use App\Models\Diet;
use Inertia\Inertia;
use App\Models\MedicationRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MedicationRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicationRecords = MedicationRecord::with('admission')->get();
        return Inertia::render('MedicationRecords/Index', [
            'medicationRecords' => $medicationRecords,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $admissions = Admission::all();
        $diet = Diet::all();

        // Pasar los datos a la vista
        return Inertia::render('MedicationRecords/Create', [
            'admissions' => $admissions,  // Enviar todos los registros de Admission
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
    public function show(MedicationRecord $medicationRecord)
    {
        try{
        $medicationRecord = MedicationRecord::where('id',$medicationRecord->id)->with(['admission.patient','admission.bed','doctor','medicationRecordDetail'])->first();
        $details = MedicationRecordDetail::where('medication_record_id', operator: $medicationRecord->id)->with('medicationNotification')->orderBy('created_at', 'desc')->get();


        return Inertia::render('MedicationRecords/Show', [
            'medicationRecord' => $medicationRecord,
            'details' => $details,



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
        $validated = $request->validate([
            'diagnosis' => 'required|string|max:255',
            //crear doctor id = con auth ID
            'diet' => 'required|string|max:255',
            'referrals' => 'required|string|max:255',
            'pending_studies' => 'required|string|max:255',
        ]);

        $medicationRecord->update($validated);

        return redirect()->route('medicationRecords.index')
                         ->with('success', 'Medication record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationRecord $medicationRecord)
    {
        $medicationRecord->update(['active' => 0]);

    return Redirect::route('medicationRecords.index');
    }
}
