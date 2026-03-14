<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ScopeSeeder extends Seeder
{
    public function run()
    {
        DB::table('scopes')->insert([
            ['id' => 1, 'code' => 'global', 'name' => 'Global Scope', 'level' => 3],
            ['id' => 2, 'code' => 'organization', 'name' => 'Organization Scope', 'level' => 2],
            ['id' => 3, 'code' => 'user_created', 'name' => 'User Created Scope', 'level' => 1],
        ]);
    }
}
