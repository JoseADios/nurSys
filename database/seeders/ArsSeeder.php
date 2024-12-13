<?php

namespace Database\Seeders;

use App\Models\Ars;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ars::create([
            'name' => 'Humano',
            'description' => 'Seguro de salud humano',
            'active' => true,
        ]);

        Ars::create([
            'name' => 'Futuro',
            'description' => 'Seguro de salud futuro',
            'active' => true,

        ]);

        Ars::create([
            'name' => 'SEMMA',
            'description' => 'Seguro para maestros',
            'active' => true,
        ]);

        Ars::create([
            'name' => 'SENASA',
            'description' => 'Seguro SENASA',
            'active' => true,
        ]);

    }
}
