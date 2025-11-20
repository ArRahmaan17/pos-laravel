<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class DiscountCodeSeeder extends Seeder
{
    public function run()
    {
        DB::table('discount_codes')->insert([
            [
                'id' => 1,
                'code' => 'WELCOME10',
                'description' => implode(' ',fake('id')->words),
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ]
        ]);
    }
}
