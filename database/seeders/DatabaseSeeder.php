<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'last_name' => 'Apellido',
            'email' => 'test@example.com',
            'password' => '12345678',
            'identification_card' => '6548463131',
            'phone' => '8094563245',

        ]);

        $this->call(BedSeeder::class);
        $this->call(PatientSeeder::class);
    }
}
