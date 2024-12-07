<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    /**
     * Display a listing of the nurses.
     */
    public function index()
    {
        $nurses = Nurse::all();
        return view('nurses.index', compact('nurses'));
    }

    /**
     * Show the form for creating a new nurse.
     */
    public function create()
    {
        return view('nurses.create');
    }

    /**
     * Store a newly created nurse in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'speciality' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);

        Nurse::create($validatedData);

        return redirect()->route('nurses.index')->with('success', 'Nurse created successfully.');
    }

    /**
     * Display the specified nurse.
     */
    public function show(Nurse $nurse)
    {
        return view('nurses.show', compact('nurse'));
    }

    /**
     * Show the form for editing the specified nurse.
     */
    public function edit(Nurse $nurse)
    {
        return view('nurses.edit', compact('nurse'));
    }

    /**
     * Update the specified nurse in storage.
     */
    public function update(Request $request, Nurse $nurse)
    {
        $validatedData = $request->validate([
            'speciality' => 'required|string|max:255',
            'area' => 'required|string|max:255',
            'active' => 'required|boolean',
        ]);

        $nurse->update($validatedData);

        return redirect()->route('nurses.index')->with('success', 'Nurse updated successfully.');
    }

    /**
     * Remove the specified nurse from storage.
     */
    public function destroy(Nurse $nurse)
    {
        $nurse->delete();

        return redirect()->route('nurses.index')->with('success', 'Nurse deleted successfully.');
    }
}
