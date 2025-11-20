<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class RoleScopeInharitanceSeeder extends Seeder
{
    public function run()
    {
        DB::table('role_scope_inharitances')->insert([
            ['parent_scope_id' => 1, 'child_scope_id' => 2],
            ['parent_scope_id' => 2, 'child_scope_id' => 3],
        ]);
    }
}
