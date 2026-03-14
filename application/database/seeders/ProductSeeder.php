<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'Sample Product',
                'code' => 'SP-' . str_pad(fake('id')->numberBetween(1, 100), 5, "0", STR_PAD_LEFT),
                'picture' => str_replace(public_path('/'), '', Storage::disk('default')->path('product/default-product.png')),
                'price' => random_int(1000, 10000),
                'buy_price' => random_int(1000, 10000),
                'weight_id' => 1,
                'category_id' => 1,
                'company_id' => 1,
                'created_by' => 1
            ],
        ]);
    }
}
