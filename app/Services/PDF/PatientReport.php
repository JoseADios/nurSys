<?php

namespace App\Services\PDF;

class PatientReport extends BasePDF
{
    public function generate($patient)
    {
        $this->SetTitle("Reporte de Paciente");
        $this->SetFont('helvetica', '', 12);
        $this->Ln(30);

        $html = "
        <h3>Datos del Paciente</h3>
        <p><strong>Nombre:</strong> {$patient->first_name} {$patient->last_name} {$patient->first_surname} {$patient->second_surname}</p>
        <p><strong>Telefono:</strong> {$patient->phone}</p>
        <p><strong>Correo:</strong> {$patient->email}</p>
        ";

        $this->writeHTML($html, true, false, true, false, '');
        return $this;
    }
}
