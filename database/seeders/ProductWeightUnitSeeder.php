<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ProductWeightUnitSeeder extends Seeder
{
    public function run()
    {
        DB::table('product_weights')->insert([
            [
                'id' => 1,
                'name' => 'Default Weight',
                'description' => fake('id')->word(),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
