<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiscountTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('discount_types')->insert([
            ['id' => 1, 'name' => 'Percentage', 'description' => 'Percentage discount', 'company_id' => 1, 'created_by' => 1],
            ['id' => 2, 'name' => 'Fixed Amount', 'description' => 'Flat discount', 'company_id' => 1, 'created_by' => 1],
        ]);
    }
}
