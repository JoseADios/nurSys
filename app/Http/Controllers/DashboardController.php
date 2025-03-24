<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\MedicalOrder;
use App\Models\NurseRecord;
use App\Models\Patient;
use App\Services\TurnService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $timeFilter = $request->input('time_filter', 'week');

        // Obtener estadísticas generales
        $stats = [
            'total_admissions' => Admission::count(),
            'active_admissions' => Admission::whereNull('discharged_date')->count(),
            'available_beds' => Bed::all()->filter->isAvailable()->count(),
            'total_beds' => Bed::count(),
            'new_patients_this_month' => $this->countPatientsInDateRange(now()->startOfMonth(), now()->endOfMonth()),
            'percent_diff_new_patients_month' => $this->getPercentDiffNewPatientsThisAndLastMonth(),
            'admissions_by_time' => $this->getAdmissionsByTimeFilter($timeFilter),
            'admissions_discharged_by_time' => $this->getAdmissionsByTimeFilter($timeFilter, false),
            'admissions_without_bed' => Admission::whereNull('bed_id')->active()->with('patient')->get(),
            'patients_by_ars' => $this->getPatientsByArs(),
            'beds_by_status' => $this->getBedsByStatus(),
            'upcoming_medications' => $this->getUpcomingMedications(),
            'patients_high_temperature' => $this->getPatientsHighTemperature(),
            'top_doctors_most_admissions' => $this->getTop3DoctorsWithMostAdmissions(),
            'pending_docs' => $this->getPendingDocumentsForCurrentUser(),
            'user_role' => Auth::user()->roles[0]->name,
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats
        ]);
    }

    public function getChartData(Request $request)
    {
        $timeFilter = $request->input('time_filter', 'week');

        return response()->json([
            'admissions_by_time' => $this->getAdmissionsByTimeFilter($timeFilter),
            'admissions_discharged_by_time' => $this->getAdmissionsByTimeFilter($timeFilter, false),
        ]);
    }

    private function getAdmissionsByTimeFilter($timeFilter = 'week', $activeAdmissions = true)
    {
        // Determinar fechas de inicio y fin según el filtro
        switch ($timeFilter) {
            case 'all':
                // No ponemos límites de fecha para obtener todos los registros
                $startDate = null;
                $endDate = null;
                $groupBy = 'year'; // Agrupar por año
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
                $groupBy = 'date'; // Agrupar por día dentro del mes
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                $groupBy = 'month'; // Agrupar por mes dentro del año
                break;
            case 'week':
            default:
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                $groupBy = 'date'; // Agrupar por día dentro de la semana
                break;
        }

        $query = Admission::query();

        // Aplicar el filtro de fecha solo si las fechas no son nulas
        if ($startDate && $endDate) {
            if (!$activeAdmissions) {
                $query->whereNotNull('discharged_date')
                    ->whereBetween('discharged_date', [$startDate, $endDate]);
            } else {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        } else {
            // Si estamos obteniendo todos los registros
            if (!$activeAdmissions) {
                $query->whereNotNull('discharged_date');
            }
        }

        // Seleccionar y agrupar según el filtro de tiempo
        if ($timeFilter === 'all') {
            // Agrupar por año para todos los registros
            if (!$activeAdmissions) {
                $query->selectRaw('YEAR(discharged_date) as year, COUNT(*) as total');
                $query->groupBy('year');
                $query->orderBy('year');
            } else {
                $query->selectRaw('YEAR(created_at) as year, COUNT(*) as total');
                $query->groupBy('year');
                $query->orderBy('year');
            }

            return $query->get()
                ->map(function ($item) {
                    // Crear una fecha con el primer día del año para mantener coherencia
                    $date = Carbon::createFromDate($item->year, 1, 1)->toDateString();
                    return [
                        'date' => $date,
                        'total' => $item->total,
                        'year' => $item->year // Añadir el año explícitamente para facilitar el formateo
                    ];
                });
        } elseif ($timeFilter === 'year') {
            // Código existente para el filtro anual
            if (!$activeAdmissions) {
                $query->selectRaw('MONTH(discharged_date) as month, COUNT(*) as total');
                $query->groupBy('month');
                $query->orderBy('month');
            } else {
                $query->selectRaw('MONTH(created_at) as month, COUNT(*) as total');
                $query->groupBy('month');
                $query->orderBy('month');
            }

            return $query->get()
                ->map(function ($item) {
                    $date = Carbon::create(now()->year, $item->month, 1)->toDateString();
                    return [
                        'date' => $date,
                        'total' => $item->total
                    ];
                });
        } else {
            // Código existente para filtros de semana y mes
            if (!$activeAdmissions) {
                $query->selectRaw('DATE(discharged_date) as date, COUNT(*) as total');
            } else {
                $query->selectRaw('DATE(created_at) as date, COUNT(*) as total');
            }

            $query->groupBy('date');
            $query->orderBy('date');

            return $query->get()
                ->map(function ($item) {
                    return [
                        'date' => $item->date,
                        'total' => $item->total
                    ];
                });
        }
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

    private function getPatientsHighTemperature()
    {
        $details = DB::table('temperature_details as d')
            ->select()
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY d.temperature_record_id ORDER BY d.updated_at DESC, d.id DESC) AS rn');

        $highTempPatients = DB::table(DB::raw("({$details->toSql()}) as sb"))
            ->mergeBindings($details)
            ->leftJoin('temperature_records AS r', 'sb.temperature_record_id', '=', 'r.id')
            ->leftJoin('admissions AS a', 'r.admission_id', '=', 'a.id')
            ->leftJoin('patients AS p', 'a.patient_id', '=', 'p.id')
            ->leftJoin('beds AS b', 'a.bed_id', '=', 'b.id')
            ->select(
                'sb.id',
                'sb.temperature_record_id',
                'sb.temperature',
                'sb.updated_at',
                DB::raw("CONCAT_WS(' ', p.first_name, p.first_surname, p.second_surname) AS patient_name"),
                DB::raw("COALESCE(NULL, CONCAT( b.room, '-', b.number)) AS bed")
            )
            ->where('rn', '=', 1)
            ->where('sb.temperature', '>=', 38)
            ->where('a.active', true)
            ->where('r.active', true)
            ->whereNull('a.discharged_date')
            ->get()
        ;

        return $highTempPatients;
    }

    private function getTop3DoctorsWithMostAdmissions()
    {
        $query = DB::table('admissions as a')
            ->join('users as u', 'a.doctor_id', '=', 'u.id')
            ->selectRaw('u.id, CONCAT_WS(" ", u.name, u.last_name) AS doctor, u.specialty, u.profile_photo_path, COUNT(a.id) AS cant')
            ->whereBetween('a.created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->groupBy('u.id', 'doctor', 'u.specialty', 'u.profile_photo_path')
            ->orderByDesc('cant')
            ->limit(3);

        return $query->get();
    }

    private function getPendingDocumentsForCurrentUser()
    {
        $userRole = Auth::user()->roles[0]->name;
        $pendingOrders = null;
        $pendingNurseR = null;

        if ($userRole === 'doctor' || $userRole === 'admin') {
            $pendingOrders = MedicalOrder::query()
                ->with('admission', 'admission.bed', 'admission.patient')
                ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
                ->where('doctor_id', Auth::id())
                ->whereNull('doctor_sign')
                ->get()
            ;
        }

        if ($userRole === 'nurse' || $userRole === 'admin') {
            $turnService = new TurnService();
            $currentTurn = $turnService->getCurrentTurn();
            $dateRange = $turnService->getDateRangeForTurn($currentTurn);

            $pendingNurseR = NurseRecord::query()
                ->with('admission', 'admission.bed', 'admission.patient')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->where('nurse_id', Auth::id())
                ->whereNull('nurse_sign')
                ->get()
            ;
        }

        if ($userRole === 'admin') {
            return $pendingOrders->merge($pendingNurseR);
        } elseif ($userRole === 'doctor') {
            return $pendingOrders;
        } elseif ($userRole === 'nurse') {
            return $pendingNurseR;
        }

    }

}
