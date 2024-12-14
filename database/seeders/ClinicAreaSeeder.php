<?php

namespace Database\Seeders;

use App\Models\ClinicArea;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicAreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ClinicArea::create([
            'name' => 'Urgencias',
            'description' => 'Esta es una descripcion de la urgencias',
            'active' => true,
        ]);

        ClinicArea::create([
            'name' => 'Pediatría',
            'description' => 'Área especializada en el cuidado médico de niños y adolescentes',
            'active' => true,
        ]);

        ClinicArea::create([
            'name' => 'Oncología',
            'description' => 'Área dedicada al diagnóstico, tratamiento y seguimiento de enfermedades oncológicas',
            'active' => true,
        ]);
    }
}
