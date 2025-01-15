<?php

namespace App\Services;

use Carbon\Carbon;

class TurnService
{
    protected $turns = [
        '7-14' => [7, 14],
        '14-21' => [14, 21],
        '21-7' => [21, 7],
    ];

    public function getCurrentTurn()
    {
        $currentHour = Carbon::now()->hour;

        foreach ($this->turns as $key => $turn) {
            if ($turn[0] < $turn[1]) {
                if ($currentHour >= $turn[0] && $currentHour < $turn[1]) {
                    return [
                        'hours' => $turn,
                        'crosses_midnight' => false
                    ];
                }
            } else {
                if ($currentHour >= $turn[0] || $currentHour < $turn[1]) {
                    return [
                        'hours' => $turn,
                        'crosses_midnight' => true
                    ];
                }
            }
        }

        return null;
    }

    public function getDateRangeForTurn($turn)
    {
        $startHour = $turn['hours'][0];
        $endHour = $turn['hours'][1];
        $now = Carbon::now();

        if (!$turn['crosses_midnight']) {
            // Turno normal (ej: 7-14, 14-21)
            return [
                'start' => $now->copy()->startOfDay()->addHours($startHour),
                'end' => $now->copy()->startOfDay()->addHours($endHour)
            ];
        } else {
            // Turno nocturno (21-7)
            $start = $now->copy()->startOfDay()->addHours($startHour);
            $end = $now->copy()->startOfDay()->addHours($endHour)->addDay();

            // Si la hora actual es menor que el fin del turno (ej: 2am),
            // ajustamos el rango al día anterior
            if ($now->hour < $endHour) {
                $start->subDay();
                $end->subDay();
            }

            return [
                'start' => $start,
                'end' => $end
            ];
        }
    }
}
