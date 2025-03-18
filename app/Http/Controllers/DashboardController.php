<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\NurseRecord;
use App\Models\Patient;
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
            'admissions_without_bed' => Admission::whereNull('bed_id')->active()->with('patient')->get(),
            'admissions_discharged_by_week' => $this->getWeeklyAdmissions(false),
            'patients_by_ars' => $this->getPatientsByArs(),
            'beds_by_status' => $this->getBedsByStatus(),
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

    private function getPatientsByArs()
    {
        $query = Patient::query()
            ->selectRaw('COUNT(*) AS count, ars AS name')
            ->groupBy('name')
            ->orderByDesc('count');

        return $query->get()
            ->map(function ($item) {
                return [
                    'name' => $item->name ? $item->name : 'Sin seguro',
                    'count' => $item->count
                ];
            });
    }

    private function getBedsByStatus()
    {
        $statuses = ['available' => 'Disponible', 'cleaning' => 'En limpieza', 'out_of_service' => 'Fuera de servicio', 'ocuppied' => 'Ocupada'];
        $query = Bed::query()
            ->selectRaw('COUNT(*) AS count, status AS status')
            ->groupBy('status')
            ->orderByDesc('count');

        return $query->get()
            ->map(function ($item) use ($statuses) {
                return [
                    'status' => $statuses[$item->status],
                    'count' => $item->count
                ];
            });
    }

}
