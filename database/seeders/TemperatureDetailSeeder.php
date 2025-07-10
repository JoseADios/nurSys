<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemperatureDetailSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            ['name' => 'mañana', 'start' => 7, 'end' => 14],
            ['name' => 'tarde', 'start' => 14, 'end' => 21],
            ['name' => 'noche', 'start' => 21, 'end' => 7],
        ];

        $numberOfDays = 5;
        $startDate = Carbon::now()->addDays(1);

        for ($day = 0; $day < $numberOfDays; $day++) {
            $currentDate = $startDate->copy()->addDays($day);

            foreach ($shifts as $shift) {
                $hour = ($shift['start'] < $shift['end']) ? rand($shift['start'], $shift['end'] - 1) : rand(0, 7);
                $createdAt = $currentDate->copy()->setTime($hour, rand(0, 59));

                // Insertar un único registro de micciones y evacuaciones por turno
                DB::table('elimination_records')->insert([
                    'temperature_record_id' => 1,
                    'nurse_id' => 1,
                    'evacuations' => rand(0, 2),
                    'urinations' => rand(1, 4),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);

                // Insertar múltiples temperaturas en temperature_details
                for ($i = 0; $i < rand(1, 3); $i++) {
                    DB::table('temperature_details')->insert([
                        'temperature_record_id' => 1,
                        'nurse_id' => 1,
                        'temperature' => mt_rand(360, 399) / 10,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }
            }
        }
    }
}
