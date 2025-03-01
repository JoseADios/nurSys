<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\MedicationRecordDetail;
use App\Models\Regime;
use App\Services\FirmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
class MedicalOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $admissionId = $request->input('admission_id');

        $query = MedicalOrder::where('active', true)
            ->with('admission.patient', 'admission.bed', 'doctor')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('created_at', 'like', '%' . $search . '%')
                      ->orWhereHas('admission.patient', function ($patientQuery) use ($search) {
                          $patientQuery->where('first_name', 'like', '%' . $search . '%')
                                       ->orWhere('first_surname', 'like', '%' . $search . '%')
                                       ->orWhere('second_surname', 'like', '%' . $search . '%');
                      })
                      ->orWhereHas('doctor', function ($doctorQuery) use ($search) {
                          $doctorQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            }

            if (!empty($admissionId)) {
                $query->where('admission_id', intval($admissionId));
            }

        $medicalOrders = $query->paginate(10);

        return Inertia::render('MedicalOrders/Index', [
            'medicalOrders' => $medicalOrders,
            'admission_id' => $request->admission_id,
            'filters' => ['search' => $search],
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
        return Inertia::render('MedicalOrders/Create', [
            'admissions' => $admissions,
            'admission_id' => intval($admission_id),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $medicalOrder = MedicalOrder::create([
            'admission_id' => $request->admission_id,
            'doctor_id' => Auth::id(),
            'created_at' => now(),
        ]);

        return Redirect::route('medicalOrders.edit', $medicalOrder->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalOrder $medicalOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalOrder $medicalOrder,Request $request)
    {
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();

        $medicalOrder->load(['admission.patient', 'admission.bed', 'admission.doctor','admission.medicationRecord']);

        $showDeleted = $request->boolean('showDeleted');
        $regimes = Regime::all();


            if ($showDeleted || !$medicalOrder->active) {

                $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->where('active',false)->orderBy('created_at', 'desc')->get();

            }else{
                $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->where('active',true)->orderBy('created_at', 'desc')->get();

            }
        return Inertia::render('MedicalOrders/Edit', [
            'medicalOrder' => $medicalOrder,
            'details' => $details,
            'admissions' => $admissions,
            'regimes' => $regimes,
            'previousUrl' => URL::previous(),
            'filters' => [
                'show_deleted' => $showDeleted,
            ],
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalOrder $medicalOrder)
    {
        $firmService = new FirmService;

        $validated = $request->validate([
            'admission_id' => 'numeric',
            'doctor_sign' => 'string',
            'active' => 'boolean',
        ]);



        if ($request->active === true) {
            $medicalOrderDetailIds = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->pluck('id');
            Log::info($medicalOrderDetailIds);
            $medicationRecordDetails = MedicationRecordDetail::whereIn('medical_order_detail_id', $medicalOrderDetailIds)->get();
            Log::info($medicationRecordDetails);
            foreach ($medicationRecordDetails as $medicationRecordDetail) {
                $medicationRecordDetail->update(['suspended_at' => null]);
            }
        }


        if ($request->signature) {
            $fileName = $firmService
                ->createImag($request->doctor_sign, $medicalOrder->doctor_sign);
            $validated['doctor_sign'] = $fileName;
        }

        $medicalOrder->update($validated);

        return back()->with('succes', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrder $medicalOrder)
    {
        $medicalOrderDetailIds = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->pluck('id');
        $medicationRecordDetails = MedicationRecordDetail::whereIn('medical_order_detail_id', $medicalOrderDetailIds)->get();


          if ($medicationRecordDetails->pluck('medical_order_detail_id')->intersect($medicalOrderDetailIds)->isNotEmpty()) {
            return redirect()->to(route('medicalOrders.show', $medicalOrder));
        }
        $medicalOrder->update(['active'=> 0]);

        foreach ($medicationRecordDetails as $medicationRecordDetail) {
            $medicationRecordDetail->update(['suspended_at' => now()]);
        }
        return Redirect::route('medicalOrders.index');
    }
}
