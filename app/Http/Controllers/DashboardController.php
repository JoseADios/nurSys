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
            'admissions_by_week' => $this->getWeeklyAdmissions(),
            'admissions_discharged_by_week' => $this->getWeeklyAdmissions(false),
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }

    private function getWeeklyAdmissions($activeAdmissions = true)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $query = Admission::query()
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek]);

        if (!$activeAdmissions) {
            $query->whereNotNull('discharged_date');
            $query->selectRaw('DATE(discharged_date) as date, COUNT(*) as total');
        } else {
            $query->selectRaw('DATE(created_at) as date, COUNT(*) as total');
        }

        $query->groupBy('date')
            ->orderBy('date');

        return $query->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'total' => $item->total
                ];
            });
    }
}
