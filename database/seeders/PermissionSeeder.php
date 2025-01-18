<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // medical orders
        Permission::create([
            'name' => 'create medicalOrders'
        ]);
        Permission::create([
            'name' => 'update medicalOrders'
        ]);
        Permission::create([
            'name' => 'view medicalOrders'
        ]);
        Permission::create([
            'name' => 'delete medicalOrders'
        ]);
    }
}
