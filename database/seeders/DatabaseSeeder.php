<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\TemperatureRecord;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Hash;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'last_name' => 'Apellido',
            'email' => 'test@example.com',
            'password' => Hash::make('12345678'),
            'identification_card' => '6548463131',
            'phone' => '8094563245',

        ]);

        User::factory(10)->create();
        User::factory()->create([
            'name' => 'San',
            'last_name' => 'Pedro',
            'email' => 'spedro@example.com',
            'password' => Hash::make('12345678'),
            'identification_card' => '6548443131',
            'phone' => '8093563245',

        ]);

        $this->call(PermissionSeeder::class);
        $this->call(RoleSeeder::class);

        $this->call(BedSeeder::class);
        $this->call(ArsSeeder::class);
        $this->call(PatientSeeder::class);
        $this->call(AdmissionSeeder::class);
        $this->call(MedicationRecordSeeder::class);
        $this->call(DrugSeeder::class);
        $this->call(ClinicSeeder::class);
        $this->call(ClinicAreaSeeder::class);
        $this->call(DietSeeder::class);
        $this->call(DrugDoseSeeder::class);
        $this->call(DrugRouteSeeder::class);
        $this->call(NationalitySeeder::class);
        $this->call(RegimeSeeder::class);
        $this->call(NurseRecordSeeder::class);
        $this->call(NurseRecordDetailSeeder::class);
        $this->call(MedicalOrderSeeder::class);
        $this->call(MedicalOrderDetailSeeder::class);
        $this->call(MedicationRecordDetailSeeder::class);
        $this->call(MedicationNotificationSeeder::class);
        $this->call(TemperatureRecordSeeder::class);
        $this->call(TemperatureDetailSeeder::class);
        $this->call(MaritalStatusSeeder::class);
    }
}
