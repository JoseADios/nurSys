<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TemperatureDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Datos de ejemplo para insertar en la tabla temperature_details
        $data = [
            [
                'temperature_record_id' => 1,
                'nurse_id' => 1,
                'temperature' => 37,
                'evacuations' => 2,
                'urinations' => 3,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'temperature_record_id' => 1,
                'nurse_id' => 1,
                'temperature' => 38,
                'evacuations' => 1,
                'urinations' => 4,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'temperature_record_id' => 2,
                'nurse_id' => 1,
                'temperature' => 39,
                'evacuations' => 2,
                'urinations' => 1,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'temperature_record_id' => 2,
                'nurse_id' => 2,
                'temperature' => 38,
                'evacuations' => 1,
                'urinations' => 'P',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'temperature_record_id' => 3,
                'nurse_id' => 1,
                'temperature' => 39,
                'evacuations' => 0,
                'urinations' => 'S',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insertar los datos en la tabla
        DB::table('temperature_details')->insert($data);
    }
}
