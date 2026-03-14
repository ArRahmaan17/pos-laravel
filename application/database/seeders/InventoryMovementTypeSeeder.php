<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class InventoryMovementTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('inventory_movement_types')->insert([
            [
                'code' => 'STOCK_IN',
                'direction' => 'in',
                'name' => 'Stock In',
                'description' => implode(" ", fake('id')->words(3)),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'code' => 'SALE',
                'direction' => 'out',
                'name' => 'Sales',
                'description' => implode(" ", fake('id')->words(3)),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'code' => 'ADJUSTMENT',
                'direction' => 'none',
                'name' => 'Adjustment',
                'description' => implode(" ", fake('id')->words(3)),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now()
            ],
        ]);
    }
}
