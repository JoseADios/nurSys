<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:temperatureDetail.create', ['store']),
            new Middleware('permission:temperatureDetail.update', ['update']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener modelos disponibles para el filtro
        $availableModels = Activity::distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->toArray();

        // Construir la consulta base
        $query = Activity::with('causer')->latest();

        // Aplicar filtros si existen
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('model')) {
            $query->where('subject_type', $request->model);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', '>=', $request->date);
        }

        // Obtener resultados paginados
        $logs = $query->paginate(15)->withQueryString();

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'model', 'date']),
            'availableModels' => $availableModels
        ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
