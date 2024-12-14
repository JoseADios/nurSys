<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Nationality::create([
            'name' => 'Español',
            'active' => true,
        ]);

        Nationality::create([
            'name' => 'Dominicano',
            'active' => true,
        ]);

        Nationality::create([
            'name' => 'Americano',
            'active' => true,
        ]);

        Nationality::create([
            'name' => 'Francés',
            'active' => true,
        ]);

    }
}
