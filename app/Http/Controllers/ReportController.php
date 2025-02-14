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
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{

    // TEMPERATURE RECORD

    public function temperatureRecordReport($id)
    {
        // Llamar a GraphController para generar el gráfico
        app('App\Http\Controllers\GraphController')->generateGraph($id);

        $temperatureRecord = TemperatureRecord::findOrFail($id);
        $clinic = Clinic::get()->first();
        $details = TemperatureDetail::where('temperature_record_id', $id)
        ->get('id');

        if ($details->isEmpty()) {
            return Redirect::route('temperatureRecords.show', $id)->with('error', 'Este registro no tiene detalles');
        }

        if ($temperatureRecord->active != true) {
            return Redirect::route('temperatureRecords.index')->with('error', 'Este registro ha sido eliminado');
        }

        return view('reports.temperature_record', [
            'temperatureRecord' => $temperatureRecord,
            'clinic' => $clinic,
            'graphPath' => asset('storage/temp_chart.jpg')]);


        // Generar el PDF con la imagen del gráfico
        // $pdf = Pdf::loadView('reports.temperature_record', [
        //     'temperatureRecord' => $temperatureRecord,
        //     'clinic' => $clinic,
        //     'graphPath' => asset('storage/temp_chart.jpg')
        // ])->setPaper('letter', 'landscape');

        // return $pdf->stream('hoja_de_temperatura.pdf');
    }
}
