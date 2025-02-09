<?php

namespace App\Services\PDF;

class TemperatureRecordReport extends BasePDF
{
    public function generate($temperatureRecord, $details, $imgPath)
    {
        $this->SetTitle("Hoja de temperatura");
        $this->SetFont('helvetica', '', 12);
        $this->Ln(30);
        $svg = '<svg width="300" height="200">
            <rect width="100%" height="100%" fill="white"/>
            <circle cx="150" cy="100" r="80" fill="blue"/>
        </svg>';

        $this->writeHTML($svg, true, false, true, false, '');
        $this->Ln(30);

        $html = "
            <p><strong>Paciente:</strong> {$temperatureRecord->admission->patient->first_name} {$temperatureRecord->admission->patient->last_name}
                {$temperatureRecord->admission->patient->first_surname} {$temperatureRecord->admission->patient->second_surname}</p>
            <p><strong>Dirección:</strong> {$temperatureRecord->admission->patient->address}</p>
            <p><strong>Diagnóstico de impresión:</strong> {$temperatureRecord->impression_diagnosis}</p>
            <p><strong>Sala:</strong> {$temperatureRecord->admission->bed->room}</p>
            <p><strong>Cama no.:</strong> {$temperatureRecord->admission->bed->number}</p>
            <p><strong>Enfermera:</strong> {$temperatureRecord->nurse->name} {$temperatureRecord->nurse->last_name}</p>
            ";

        $this->writeHTML($html, true, false, true, false, '');

        // Agregar el gráfico al PDF
        // if (file_exists($imgPath)) {
        //     $this->Image($imgPath, 30, 120, 140, 70, 'PNG'); // Ajusta posición y tamaño
        // }

        return $this;
    }
}
