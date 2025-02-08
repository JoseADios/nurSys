<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\PDF\PatientReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function patientReport($id) {
        $patient = Patient::findOrFail($id);
        $pdf = (new PatientReport())->generate($patient);

        return response()->streamDownload(
            fn () => $pdf->Output(),
            "reporte_paciente.pdf"
        );
    }
}
