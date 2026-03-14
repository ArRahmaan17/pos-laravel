<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KitSeeder extends Seeder
{
    public function run()
    {
        DB::table('kits')->insert([
            [
                'id' => 1,
                'name' => 'Starter Kit',
                'description' => 'Example bundled kit',
                'company_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ]
        ]);
    }
}