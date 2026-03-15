<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('business_types')->insert([
            [
                'name' => 'Physical Retail Stores',
                'description' => 'Retail stores like supermarkets and clothing outlets.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Restaurants and Cafes',
                'description' => 'Establishments serving food and beverages where customers pay after ordering.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Beauty Salons and Spas',
                'description' => 'Businesses offering services like haircuts and massages.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Cinemas',
                'description' => 'Movie theaters where customers buy tickets and snacks.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Bookstores',
                'description' => 'Shops selling books and stationery.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Gyms and Fitness Centers',
                'description' => 'Gyms that may use cashiers for payments for classes.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Gas Stations',
                'description' => 'Fuel stations where customers pay after filling up.',
                'created_by' => 1,
                'created_at' => now(),
            ],
            [
                'name' => 'Home Goods Stores',
                'description' => 'Stores selling household items and furniture.',
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
