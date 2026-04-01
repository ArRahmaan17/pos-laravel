<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run()
    {
        DB::table('companies')->insert([
            [
                'id' => 1,
                'name' => 'Default Company',
                'phone_number' => '081234567890',
                'email' => 'admin@default.com',
                'picture' => 'resources/default/company/default-company.png',
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
                'place' => 'Main Office',
                'address' => 'Default Street No. 1',
                'city' => 'Default City',
                'province' => 'Default Province',
                'zip_code' => '12345',
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
