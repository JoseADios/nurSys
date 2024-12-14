<?php

namespace Database\Seeders;

use App\Models\Drug;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Drug::create([
            'name' => 'Neumelubrina',
            'description' => 'Esta es una descripcion de la neumelubrina',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Paracetamol',
            'description' => 'Medicamento analgésico y antipirético de uso común',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Ibuprofeno',
            'description' => 'Antiinflamatorio no esteroideo para el dolor y la fiebre',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Amoxicilina',
            'description' => 'Antibiótico de amplio espectro para infecciones bacterianas',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Losartán',
            'description' => 'Medicamento para el tratamiento de la hipertensión arterial',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Omeprazol',
            'description' => 'Inhibidor de la bomba de protones para problemas de acidez',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Metformina',
            'description' => 'Medicamento para el tratamiento de la diabetes tipo 2',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Atorvastatina',
            'description' => 'Medicamento para reducir el colesterol',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Salmeterol',
            'description' => 'Broncodilatador para el tratamiento del asma y EPOC',
            'active' => true,
        ]);

        Drug::create([
            'name' => 'Risperidona',
            'description' => 'Antipsicótico utilizado para tratar trastornos mentales',
            'active' => true,
        ]);
    }
}
