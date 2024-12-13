<?php

namespace Database\Seeders;

use App\Models\Regime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RegimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Regime::create([
            'name' => 'Subsidiario',
            'description' => 'Esta es una descripcion de la regimen',
        ]);

        Regime::create([
            'name' => 'Contributivo',
            'description' => 'Esta es una descripcion de la regimen',
        ]);

    }
}
