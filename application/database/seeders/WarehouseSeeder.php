<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        DB::table('warehouses')->insert([
            [
                'id' => 1,
                'name' => 'Main Warehouse',
                'description' => fake('id')->address(),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
        DB::table('shelves')->insert([
            [
                'id' => 1,
                'warehouse_id' => 1,
                'name' => fake('id')->word(),
                'description' => fake('id')->address(),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'warehouse_id' => 1,
                'name' => fake('id')->word(),
                'description' => fake('id')->address(),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'warehouse_id' => 1,
                'name' => fake('id')->word(),
                'description' => fake('id')->address(),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
