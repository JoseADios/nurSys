<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Habitaciones del segundo piso
        // Habitaciones privadas (una cama)
        $privateRooms2ndFloor = ['213', '214', '215', '216', '217', '218'];
        foreach ($privateRooms2ndFloor as $room) {
            DB::table('beds')->insert([
                'number' => 1,
                'room' => $room,
                'floor' => 2,
                'active' => true,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Suite Ejecutiva (una cama)
        DB::table('beds')->insert([
            'number' => 1,
            'room' => 'Suite 1-Ejecutiva',
            'floor' => 2,
            'active' => true,
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Suite 2 - Semiprivada (tres camas)
        for ($i = 1; $i <= 3; $i++) {
            DB::table('beds')->insert([
                'number' => $i,
                'room' => 'Suite 2',
                'floor' => 2,
                'active' => true,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Habitaciones del tercer piso
        // Habitaciones privadas (una cama)
        $privateRooms3rdFloor = [
            '301', '302', '303', '304', '305', '308', '309',
            '311', '312', '314', '315', '316', '317', '318',
            '319', '320', '321', '322', '323', '324'
        ];

        foreach ($privateRooms3rdFloor as $room) {
            DB::table('beds')->insert([
                'number' => 1,
                'room' => $room,
                'floor' => 3,
                'active' => true,
                'status' => 'available',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Habitaciones semiprivadas (múltiples camas)
        $semiPrivateRooms3rdFloor = [
            '306' => 2, // 2 camas
            '307' => 2, // 2 camas
            '310' => 2, // 2 camas
            '325' => 3  // 3 camas
        ];

        foreach ($semiPrivateRooms3rdFloor as $room => $bedCount) {
            for ($i = 1; $i <= $bedCount; $i++) {
                DB::table('beds')->insert([
                    'number' => $i,
                    'room' => $room,
                    'floor' => 3,
                    'active' => true,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
