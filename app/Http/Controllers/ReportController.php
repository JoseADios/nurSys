<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Services\PDF\PatientReport;
use App\Services\PDF\TemperatureRecordReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    // PATIENT
    public function patientReport($id)
    {
        $patient = Patient::findOrFail($id);
        $pdf = (new PatientReport())->generate($patient);

        return response()->streamDownload(
            fn() => $pdf->Output(),
            "reporte_paciente.pdf"
        );
    }

    // TEMPERATURE RECORD
    public function temperatureRecordReport(Request $request, $id)
    {
        ini_set('memory_limit', '512M');

        // Obtener el registro de temperatura con sus relaciones
        $temperatureRecord = TemperatureRecord::with('admission.patient', 'admission.bed', 'nurse')
            ->find($id);

        if (!$temperatureRecord) {
            return response()->json(['error' => 'Registro de temperatura no encontrado'], 404);
        }

        $details = TemperatureDetail::where('temperature_record_id', $id)->get();
        $imageData = $request->input('img');

        // Validar que la imagen esté presente y en el formato correcto
        if (!isset($imageData['imgURI']) || strpos($imageData['imgURI'], 'data:image') !== 0) {
            return response()->json(['error' => 'Formato de imagen no válido'], 400);
        }

        try {
            // Extraer base64 y decodificar
            $imageData = explode(',', $imageData['imgURI'])[1];
            $imageData = base64_decode($imageData);

            // Guardar en un archivo temporal único
            $imagePath = storage_path('app/public/temperatureChart_' . uniqid() . '.png');
            file_put_contents($imagePath, $imageData);

            // Generar PDF
            $pdf = new TemperatureRecordReport();
            $pdf->generate($temperatureRecord, $details, $imagePath);

            // Eliminar la imagen temporal después de usarla
            unlink($imagePath);

            // Descargar el PDF
            return response()->streamDownload(
                fn() => $pdf->Output(),
                'hoja_temperatura.pdf'
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar la imagen: ' . $e->getMessage()], 500);
        }
    }
}
