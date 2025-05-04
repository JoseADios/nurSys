<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
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
            new Middleware('role:admin', ['index'])
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener modelos disponibles para el filtro
        $models = Activity::distinct()
            ->whereNotNull('subject_type')
            ->pluck('subject_type')
            ->toArray();

        $availableModels = $this->setModelsWithLabels($models);

        $causers = Activity::distinct()
            ->whereNotNull('causer_id')
            ->pluck('causer_id')
            ->toArray();

        $availableCausers = User::whereIn('id', $causers)->get();

        // Construir la consulta base
        $query = Activity::with('causer')->latest();

        // Aplicar filtros si existen
        if ($request->filled('action')) {
            $query->where('description', $request->action);
        }

        if ($request->filled('model')) {
            $query->where('subject_type', $request->model);
        }

        if ($request->filled('causer')) {
            $query->where('causer_id', $request->causer);
        }

        if ($request->filled('startDate') && $request->filled('endDate')) {
            $startDate = $request->startDate;
            $endDate = Carbon::parse($request->endDate)->endOfDay();
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Obtener resultados paginados
        $logs = $query->paginate(15)->withQueryString();

        return Inertia::render('ActivityLogs/Index', [
            'logs' => $logs,
            'filters' => $request->only(['search', 'model', 'date']),
            'availableModels' => $availableModels,
            'availableCausers' => $availableCausers
        ]);
    }


    private function setModelsWithLabels($modelNames)
    {
        $modelLabels = config('model_labels');
        $mapNamesWhitLabels = [];

        foreach ($modelNames as $key => $name) {
            $model = [];
            $model['name'] =  $name;
            $model['label'] = $modelLabels[$name] ?? $name;

            array_push($mapNamesWhitLabels, $model);
        }
        return $mapNamesWhitLabels;
    }
}
