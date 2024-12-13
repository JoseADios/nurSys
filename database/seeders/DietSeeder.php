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
            'description' => 'Esta es una descripcion de la corriente',
        ]);

        Diet::create([
            'name' => 'Proteína',
            'description' => 'Esta es una descripcion de la proteina',
        ]);
    }
}
