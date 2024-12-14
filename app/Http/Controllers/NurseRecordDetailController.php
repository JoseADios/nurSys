<?php

namespace App\Http\Controllers;

use App\Models\NurseRecordDetail;
use Illuminate\Http\Request;

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
            'active' => true,
            'created_at' => now()
        ]);

        return back()->with('success', 'Detalle agregado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(NurseRecordDetail $nurse_record_detail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NurseRecordDetail $nurse_record_detail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NurseRecordDetail $nurse_record_detail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NurseRecordDetail $nurse_record_detail)
    {
        //
    }
}
