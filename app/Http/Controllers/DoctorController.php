<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Mostrar la lista de médicos.
     */
    public function index()
    {
        $doctors = Doctor::all();
        return response()->json($doctors);
    }

    /**
     * Almacenar un nuevo doctor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'speciality' => 'required|string|max:255',
            'exequatur' => 'required|integer',
            'active' => 'required|boolean',
        ]);

        $doctor = Doctor::create([
            'speciality' => $request->speciality,
            'exequatur' => $request->exequatur,
            'active' => $request->active,
        ]);

        return response()->json($doctor, 201);
    }

    /**
     * Mostrar un doctor específico.
     */
    public function show($id)
    {
        $doctor = Doctor::find($id);
        if ($doctor) {
            return response()->json($doctor);
        }
        return response()->json(['message' => 'Doctor not found'], 404);
    }

    /**
     * Actualizar los datos de un doctor.
     */
    public function update(Request $request, $id)
    {
        $doctor = Doctor::find($id);
        if ($doctor) {
            $request->validate([
                'speciality' => 'nullable|string|max:255',
                'exequatur' => 'nullable|integer',
                'active' => 'nullable|boolean',
            ]);

            $doctor->update($request->all());
            return response()->json($doctor);
        }
        return response()->json(['message' => 'Doctor not found'], 404);
    }

    /**
     * Eliminar un doctor.
     */
    public function destroy($id)
    {
        $doctor = Doctor::find($id);
        if ($doctor) {
            $doctor->delete();
            return response()->json(['message' => 'Doctor deleted']);
        }
        return response()->json(['message' => 'Doctor not found'], 404);
    }
}
