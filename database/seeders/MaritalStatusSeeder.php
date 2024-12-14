<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MaritalStatus::create([
            'name' => 'Casado',
            'active' => true,
        ]);

        MaritalStatus::create([
            'name' => 'Soltero',
            'active' => true,
        ]);

        MaritalStatus::create([
            'name' => 'Divorciado',
            'active' => true,
        ]);

        MaritalStatus::create([
            'name' => 'Viudo',
            'active' => true,
        ]);
    }
}
