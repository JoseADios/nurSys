<?php

namespace Database\Seeders;

use App\Models\DrugRoute;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrugRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DrugRoute::create([
            'name' => 'IV',
            'description' => 'Intravenosa',
            'active' => true,
        ]);

        DrugRoute::create([
            'name' => 'IM',
            'description' => 'Intramuscular',
            'active' => true
        ]);

        DrugRoute::create([
            'name' => 'VO',
            'description' => 'Oral',
            'active' => true,
        ]);

        DrugRoute::create([
            'name' => 'IV',
            'description' => 'Intravenosa',
            'active' => true,
        ]);

        DrugRoute::create([
            'name' => 'S/C',
            'description' => 'Subcutánea',
            'active' => true,
        ]);
    }
}
