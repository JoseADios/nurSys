<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Services\PDF\PatientReport;
use App\Services\PDF\TemperatureRecordReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{

    // TEMPERATURE RECORD

    public function temperatureRecordReport($id)
    {
        $temperatureRecord = TemperatureRecord::where('id', $id)
            ->with('admission', 'admission.patient', 'admission.bed', 'nurse')->first();

        $details = TemperatureDetail::where('temperature_record_id', $id)->get();

        $pdf = Pdf::loadView('reports.temperature_record', compact('temperatureRecord', 'details'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('hoja_temperatura.pdf');
    }
}
