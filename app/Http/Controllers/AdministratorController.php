<?php

namespace App\Http\Controllers;

use App\Models\Administrator;
use Illuminate\Http\Request;

class AdministratorController extends Controller
{
    /**
     * Display a listing of the administrators.
     */
    public function index()
    {
        $administrators = Administrator::all();
        return view('administrators.index', compact('administrators'));
    }

    /**
     * Show the form for creating a new administrator.
     */
    public function create()
    {
        return view('administrators.create');
    }

    /**
     * Store a newly created administrator in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'active' => 'required|boolean',
        ]);

        Administrator::create($validatedData);

        return redirect()->route('administrators.index')->with('success', 'Administrator created successfully.');
    }

    /**
     * Display the specified administrator.
     */
    public function show(Administrator $administrator)
    {
        return view('administrators.show', compact('administrator'));
    }

    /**
     * Show the form for editing the specified administrator.
     */
    public function edit(Administrator $administrator)
    {
        return view('administrators.edit', compact('administrator'));
    }

    /**
     * Update the specified administrator in storage.
     */
    public function update(Request $request, Administrator $administrator)
    {
        $validatedData = $request->validate([
            'active' => 'required|boolean',
        ]);

        $administrator->update($validatedData);

        return redirect()->route('administrators.index')->with('success', 'Administrator updated successfully.');
    }

    /**
     * Remove the specified administrator from storage.
     */
    public function destroy(Administrator $administrator)
    {
        $administrator->delete();

        return redirect()->route('administrators.index')->with('success', 'Administrator deleted successfully.');
    }
}
