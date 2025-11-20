<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('user_roles')->insert([
            ['user_id' => 1, 'role_id' => 1],
        ]);
    }
}
