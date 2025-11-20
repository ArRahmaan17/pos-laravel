<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class UserCompanySeeder extends Seeder
{
    public function run()
    {
        DB::table('user_companies')->insert([
            ['user_id' => 1, 'company_id' => 1],
        ]);
    }
}
