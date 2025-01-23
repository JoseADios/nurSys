<?php

namespace App\Http\Controllers;

use App\Models\NurseRecordDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

use function PHPUnit\Framework\returnSelf;

class NurseRecordDetailController extends Controller
{
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
        NurseRecordDetail::create([
            'nurse_record_id' => $request->nurse_record_id,
            'medication' =>  $request->medication,
            'comment' => $request->comment,
            'created_at' => now()
        ]);

        return back()->with('success', 'Detalle agregado exitosamente');
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
        return Inertia::render('NurseRecordDetail/Edit', [
            'nurseRecordDetail' => $nurseRecordDetail
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NurseRecordDetail $nurseRecordDetail)
    {
        $nurseRecordDetail->update($request->all());
        return Redirect::route('nurseRecords.edit', $nurseRecordDetail->nurse_record_id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NurseRecordDetail $nurseRecordDetail)
    {
        $nurseRecordDetail->update(['active' => 0]);
        return Redirect::route('nurseRecords.edit', $nurseRecordDetail->nurse_record_id);
    }
}
