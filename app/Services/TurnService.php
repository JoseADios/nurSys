<?php

// app/Services/TurnService.php

namespace App\Services;

use Carbon\Carbon;

class TurnService
{
    public function getCurrentTurn()
    {
        $turns = [
            '0-8' => [0, 8],
            '8-16' => [8, 16],
            '16-24' => [16, 24],
        ];
        $currentHour = Carbon::now()->hour;

        foreach ($turns as $key => $turn) {
            if ($currentHour >= $turn[0] && $currentHour < $turn[1]) {
                return $turns[$key];
            }
        }
        return null;
    }
}
