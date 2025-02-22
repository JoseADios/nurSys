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
            ['name' => 'mañana', 'start' => 7, 'end' => 14],  // 7:00 - 14:00
            ['name' => 'tarde', 'start' => 14, 'end' => 21],  // 14:00 - 21:00
            ['name' => 'noche', 'start' => 21, 'end' => 7],   // 21:00 - 7:00
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

            // Generar un registro por cada turno
            foreach ($shifts as $shift) {
                // Calcular la hora aleatoria dentro del turno
                if ($shift['start'] < $shift['end']) {
                    // Turnos normales (mañana y tarde)
                    $hour = rand($shift['start'], $shift['end'] - 1);
                    $createdAt = $currentDate->copy()->setTime($hour, rand(0, 59));
                } else {
                    // Turno noche (cruza la medianoche)
                    if (rand(0, 1) == 0) {
                        // Primera mitad del turno (21:00 - 23:59)
                        $hour = rand($shift['start'], 23);
                        $createdAt = $currentDate->copy()->setTime($hour, rand(0, 59));
                    } else {
                        // Segunda mitad del turno (00:00 - 7:00)
                        $hour = rand(0, $shift['end'] - 1);
                        $createdAt = $currentDate->copy()->addDay()->setTime($hour, rand(0, 59));
                    }
                }

                // Añadir los datos al array
                $data[] = [
                    'temperature_record_id' => 1,
                    'nurse_id' => 1,
                    'temperature' => mt_rand(360, 399) / 10, // Temperatura entre 36.0 y 39.9
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
