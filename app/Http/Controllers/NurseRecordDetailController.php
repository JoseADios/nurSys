<?php

namespace App\Http\Controllers;

use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;


class NurseRecordDetailController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    public static function middleware(): array
    {
        return [
            new Middleware('permission:nurseRecordDetail.view', only: ['index', 'show']),
            new Middleware('permission:nurseRecordDetail.create', only: ['store']),
            new Middleware('permission:nurseRecordDetail.update', only: ['update']),
            new Middleware('permission:nurseRecordDetail.delete', only: ['destroy']),
        ];
    }

    /**
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
        $nurseRecord = NurseRecord::findOrFail($request->nurse_record_id);
        $this->authorize('create', [NurseRecordDetail::class, $nurseRecord]);

        $validated = $request->validate([
            'nurse_record_id' => 'integer',
            'medication' => 'string|max:255|required',
            'comment' => 'string|required'
        ]);

        NurseRecordDetail::create($validated);

        return back()->with('flash.toast', 'Detalle agregado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(NurseRecordDetail $nurseRecordDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NurseRecordDetail $nurseRecordDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NurseRecordDetail $nurseRecordDetail)
    {
        $showDeleted = $request->boolean('showDeleted');
        $this->authorize('update', $nurseRecordDetail);

        $validated = $request->validate([
            'nurse_record_id' => 'integer',
            'medication' => 'string|max:255|required',
            'comment' => 'string|required',
            'active' => 'boolean'
        ]);

        $nurseRecordDetail->update($validated);
        return Redirect::route('nurseRecords.show', ['nurseRecord' => $nurseRecordDetail->nurse_record_id, 'showDeleted' => $showDeleted])->with('flash.toast', 'Evento actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NurseRecordDetail $nurseRecordDetail)
    {
        $nurseRecordDetail->update(['active' => 0]);
        return Redirect::route('nurseRecords.show', $nurseRecordDetail->nurse_record_id)->with('flash.toast', 'Evento eliminado exitosamente');
    }
}
