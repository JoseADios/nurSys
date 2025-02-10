<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
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

        $clinic = Clinic::get()->first();

        // return view('reports.temperature_record', compact('temperatureRecord', 'details', 'clinic'));

        $pdf = Pdf::loadView('reports.temperature_record', compact('temperatureRecord', 'details', 'clinic'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('hoja_temperatura.pdf');
    }
}
