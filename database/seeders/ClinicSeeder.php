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
            'address' => 'Av. Circunvalación No. 4 Monseñor Nouel-Bonao República Dominicana',
            'phone' => '8092962004',
            'email' => 'info@iemmn.com.do',
            'rnc' => '12345678',
        ]);
    }
}
