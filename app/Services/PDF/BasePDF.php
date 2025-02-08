<?php

namespace App\Services\PDF;

use App\Models\Clinic;
use TCPDF;

class BasePDF extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
        $this->SetMargins(10, 10, 10);
        $this->SetAutoPageBreak(true, 15);
        $this->AddPage();
    }

    public function header()
    {
        $clinic = Clinic::get()->first();

        $this->Ln(10);
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 1, $clinic->name, 0, 1, 'C');
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 1, $clinic->address, 0, 1, 'C');
        $this->Cell(0, 1, 'Tel.: ' . $clinic->phone. ' • Fax: ' . $clinic->fax, 0, 1, 'C');
        $this->SetFont('helvetica', 'B', 9);
        $this->Cell(0, 1, 'RNC ' . $clinic->rnc, 0, 1, 'C');
    }

    public function footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 10);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}
