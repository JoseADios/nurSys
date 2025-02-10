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
            'slogan' => 'Trabajando por su salud',
            'address' => 'Av. Aniana Vargas No. 4 Monseñor Nouel-Bonao R. D.',
            'phone' => '809-296-2002',
            'fax' => '809-296-1685',
            'email' => 'info@iemmn.com.do',
            'rnc' => '1-200040-7',
        ]);
    }
}
