<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserCompanySeeder extends Seeder
{
    public function run()
    {
        DB::table('user_companies')->insert([
            ['user_id' => 1, 'company_id' => 1],
        ]);
    }
}
