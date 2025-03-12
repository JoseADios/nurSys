<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use App\Models\Patient;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Services\PDF\TemperatureRecordReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{

    // TEMPERATURE RECORD

    public function temperatureRecordReport($id)
    {
        // Llamar a GraphController para generar el gráfico
        app('App\Http\Controllers\GraphController')->generateGraph($id);

        $temperatureRecord = TemperatureRecord::findOrFail($id);
        // $nurseSignaturePath = asset('storage/' . $temperatureRecord->nurse_sign);

        $nurseSignaturePath = $temperatureRecord->nurse_sign
            ? public_path('storage/' . $temperatureRecord->nurse_sign)
            : null;

        $clinic = Clinic::get()->first();
        $details = TemperatureDetail::where('temperature_record_id', $id)
            ->get('id');

        if ($details->isEmpty()) {
            return Redirect::route('temperatureRecords.show', $id)->with('flash.toast', 'No se ha registrado ninguna temperatura')->with('flash.toastStyle', 'danger');
        }

        if ($temperatureRecord->active != true) {
            return Redirect::route('temperatureRecords.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        // return view('reports.temperature_record', [
        //     'temperatureRecord' => $temperatureRecord,
        //     'clinic' => $clinic,
        //     'nurseSignaturePath' => $nurseSignaturePath,
        //     'graphPath' => asset('storage/temp_chart.jpg')]);


        // Generar el PDF con la imagen del gráfico
        $pdf = Pdf::loadView('reports.temperature_record', [
            'temperatureRecord' => $temperatureRecord,
            'clinic' => $clinic,
            'nurseSignaturePath' => $nurseSignaturePath,
            'graphPath' => asset('storage/temp_chart.jpg')
        ])->setPaper('letter', 'landscape');

        return $pdf->stream('hoja_de_temperatura.pdf');
    }

    public function nurseRecordReport($id)
    {
        $nurseRecord = NurseRecord::findOrFail($id);

        $nurseSignaturePath = $nurseRecord->nurse_sign
            ? public_path('storage/' . $nurseRecord->nurse_sign)
            : null;

        $clinic = Clinic::get()->first();
        $details = NurseRecordDetail::where('nurse_record_id', $id)
            ->get();

        if ($nurseRecord->active != true) {
            return Redirect::route('nurseRecords.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        $pdf = Pdf::loadView('reports.nurse_record', [
            'nurseRecord' => $nurseRecord,
            'clinic' => $clinic,
            'details' => $details,
            'nurseSignaturePath' => $nurseSignaturePath,
        ])->setPaper('a4');

        // return $pdf->stream('hoja_de_enfermeria'. $nurseRecord->admission->patient->first_name . '.pdf');
        return $pdf->stream('hoja_de_enfermeria.pdf');

        return view('reports.nurse_record', [
            'nurseRecord' => $nurseRecord,
            'clinic' => $clinic,
            'details' => $details,
            'nurseSignaturePath' => $nurseSignaturePath,
            'graphPath' => asset('storage/temp_chart.jpg')
        ]);

    }
}
