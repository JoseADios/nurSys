<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\EliminationRecord;
use App\Models\NurseRecord;
use App\Models\MedicationRecord;
use App\Models\MedicationRecordDetail;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\Admission;
use App\Models\NurseRecordDetail;
use App\Models\Patient;
use App\Models\TemperatureDetail;
use App\Models\MedicationNotification;
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
            ->where('active', true)
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

        // return view('reports.nurse_record', [
        //     'nurseRecord' => $nurseRecord,
        //     'clinic' => $clinic,
        //     'details' => $details,
        //     'nurseSignaturePath' => $nurseSignaturePath,
        //     'graphPath' => asset('storage/temp_chart.jpg')
        // ]);

    }
    public function admissionReport($id)
    {
        $admission = Admission::findOrFail($id);


        $clinic = Clinic::get()->first();



        if ($admission->active != true) {
            return Redirect::route('admissions.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        $pdf = Pdf::loadView('reports.admission', [
            'admission' => $admission,
            'clinic' => $clinic,


        ])->setPaper('a4');


        return $pdf->stream('ficha_de_medicamentos.pdf');



    }
    public function medicationRecordReport($id)
    {
        $medicationRecord = medicationRecord::findOrFail($id);


        $clinic = Clinic::get()->first();
        $details = medicationRecordDetail::where('medication_record_id', $id)->with('medicationNotification')->get();

        foreach ($details as $detail) {
            $notifications = MedicationNotification::where('medication_record_detail_id', $detail->id)->with('medicationRecordDetail')->get();
        }


        if ($medicationRecord->active != true) {
            return Redirect::route('medicationRecords.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        $pdf = Pdf::loadView('reports.medication_record', [
            'medicationRecord' => $medicationRecord,
            'clinic' => $clinic,
            'details' => $details,
            'notification'=>$notifications

        ])->setPaper('a4');


        return $pdf->stream('ficha_de_medicamentos.pdf');



    }
    public function medicalOrderReport($id)
    {
        $MedicalOrder = MedicalOrder::findOrFail($id);


        $clinic = Clinic::get()->first();
        $details = MedicalOrderDetail::where('medical_order_id', $id)->get();




        if ($MedicalOrder->active != true) {
            return Redirect::route('MedicalOrders.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        $pdf = Pdf::loadView('reports.medical_order', [
            'MedicalOrder' => $MedicalOrder,
            'clinic' => $clinic,
            'details' => $details,


        ])->setPaper('a4');


        return $pdf->stream('ficha_de_medicamentos.pdf');



    }
}
