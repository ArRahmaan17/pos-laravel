<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ProductCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('product_categories')->insert([
            [
                'id' => 1,
                'name' => 'Default Category',
                'description' => fake('id')->word(),
                'created_by' => 1,
                'company_id' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
