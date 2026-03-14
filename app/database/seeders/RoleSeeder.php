<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Developer',
                'code' => 'dev',
                'description' => 'application dev',
                'scope_id' => 1,
                'is_system' => true,
                'created_by' => 1,
            ],
        ]);
    }
}
