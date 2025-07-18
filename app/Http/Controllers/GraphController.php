<?php

namespace App\Http\Controllers;

use Amenadiel\JpGraph\Graph\Axis;
use App\Models\EliminationRecord;
use App\Models\TemperatureRecord;
use Illuminate\Http\Request;
use App\Models\TemperatureDetail;
use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\LinePlot;
use Amenadiel\JpGraph\Plot\PlotLine;
use Amenadiel\JpGraph\Text\Text;
use Amenadiel\JpGraph\Util\DateScaleUtils;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GraphController extends Controller
{
    public function generateGraph($id)
    {
        $details = $this->getTemperatureDetails($id);
        $eliminations = $this->getTemperatureEliminations($id);

        // Obtener la fecha de admisión
        $admissionDate = $this->getAdmissionDate($id);

        if (!$admissionDate) {
            return $this->jsonResponse('No se pudo obtener la fecha de admisión.', null, 400);
        }

        if ($details->isEmpty()) {
            return $this->jsonResponse('No hay datos disponibles para generar el gráfico.', null, 400);
        }

        $timestamps = $details->pluck('updated_at')->map(fn($date) => $date->timestamp)->toArray();
        $dataY = $details->pluck('temperature')->toArray();

        if (empty($dataY)) {
            return $this->jsonResponse('No hay datos de temperatura válidos.', null, 400);
        }

        if (count($timestamps) !== count($dataY)) {
            return $this->jsonResponse('Los datos de tiempo y temperatura no coinciden. Verifica la integridad de los datos.', null, 400);
        }

        $graphPath = $this->prepareGraphPath();

        $graph = $this->createGraph($timestamps, $dataY);

        $this->addVerticalLines($graph, $timestamps);
        $this->drawTopTable($graph, $timestamps, $admissionDate); // Pasar fecha de admisión
        $this->drawTable($graph, $eliminations, $timestamps);

        return $this->saveGraph($graph, $graphPath);
    }

    private function getTemperatureDetails($id)
    {
        return TemperatureDetail::where('temperature_record_id', $id)
            ->orderBy('updated_at', 'asc')
            ->get(['id', 'temperature', 'updated_at']);
    }

    private function getTemperatureEliminations($id)
    {
        return EliminationRecord::where('temperature_record_id', $id)
            ->orderBy('updated_at', 'asc')
            ->get(['id', 'urinations', 'evacuations', 'updated_at']);
    }

    private function getAdmissionDate($temperatureRecordId)
    {
        // Asumiendo que TemperatureRecord tiene una relación con Admission
        $temperatureRecord = TemperatureRecord::with('admission')->find($temperatureRecordId);

        if (!$temperatureRecord || !$temperatureRecord->admission) {
            return null;
        }

        return $temperatureRecord->admission->created_at;
    }

    private function jsonResponse($message, $path, $status)
    {
        return response()->json([
            'message' => $message,
            'path' => $path
        ], $status);
    }

    private function prepareGraphPath()
    {
        $graphPath = storage_path('app/public/temp_chart.jpg');

        if (file_exists($graphPath)) {
            unlink($graphPath);
        }

        return $graphPath;
    }

    private function createGraph($timestamps, $dataY)
    {
        $graph = new Graph(1500, 750);

        $graph->SetScale("datlin");
        $graph->xaxis->scale->SetTimeAlign(DAYADJ_1);
        $graph->SetMargin(120, 90, 70, 90);

        $turnTimestamps = $this->generateTurnTimestamps($timestamps);

        $timeRange = max($timestamps) - min($timestamps);
        if ($timeRange >= 86400) { // 86400 segundos = 1 día
            $graph->xaxis->SetTickPositions($turnTimestamps, $turnTimestamps);
        } else {
            $graph->xaxis->HideFirstLastLabel();
            // $graph->xaxis->SetTextTickInterval(7);
        }

        $graph->xaxis->SetPos("max");
        $graph->xaxis->SetLabelFormatCallback(function ($timestamp) {
            return date('H', $timestamp);
        });

        $graph->yaxis->scale->SetAutoMin(35);
        $graph->yaxis->scale->SetAutoMax(42.9);

        $lineplot = new LinePlot($dataY, $timestamps);
        $lineplot->SetColor("black");
        $lineplot->SetWeight(4);
        $lineplot->mark->SetType(MARK_FILLEDCIRCLE);
        $lineplot->mark->SetColor('white');
        $lineplot->mark->SetFillColor('black');
        $lineplot->mark->SetSize(4);
        $graph->Add($lineplot);

        $hline = new PlotLine(HORIZONTAL, 37.0, 'red');
        $graph->AddLine($hline);

        return $graph;
    }

    private function drawTable($graph, $eliminations, $timestamps)
    {
        $tableHeight = 40;
        $rowHeight = 20;
        $margin = 30;

        $yTableTop = $graph->img->height - $margin - $tableHeight;
        $yTableMiddle = $graph->img->height - $margin - $rowHeight;
        $yTableBottom = $graph->img->height - $margin;

        $this->drawTableBorders($graph, $yTableTop, $yTableMiddle, $yTableBottom);

        $headerUrinations = new Text("Micciones", $graph->img->left_margin - 30, $yTableTop + ($rowHeight / 2));
        $headerUrinations->SetAlign('right', 'center');
        $graph->AddText($headerUrinations);

        $headerEvacuations = new Text("Evacuaciones", $graph->img->left_margin - 30, $yTableBottom - ($rowHeight / 2));
        $headerEvacuations->SetAlign('right', 'center');
        $graph->AddText($headerEvacuations);

        $turnTimestamps = $this->generateTurnTimestamps($timestamps);
        $timestampRange = max($timestamps) - min($timestamps);

        if ($eliminations->isEmpty()) {
            $graph->img->Line($graph->img->left_margin + $graph->img->plotwidth, $yTableTop, $graph->img->left_margin + $graph->img->plotwidth, $yTableBottom);
            return;
        }


        // si solo hay un registro
        if ($timestampRange == 0) {
            $centerXPos = $graph->img->left_margin + ($graph->img->plotwidth / 2);

            $currentTurnData = $eliminations[0];
            $graph->img->Line($graph->img->left_margin + $graph->img->plotwidth, $yTableTop, $graph->img->left_margin + $graph->img->plotwidth, $yTableBottom);

            $this->addTableText($graph, $currentTurnData, $centerXPos, $yTableTop, $yTableBottom, $rowHeight);
        } else {

            foreach ($turnTimestamps as $i => $turnTimestamp) {

                $xPos = $this->calculateXPos($graph, $timestamps, $turnTimestamp);
                $graph->img->Line($xPos, $yTableTop, $xPos, $yTableBottom);

                if ($i < count($turnTimestamps) - 1) {
                    $currentTurnData = $eliminations->first(function ($detail) use ($turnTimestamps, $i) {
                        $timestamp = strtotime($detail->updated_at);
                        return $timestamp >= $turnTimestamps[$i] && $timestamp < $turnTimestamps[$i + 1];
                    });

                    if ($currentTurnData) {

                        $nextXPos = $this->calculateXPos($graph, $timestamps, $turnTimestamps[$i + 1]);
                        $cellCenter = ($xPos + $nextXPos) / 2;

                        // si no hay espacio suficiente
                        if ($xPos !== null && abs($xPos - $nextXPos) < 20) {

                            // verificar si es el primer registro
                            if ($currentTurnData->id == $eliminations[0]->id) {
                                $graph->img->SetColor('white');
                                $graph->img->Line($graph->img->left_margin, $yTableTop, $graph->img->left_margin, $yTableBottom);
                                $graph->img->SetColor('black');
                                $graph->img->Line($graph->img->left_margin - 20, $yTableTop, $graph->img->left_margin - 20, $yTableBottom);
                                $this->addTableText($graph, $currentTurnData, $cellCenter, $yTableTop, $yTableBottom, $rowHeight, true, false);
                            } else {
                                $newXPos = $graph->img->left_margin + $graph->img->plotwidth + 40;
                                $graph->img->SetColor('black');

                                $graph->img->Line($newXPos, $yTableTop, $newXPos, $yTableBottom);
                                $graph->img->Line($newXPos - 40, $yTableTop, $newXPos, $yTableTop);
                                $graph->img->Line($newXPos - 40, $yTableMiddle, $newXPos, $yTableMiddle);
                                $graph->img->Line($newXPos - 40, $yTableBottom, $newXPos, $yTableBottom);
                                $this->addTableText($graph, $currentTurnData, $cellCenter, $yTableTop, $yTableBottom, $rowHeight, false, true);
                                $graph->img->SetColor('white');
                            }
                        } else {
                            $this->addTableText($graph, $currentTurnData, $cellCenter, $yTableTop, $yTableBottom, $rowHeight);
                        }
                    }
                }
            }
        }
    }

    private function drawTableBorders($graph, $yTableTop, $yTableMiddle, $yTableBottom)
    {
        $graph->img->SetColor('black');
        $graph->img->Line(1, $yTableTop, 1, $yTableBottom);
        $graph->img->Line(1, $yTableTop, $graph->img->width - $graph->img->right_margin, $yTableTop);
        $graph->img->Line(1, $yTableMiddle, $graph->img->width - $graph->img->right_margin, $yTableMiddle);
        $graph->img->Line(1, $yTableBottom, $graph->img->width - $graph->img->right_margin, $yTableBottom);
        $graph->img->Line($graph->img->left_margin, $yTableTop, $graph->img->left_margin, $yTableBottom);
    }

    private function calculateXPos($graph, $timestamps, $turnTimestamp)
    {
        $timestampRange = max($timestamps) - min($timestamps);
        if ($timestampRange == 0) {
            return $graph->img->left_margin + $graph->img->plotwidth;
        }
        $xPos = $graph->img->left_margin + ($turnTimestamp - min($timestamps)) * ($graph->img->plotwidth / $timestampRange);
        return max($graph->img->left_margin, min($xPos, $graph->img->width - $graph->img->right_margin));
    }

    private function addTableText($graph, $currentTurnData, $cellCenter, $yTableTop, $yTableBottom, $rowHeight, $startMinSpace = false, $endMinSpace = false)
    {
        if ($startMinSpace) {
            $cellCenter = $graph->img->left_margin - 4;
        } elseif ($endMinSpace) {
            $cellCenter += 20;
        }

        $textUrinations = new Text(strval($currentTurnData->urinations), $cellCenter, $yTableTop + ($rowHeight / 2));
        $textUrinations->SetAlign('center', 'center');
        $graph->AddText($textUrinations);

        $textEvacuations = new Text(strval($currentTurnData->evacuations), $cellCenter, $yTableBottom - ($rowHeight / 2));
        $textEvacuations->SetAlign('center', 'center');
        $graph->AddText($textEvacuations);
    }

    private function saveGraph($graph, $graphPath)
    {
        try {
            $graph->Stroke($graphPath);

            return $this->jsonResponse('Gráfico generado con éxito.', asset('storage/temp_chart.jpg'), 200);
        } catch (\Exception $e) {
            Log::error("Error al generar el gráfico: " . $e->getMessage());
            return $this->jsonResponse('Error al generar el gráfico: ' . $e->getMessage(), null, 500);
        }
    }

    private function addVerticalLines($graph, $timestamps)
    {
        // Obtener el primer y último día
        $firstDay = date('Y-m-d', min($timestamps));
        $lastDay = date('Y-m-d', max($timestamps));

        // Generar array con todos los días entre el primero y el último
        $allDays = [];
        $currentDay = $firstDay;
        while ($currentDay <= $lastDay) {
            $allDays[] = $currentDay;
            $currentDay = date('Y-m-d', strtotime($currentDay . ' +1 day'));
        }

        $minTimestamp = min($timestamps);
        $maxTimestamp = max($timestamps);

        foreach ($allDays as $day) {
            $this->addDayLines($graph, $day, $minTimestamp, $maxTimestamp);
        }
    }

    private function addDayLines($graph, $day, $minTimestamp, $maxTimestamp)
    {
        $midnight = strtotime($day . ' 00:00:00');

        // Agregar línea vertical negra a medianoche
        if ($midnight >= $minTimestamp && $midnight <= $maxTimestamp) {
            $graph->AddLine(new PlotLine(VERTICAL, $midnight, 'black', 2));
        }

        // Agregar líneas verticales grises para cada turno
        $turns = ['07:00:00', '14:00:00', '21:00:00'];
        foreach ($turns as $turn) {
            $turnTimestamp = strtotime($day . ' ' . $turn);
            if ($turnTimestamp >= $minTimestamp && $turnTimestamp <= $maxTimestamp) {
                $graph->AddLine(new PlotLine(VERTICAL, $turnTimestamp, 'lightgray', 1));
            }
        }
    }

    private function generateTurnTimestamps($timestamps)
    {
        if (empty($timestamps)) {
            return [];
        }

        $uniqueDays = array_unique(array_map(fn($timestamp) => date('Y-m-d', $timestamp), $timestamps));
        // Obtener el primer y el último día
        $firstDay = strtotime($uniqueDays[0]);
        $lastDay = strtotime(end($uniqueDays));

        // Generar una lista completa de días entre el primer y el último día
        $completeDays = [];
        for ($day = $firstDay; $day <= $lastDay; $day = strtotime('+1 day', $day)) {
            $completeDays[] = date('Y-m-d', $day);
        }

        // Asegurarse de que todos los días únicos estén presentes en la lista completa
        $uniqueDays = array_unique(array_merge($completeDays, $uniqueDays));

        // Ordenar los días para asegurarse de que estén en el orden correcto
        sort($uniqueDays);

        // Agregar un día antes del primer día y un día después del último día
        array_unshift($uniqueDays, date('Y-m-d', strtotime($uniqueDays[0] . ' -1 day')));
        $uniqueDays[] = date('Y-m-d', strtotime(end($uniqueDays) . ' +1 day'));

        $turnTimestamps = [];
        foreach ($uniqueDays as $day) {
            $turnTimestamps[] = strtotime($day . ' 07:00:00');
            $turnTimestamps[] = strtotime($day . ' 14:00:00');
            $turnTimestamps[] = strtotime($day . ' 21:00:00');
        }
        return $turnTimestamps;
    }

    private function drawTopTable($graph, $timestamps, $admissionDate)
    {
        $tableHeight = 40;
        $rowHeight = 20;
        $margin = 10;

        $yTableTop = $margin;
        $yTableMiddle = $margin + $rowHeight;
        $yTableBottom = $margin + $tableHeight;

        $this->drawTopTableBorders($graph, $yTableTop, $yTableMiddle, $yTableBottom);

        $headerDays = new Text("Fecha", $graph->img->left_margin - 40, $yTableTop + ($rowHeight / 2));
        $headerDays->SetAlign('right', 'center');
        $graph->AddText($headerDays);

        $headerDays = new Text("Dias Hosp.", $graph->img->left_margin - 40, $yTableMiddle + ($rowHeight / 2));
        $headerDays->SetAlign('right', 'center');
        $graph->AddText($headerDays);

        // Obtener el primer y último día de los timestamps
        $firstDay = date('Y-m-d', min($timestamps));
        $lastDay = date('Y-m-d', max($timestamps));

        // Generar array con todos los días entre el primero y el último
        $allDays = [];
        $currentDay = $firstDay;
        while ($currentDay <= $lastDay) {
            $allDays[] = $currentDay;
            $currentDay = date('Y-m-d', strtotime($currentDay . ' +1 day'));
        }

        $timestampRange = max($timestamps) - min($timestamps);

        // Convertir la fecha de admisión a formato de fecha para comparar
        $admissionDateFormatted = $admissionDate->format('Y-m-d');

        // si solo hay un registro
        if ($timestampRange == 0) {
            $centerXPos = $graph->img->left_margin + ($graph->img->plotwidth / 2);
            $graph->img->Line($graph->img->left_margin + $graph->img->plotwidth, $yTableTop, $graph->img->left_margin + $graph->img->plotwidth, $yTableBottom);

            $graph->img->Line($graph->img->left_margin, $yTableTop, $graph->img->left_margin, $yTableBottom);
            $graph->img->SetColor('black');
            $textDay = new Text($allDays[0], $centerXPos, $yTableTop + ($rowHeight / 2));
            $textDay->SetAlign('center', 'center');
            $graph->AddText($textDay);

            // Calcular días desde la admisión
            $daysSinceAdmission = $this->calculateDaysFromAdmission($allDays[0], $admissionDateFormatted);
            $dayText = $daysSinceAdmission == 0 ? 'ADM' : strval($daysSinceAdmission);

            $textDay = new Text($dayText, $centerXPos, $yTableBottom - ($rowHeight / 2));
            $textDay->SetAlign('center', 'center');
            $graph->AddText($textDay);

        } else {

            foreach ($allDays as $i => $day) {
                $firstLowSpace = false;
                $xPos = $this->calculateXPos($graph, $timestamps, strtotime($day . ' 00:00:00'));

                $nextXPos = $this->calculateXPos($graph, $timestamps, strtotime($day . ' +1 day 00:00:00'));
                $cellCenter = ($xPos + $nextXPos) / 2;

                $graph->img->Line($xPos, $yTableTop, $xPos, $yTableBottom);

                // si hay poco espacio para la casilla
                if ($xPos !== null && abs($xPos - $nextXPos) < 70) {
                    if ($i == 0) {
                        $cellCenter -= 10;
                        $firstLowSpace = true;
                        $graph->img->SetColor('black');
                        $graph->img->Line($graph->img->left_margin - 20, $yTableTop, $graph->img->left_margin - 20, $yTableBottom);
                        $graph->img->Line($nextXPos, $yTableTop, $nextXPos, $yTableBottom);

                        $graph->img->SetColor('white');
                        $graph->img->Line($graph->img->left_margin, $yTableTop, $graph->img->left_margin, $yTableBottom);
                        $graph->img->SetColor('black');
                    } else {
                        $graph->img->SetColor('black');
                        $cellCenter += 45;
                        $newXPos = $graph->img->left_margin + $graph->img->plotwidth + 80;
                        $graph->img->Line($newXPos, $yTableTop, $newXPos, $yTableBottom);
                        $graph->img->Line($newXPos - 80, $yTableTop, $newXPos - 80, $yTableBottom);
                        $graph->img->Line($newXPos - 80, $yTableTop, $newXPos, $yTableTop);
                        $graph->img->Line($newXPos - 80, $yTableMiddle, $newXPos, $yTableMiddle);
                        $graph->img->Line($newXPos - 80, $yTableBottom, $newXPos, $yTableBottom);
                        $graph->img->SetColor('white');

                        if (round($graph->img->left_margin + $graph->img->plotwidth) != round($xPos)) {
                            $graph->img->Line($graph->img->left_margin + $graph->img->plotwidth, $yTableTop, $graph->img->left_margin + $graph->img->plotwidth, $yTableBottom);
                        }
                    }
                }

                if ($i >= 1) {
                    $graph->img->SetColor('black');
                }

                // Mostrar la fecha
                if ($firstLowSpace) {
                    $textDay = new Text(date('d', strtotime($day)), $cellCenter, $yTableTop + ($rowHeight / 2));
                    $textDay->SetAlign('center', 'center');
                    $graph->AddText($textDay);
                } else {
                    $textDay = new Text($day, $cellCenter, $yTableTop + ($rowHeight / 2));
                    $textDay->SetAlign('center', 'center');
                    $graph->AddText($textDay);
                }

                // Calcular días desde la admisión y mostrar el día de hospitalización
                $daysSinceAdmission = $this->calculateDaysFromAdmission($day, $admissionDateFormatted);
                $dayText = $daysSinceAdmission == 0 ? 'ADM' : strval($daysSinceAdmission);

                $textDay = new Text($dayText, $cellCenter, $yTableMiddle + ($rowHeight / 2));
                $textDay->SetAlign('center', 'center');
                $graph->AddText($textDay);
            }
        }
    }

    private function calculateDaysFromAdmission($currentDay, $admissionDate)
    {
        $currentTimestamp = strtotime($currentDay);
        $admissionTimestamp = strtotime($admissionDate);

        $daysDifference = floor(($currentTimestamp - $admissionTimestamp) / (24 * 60 * 60));

        return max(0, $daysDifference); // Asegurar que no sea negativo
    }

    private function drawTopTableBorders($graph, $yTableTop, $yTableMiddle, $yTableBottom)
    {

        $graph->img->SetColor('black');
        $graph->img->Line(1, $yTableTop, 1, $yTableBottom);
        $graph->img->Line(1, $yTableTop, $graph->img->width - $graph->img->right_margin, $yTableTop);
        $graph->img->Line(1, $yTableMiddle, $graph->img->width - $graph->img->right_margin, $yTableMiddle);
        $graph->img->Line(1, $yTableBottom, $graph->img->width - $graph->img->right_margin, $yTableBottom);
        $graph->img->Line($graph->img->left_margin + $graph->img->plotwidth, $yTableTop, $graph->img->left_margin + $graph->img->plotwidth, $yTableBottom);
    }
}
