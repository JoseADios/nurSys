<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\NurseRecord;
use App\Models\Patient;
use DB;
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
            'new_patients_this_month' => $this->countPatientsInDateRange(now()->startOfMonth(), now()->endOfMonth()),
            'percent_diff_new_patients_month' => $this->getPercentDiffNewPatientsThisAndLastMonth(),
            'admissions_by_week' => $this->getWeeklyAdmissions(),
            'admissions_without_bed' => Admission::whereNull('bed_id')->active()->with('patient')->get(),
            'admissions_discharged_by_week' => $this->getWeeklyAdmissions(false),
            'patients_by_ars' => $this->getPatientsByArs(),
            'beds_by_status' => $this->getBedsByStatus(),
            'upcoming_medications' => $this->getUpcomingMedications(),
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

    private function countPatientsInDateRange($startOfMonth, $endtOfMonth)
    {
        return Patient::whereBetween('created_at', [$startOfMonth, $endtOfMonth])->count();
    }

    private function getPercentDiffNewPatientsThisAndLastMonth()
    {
        $totalThisMonth = $this->countPatientsInDateRange(now()->startOfMonth(), now()->endOfMonth());
        $totalLastMonth = $this->countPatientsInDateRange(now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth());

        if ($totalLastMonth === 0) {
            return 100;
        }
        return ($totalThisMonth * 100) / $totalLastMonth;
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

    private function getUpcomingMedications()
    {
        $notifications = DB::table('medication_notifications as n')
            ->join('medication_record_details as d', 'n.medication_record_detail_id', '=', 'd.id')
            ->select(
                'n.id as id',
                'n.medication_record_detail_id as detail_id',
                'n.scheduled_time as scheduled',
                'd.medication_record_id as medication_record_id',
                DB::raw("CONCAT_WS(' ', d.drug, d.dose, d.route) as medication"),
                DB::raw("ROW_NUMBER() OVER (PARTITION BY n.medication_record_detail_id) as rank_notifications")
            )
            ->whereRaw('n.applied IS NOT TRUE')
            ->where('d.active', true)
            ->whereNull('d.suspended_at')
            ->orderBy('scheduled')
            ->orderBy('detail_id');

        // Convertir la subconsulta en una consulta externa usando una subconsulta
        $pendingMedications = DB::table(DB::raw("({$notifications->toSql()}) as subquery"))
            ->mergeBindings($notifications)
            ->join('medication_records as r', 'subquery.medication_record_id', '=', 'r.id')
            ->join('admissions as a', 'r.admission_id', '=', 'a.id')
            ->join('patients as p', 'a.patient_id', '=', 'p.id')
            ->join('beds as b', 'a.bed_id', '=', 'b.id')
            ->select(
                DB::raw("CONCAT_WS(' ', p.first_name, p.first_surname, p.second_surname) as patient_name"),
                'subquery.detail_id',
                'subquery.medication',
                'subquery.scheduled',
                DB::raw("CONCAT(b.room, '-', b.number) as bed")
            )
            ->where('subquery.rank_notifications', 1)
            ->whereNull('a.discharged_date')
            ->whereRaw('(ABS(TIMESTAMPDIFF(HOUR, subquery.scheduled , NOW())) <= 1 OR subquery.scheduled < NOW())')
            ->get();

        return $pendingMedications;
    }

}
