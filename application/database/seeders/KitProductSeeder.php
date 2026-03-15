<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KitProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('kit_products')->insert([
            [
                'id' => 1,
                'kit_id' => 1,
                'product_id' => 1, // must exist in ProductSeeder
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
