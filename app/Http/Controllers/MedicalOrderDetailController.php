<?php

namespace App\Http\Controllers;

use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\MedicationNotification;
use App\Models\MedicationRecordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class MedicalOrderDetailController extends Controller implements HasMiddleware
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
    }  /**
       * Display a listing of the resource.
       */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $medicalOrder = MedicalOrder::findOrFail($request->medical_order_id);

        $this->authorize('create', [MedicalOrderDetail::class, $medicalOrder]);

        MedicalOrderDetail::create([
            'medical_order_id' => $request->medical_order_id,
            'order' => $request->order,
            'regime' => $request->regime,
            'created_at' => now()
        ]);


        return back()->with('success', 'Detalle agregado exitosamente')->with('flash.toast', 'Detalle guardado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalOrderDetail $medicalOrderDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalOrderDetail $medicalOrderDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalOrderDetail $medicalOrderDetail)
    {
        $this->authorize('update', [MedicalOrderDetail::class, $medicalOrderDetail]);



        $medicationRecordDetail = MedicationRecordDetail::where('medical_order_detail_id', $medicalOrderDetail->id)->first();
        if ($request->suspended_at == null) {
            if ($medicationRecordDetail) {
                $medicationRecordDetail->update(['suspended_at' => null]);

            }
        } else {
            if ($medicationRecordDetail) {
                $medicationRecordDetail->update(['suspended_at' => now()]);
            }
        }
        $medicalOrderDetail->update($request->all());
        return back()->with('flash.toast', 'Registro actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalOrderDetail $medicalOrderDetail)
    {
        $this->authorize('delete', [MedicalOrderDetail::class, $medicalOrderDetail]);
        $medicalOrderDetail->update(['active' => 0]);
        $admission_id = $medicalOrderDetail->medicalOrder()->first(['admission_id'])->admission_id;
        return Redirect::route('medicalOrders.show', ['medicalOrder' => $medicalOrderDetail->medical_order_id, 'admission_id' => $admission_id])->with('flash.toast', 'Registro eliminado correctamente');
        ;
    }
}
