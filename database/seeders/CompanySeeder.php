<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CompanySeeder extends Seeder
{
    public function run()
    {
        DB::table('companies')->insert([
            [
                'id' => 1,
                'name' => 'Default Company',
                'phone_number' => fake('id')->phoneNumber(),
                'email' => fake('id')->email(),
                'picture' => str_replace(public_path('/'), "", Storage::disk('default')->path('/company/default-company.png')),
                'user_id' => 1,
                'business_id' => 1,
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
        DB::table('company_addresses')->insert([
            [
                'id' => 1,
                'company_id' => 1,
                'place' => fake('id')->word(),
                'address' => fake('id')->word(),
                'city' => fake('id')->word(),
                'province' => fake('id')->word(),
                'zip_code' => fake('id')->numberBetween(10000, 99999),
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
