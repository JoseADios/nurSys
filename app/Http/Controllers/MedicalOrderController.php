<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\Regime;
use App\Services\FirmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MedicalOrderController extends Controller
{
    protected $firmService;
    public function __construct(FirmService $firmService)
    {
        $this->firmService = $firmService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MedicalOrder::where('active', true)
            ->with('admission.patient', 'admission.bed', 'doctor')
            ->orderBy('created_at', 'desc')
            ->orderBy('updated_at', 'desc');

        if ($request->has('admission_id')) {
            $query->where('admission_id', $request->admission_id);
        }

        $medicalOrders = $query->get();

        return Inertia::render('MedicalOrders/Index', [
            'medicalOrders' => $medicalOrders,
            'admission_id' => $request->admission_id,
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
            'admission_id' => intval($admission_id)
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
    public function edit(MedicalOrder $medicalOrder)
    {
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();
        $medicalOrder->load(['admission.patient', 'admission.bed', 'admission.doctor']);
        $regimes = Regime::all();
        $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)
            ->where('active', true)
            ->orderBy('updated_at', 'desc')
            ->orderBy('created_at', 'desc')->get();

        return Inertia::render('MedicalOrders/Edit', [
            'medicalOrder' => $medicalOrder,
            'details' => $details,
            'admissions' => $admissions,
            'regimes' => $regimes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalOrder $medicalOrder)
    {
        $validated = $request->validate([
            'admission_id' => 'numeric',
            'doctor_sign' => 'string',
        ]);

        if ($request->signature) {
            $fileName = $this->firmService
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
        //
    }
}
