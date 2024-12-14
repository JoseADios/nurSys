<?php

namespace Database\Seeders;

use App\Models\Clinic;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clinic::create([
            'name' => 'Instituto de Especialidades Medicas Monseñor Nouel',
            'about' => 'Esta es una descripcion de la institucion',
            'address' => 'Av. de la Constitucion, s/n',
            'phone' => '8094563245',
            'rnc' => '12345678',
        ]);
    }
}
