<?php

namespace Database\Seeders;

use App\Models\MedicalOrder;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalOrderDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener algunas órdenes médicas existentes
        $medicalOrders = MedicalOrder::all();

        // Crear múltiples detalles de órdenes médicas
        $medicalOrderDetails = [
            [
                'medical_order_id' => $medicalOrders->first()->id,
                'order' => 'Administrar antibióticos cada 8 horas',
                'regime' => 'Tratamiento intensivo',
                'suspended_at' => null,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medical_order_id' => $medicalOrders->skip(1)->first()->id,
                'order' => 'Reposo absoluto por 48 horas',
                'regime' => 'Recuperación',
                'suspended_at' => null,
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medical_order_id' => $medicalOrders->skip(2)->first()->id,
                'order' => 'Suspender medicación anterior',
                'regime' => 'Cambio de tratamiento',
                'suspended_at' => Carbon::now()->addDays(3),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'medical_order_id' => $medicalOrders->first()->id,
                'order' => 'Control de signos vitales cada 4 horas',
                'regime' => 'Monitoreo',
                'suspended_at' => now(),
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('medical_order_details')->insert($medicalOrderDetails);
    }
}
