<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\NurseRecord;
use App\Models\MedicationRecord;
use App\Models\MedicationRecordDetail;
use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\NurseRecordDetail;
use App\Models\TemperatureDetail;
use App\Models\MedicationNotification;
use App\Models\TemperatureRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    use AuthorizesRequests;
    // TEMPERATURE RECORD

    public function temperatureRecordReport($id)
    {

        // Llamar a GraphController para generar el gráfico
        app('App\Http\Controllers\GraphController')->generateGraph($id);

        $temperatureRecord = TemperatureRecord::findOrFail($id);

        $this->authorize('view', $temperatureRecord);
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
        $this->authorize('view', $nurseRecord);

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
    public function medicationRecordReport($id)
    {
        $medicationRecord = medicationRecord::findOrFail($id);

        $this->authorize('view', $medicationRecord);

        $clinic = Clinic::get()->first();
        $details = medicationRecordDetail::where('medication_record_id', $id)
            ->where('active', true)
            ->with('medicationNotification')->get();
        if (!$details) {
            return Redirect::route('medicationRecords.index')->with('flash.toast', 'Esta Ficha de Medicamentos no tiene detalles')->with('flash.toastStyle', 'danger');
        }
        $hasNotifications = false;
        $notifications = collect();
        foreach ($details as $detail) {
            $notifications = MedicationNotification::where('medication_record_detail_id', $detail->id)->with('medicationRecordDetail')->get();
            if ($notifications->isEmpty()) {
                return Redirect::route('medicationRecords.index')->with('flash.toast', 'Esta Ficha de Medicamentos  no tiene notificaciones')->with('flash.toastStyle', 'danger');
            }

            $hasNotifications = $notifications->count() > 0;

            if ($hasNotifications) {
                break;
            }
        }


        if ($medicationRecord->active != true) {
            return Redirect::route('medicationRecords.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        $pdf = Pdf::loadView('reports.medication_record', [
            'medicationRecord' => $medicationRecord,
            'clinic' => $clinic,
            'details' => $details,

            'notification' => $notifications,
            'hasnotifications' => $hasNotifications,


        ])->setPaper('a4');

        return $pdf->stream('ficha_de_medicamentos.pdf');

    }


    public function medicalOrderReport($id)
    {
        $MedicalOrder = MedicalOrder::findOrFail($id);
        $this->authorize('view', $MedicalOrder);

        $clinic = Clinic::get()->first();

        $details = MedicalOrderDetail::where('medical_order_id', $id)->get();

        if ($details) {
            return Redirect::route('medicalOrders.index')->with('flash.toast', 'Esta Orden Medica no tiene detalles')->with('flash.toastStyle', 'danger');
        }
        if ($MedicalOrder->active != true) {
            return Redirect::route('medicalOrders.index')->with('flash.toast', 'Este registro ha sido eliminado')->with('flash.toastStyle', 'danger');
        }

        $pdf = Pdf::loadView('reports.medical_order', [
            'MedicalOrder' => $MedicalOrder,
            'clinic' => $clinic,
            'details' => $details,


        ])->setPaper('a4');

        return $pdf->stream('ficha_de_medicamentos.pdf');
    }
}
