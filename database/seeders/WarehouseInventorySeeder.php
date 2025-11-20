<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class WarehouseInventorySeeder extends Seeder
{
    public function run()
    {
        DB::table('warehouse_inventories')->insert([
            [
                'id' => 1,
                'product_id' => 1,
                'warehouse_id' => 1,
                'quantity_on_hand' => 100,
                'created_by' => 1,
                'created_at' => now(),
            ]
        ]);
    }
}
