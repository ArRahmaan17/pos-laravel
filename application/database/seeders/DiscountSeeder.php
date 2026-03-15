<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountSeeder extends Seeder
{
    public function run()
    {
        DB::table('discounts')->insert([
            [
                'id' => 1,
                'product_id' => 1,
                'kit_product_id' => null,
                'discount_code_id' => 1,
                'discount_type_id' => 1,
                'discount_value' => 10.00,
                'company_id' => 1,
                'expirated_at' => now()->addMonths(3),
                'max_usage' => 100,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
