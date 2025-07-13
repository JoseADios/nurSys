<?php

namespace Database\Seeders;

use App\Models\Diet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Diet::create([
            'name' => 'Corriente',
            'description' => 'Pacientes sin restricciones alimentarias específicas. ',
        ]);

        Diet::create([
            'name' => 'Suave',
            'description' => 'Pacientes con molestias gastrointestinales leves. ',
        ]);

        Diet::create([
            'name' => 'Hiposódica',
            'description' => 'Pacientes con hipertensión, insuficiencia renal o cardíaca.',
        ]);

        Diet::create([
            'name' => 'Líquida',
            'description' => 'Usada antes o después de cirugías o cuando el paciente no puede masticar.',
        ]);


    }
}
