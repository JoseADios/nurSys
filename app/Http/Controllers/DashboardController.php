<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\MedicationRecord;
use App\Models\NurseRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Obtener estadísticas generales
        $stats = [
            'total_admissions' => Admission::count(),
            'active_admissions' => Admission::whereNull('discharged_date')->count(),
            'available_beds' => Bed::all()->filter->isAvailable()->count(),
            'total_beds' => Bed::count(),
            'nurse_records_today' => NurseRecord::whereDate('created_at', today())->count(),
            'admissions_by_week' => $this->getWeeklyAdmissions()
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }

    private function getWeeklyAdmissions()
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        return Admission::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total
                ];
            });
    }
}
