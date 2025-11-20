<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

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
            ]
        ]);
    }
}
