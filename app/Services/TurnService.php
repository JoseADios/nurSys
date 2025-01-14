<?php

// app/Services/TurnService.php

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
                    return $turn;
                }
            } else {
                if ($currentHour >= $turn[0] || $currentHour < $turn[1]) {
                    return $turn;
                }
            }
        }

        return null;
    }
}
