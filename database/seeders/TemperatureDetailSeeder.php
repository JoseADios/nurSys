<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemperatureDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir los turnos
        $shifts = [
            ['start' => 7, 'end' => 14],   // Turno mañana (7:00 - 14:00)
            ['start' => 14, 'end' => 21],  // Turno tarde (14:00 - 21:00)
            ['start' => 21, 'end' => 7],   // Turno noche (21:00 - 7:00)
        ];

        // Número de días para los que se generarán datos
        $numberOfDays = 5;

        // Fecha inicial
        $startDate = Carbon::now()->subDays($numberOfDays);

        // Array para almacenar los datos
        $data = [];

        // Generar datos para cada día
        for ($day = 0; $day < $numberOfDays; $day++) {
            $currentDate = $startDate->copy()->addDays($day);

            // Generar 4 temperaturas por día (una por turno, excepto el último turno que cubre dos días)
            for ($i = 0; $i < 4; $i++) {
                $shift = $shifts[$i % count($shifts)]; // Seleccionar el turno correspondiente

                // Generar una hora aleatoria dentro del turno
                $hour = rand($shift['start'], $shift['end'] - 1);
                $minute = rand(0, 59);

                // Si el turno es de 21 a 7, ajustar la fecha correctamente
                if ($shift['start'] > $shift['end']) {
                    if ($hour >= 21) {
                        $createdAt = $currentDate->copy()->setTime($hour, $minute);
                    } else {
                        $createdAt = $currentDate->copy()->addDay()->setTime($hour, $minute);
                    }
                } else {
                    $createdAt = $currentDate->copy()->setTime($hour, $minute);
                }

                // Añadir los datos al array
                $data[] = [
                    'temperature_record_id' => 1,
                    'nurse_id' => 1,
                    'temperature' => mt_rand(360, 399) / 10, // Temperatura flotante entre 36.0 y 39.9
                    'evacuations' => rand(0, 2),
                    'urinations' => rand(1, 4),
                    'active' => true,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];
            }
        }

        // Insertar los datos en la tabla
        DB::table('temperature_details')->insert($data);
    }
}
