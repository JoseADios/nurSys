<?php

namespace Database\Seeders;

use App\Models\DrugDose;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrugDoseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DrugDose::create([
            'name' => 'Cuch',
            'description' => 'Cucharadita',
            'active' => true,
        ]);

        DrugDose::create([
            'name' => 'CC',
            'description' => 'Centimetro cubico',
            'active' => true,
        ]);

        DrugDose::create([
            'name' => 'MG',
            'description' => 'Miligramo',
            'active' => true,
        ]);
    }
}
