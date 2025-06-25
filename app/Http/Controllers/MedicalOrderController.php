<?php

namespace App\Http\Controllers;
use App\Models\User;
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
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
class MedicalOrderController extends Controller implements HasMiddleware
{

    use AuthorizesRequests;
    public static function middleware(): array
    {
        return [
            new Middleware('permission:medicalOrder.view', only: ['index', 'show']),
            new Middleware('permission:medicalOrder.create', only: ['store']),
            new Middleware('permission:medicalOrder.update', only: ['update']),
            new Middleware('permission:medicalOrder.delete', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $admissionId = $request->integer('admission_id');
        $search = $request->input('search', '');
        $sortField = $request->input('sortField');
        $sortDirection = $request->input('sortDirection', 'asc');
        $showDeleted = $request->boolean('showDeleted');
        $days = $request->integer('days');
        $myRecords = $request->boolean('myRecords', Auth::user()->hasRole(['nurse', 'admin']));
        $in_process = $request->input('in_process', 'true');

        // si se filtra por ingreso mostrar los registros aunque esten dados de alta
        if ($admissionId && !$request->has('in_process')) {
            $in_process = false;
        }

        $query = MedicalOrder::with('admission.patient', 'admission.bed', 'admission.doctor')
            ->select([
                'medical_orders.created_at',
                'medical_orders.*'
            ])
            ->join('admissions', 'medical_orders.admission_id', '=', 'admissions.id')
            ->join('patients', 'admissions.patient_id', '=', 'patients.id')
            ->join('beds', 'admissions.bed_id', '=', 'beds.id')
            ->join('users', 'admissions.doctor_id', '=', 'users.id')
            ->where('medical_orders.active', !$showDeleted);


        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw('DATE(medical_orders.created_at) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(patients.first_name, " ", patients.first_surname, " ", COALESCE(patients.second_surname, "")) LIKE ?', ['%' . $search . '%'])
                    ->orWhereRaw('CONCAT(users.name, " ", COALESCE(users.last_name, "")) LIKE ?', ['%' . $search . '%']);

            });
        }

        if ($days) {
            $query->where(
                'medical_orders.created_at',
                '>=',
                now()->subDays($days)->startOfDay()
            );
        }
        if ($in_process === 'true') {
            $query->whereNull('admissions.discharged_date');
        } elseif ($in_process === 'false') {
            $query->whereNotNull('admissions.discharged_date');
        }
        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest('medical_orders.updated_at')
                ->latest('medical_orders.created_at');
        }
        if (!empty($admissionId)) {
            $query->where('admission_id', intval($admissionId));
        }
        if ($myRecords) {
            $query->where('medical_orders.doctor_id', Auth::id());
        }


        $medicalOrders = $query->paginate(10);
        $medicalOrders->getCollection()->transform(function ($order) {
            if ($order->admission->discharged_date != null) {
                $order->in_process = false;
            } else {
                $order->in_process = true;
            }
            return $order;
        });

        return Inertia::render('MedicalOrders/Index', [
            'medicalOrders' => $medicalOrders,
            'filters' => [
                'admission_id' => $admissionId,
                'search' => $search,
                'show_deleted' => $showDeleted,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
                'in_process' => $in_process,
                'myRecords' => $myRecords,
            ],
            'can' => [
                'create' => Gate::allows('create', Admission::class),
            ],
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
        $this->authorize('create', [MedicalOrder::class, $request->admission_id]);

        $has_admission_id = $request->boolean('has_admission_id');
        $medicalOrder = MedicalOrder::create([
            'admission_id' => $request->admission_id,
            'doctor_id' => Auth::id(),
            'created_at' => now(),
        ]);


        return redirect()->route('medicalOrders.show', ['medicalOrder' => $medicalOrder->id, 'admission_id' => $has_admission_id])->with('flash.toast', 'Registro guardado correctamente');

    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalOrder $medicalOrder, Request $request)
    {
        $this->authorize('view', $medicalOrder);
        $medicalOrder->load(['admission.patient', 'admission.bed', 'admission.doctor', 'admission.medicationRecord']);
        $admissionId = $request->integer('admission_id');
        $showDeleted = $request->boolean('showDeleted');

        $query = MedicalOrder::where('admission_id', $medicalOrder->admission_id)->with('admission.patient', 'admission.bed', 'admission.doctor', 'admission.medicationRecord');
        $medicalOrder = $query->latest()->firstOrFail();
        Log::info($medicalOrder);
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();


        $doctor = User::find($medicalOrder->doctor_id);

        if ($showDeleted || !$medicalOrder->active) {
            $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)
                ->where('active', false)
                ->orderBy('created_at', 'desc')
                ->get();

        } else {
            $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)
                ->where('active', true)
                ->orderBy('created_at', 'desc')
                ->get();

        }


        $regimes = Regime::all();

        return Inertia::render('MedicalOrders/Show', [
            'medicalOrder' => $medicalOrder,
            'details' => $details,
            'admissions' => $admissions,
            'doctor' => $doctor,
            'admission_id' => $admissionId,
            'regimes' => $regimes,
            'previousUrl' => URL::previous(),
            'filters' => [
                'showDeleted' => $showDeleted,
            ],
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalOrder $medicalOrder, Request $request)
    {
        $admissions = Admission::where('active', true)->with('patient', 'bed')->get();

        $medicalOrder->load(['admission.patient', 'admission.bed', 'admission.doctor', 'admission.medicationRecord']);

        $showDeleted = $request->boolean('showDeleted');
        $regimes = Regime::all();


        if ($showDeleted || !$medicalOrder->active) {

            $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->where('active', false)->orderBy('created_at', 'desc')->get();

        } else {
            $details = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->where('active', true)->orderBy('created_at', 'desc')->get();

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

        $this->authorize('update', $medicalOrder);

        if ($request->has('admission_id') && $request->admission_id !== $medicalOrder->admission_id) {
            $this->authorize('updateAdmission', [MedicalOrder::class, $request->admission_id]);
        }
        if ($request->has('doctor_id') && $request->input('doctor_id')) {
            $this->authorize('updateNurse', $medicalOrder);
            $medicalOrder->update($request->only(['doctor_id']));
        }

        if ($request->active === true) {
            $medicalOrderDetailIds = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->pluck('id');

            $medicationRecordDetails = MedicationRecordDetail::whereIn('medical_order_detail_id', $medicalOrderDetailIds)->get();

            foreach ($medicationRecordDetails as $medicationRecordDetail) {
                $medicationRecordDetail->update(['suspended_at' => null]);
            }
        }


        if ($request->signature) {
            $fileName = $firmService
                ->createImag($request->doctor_sign, $medicalOrder->doctor_sign);
            $validated['doctor_sign'] = $fileName;
            $medicalOrder->update($validated);

            return back()->with('flash.toast', 'Registro actualizado correctamente');
        }

        $validated = $request->validate([
            'admission_id' => 'numeric',
            'doctor_sign' => 'string',
            'active' => 'boolean',
        ]);
        $medicalOrder->update($validated);

        return back()->with('flash.toast', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrder $medicalOrder)
    {
        $this->authorize('delete', $medicalOrder);

        $medicalOrderDetailIds = MedicalOrderDetail::where('medical_order_id', $medicalOrder->id)->pluck('id');
        $medicationRecordDetails = MedicationRecordDetail::whereIn('medical_order_detail_id', $medicalOrderDetailIds)->get();

        if ($medicationRecordDetails->isNotEmpty()) {

            return back()->with('flash.toast', 'No se puede eliminar esta Orden Medica porque tiene registros de Fichas de medicamento asociadas.')->with('flash.toastStyle', 'danger');

        }

        $medicalOrder->update(['active' => 0]);

        foreach ($medicationRecordDetails as $medicationRecordDetail) {
            $medicationRecordDetail->update(['suspended_at' => now()]);
        }
        return Redirect::route('medicalOrders.index', ['admission_id' => $medicalOrder->admission_id])->with('flash.toast', 'Registro eliminado correctamente');
    }
}
