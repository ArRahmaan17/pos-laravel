<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::table('permissions')->insert([
            ['id' => 1, 'name' => 'manage users', 'description' => fake('id')->word(5), 'code' => 'manage_users', 'resource' => 'users', 'min_scope_level' => 3, 'created_by' => 1, 'created_at' => now()],
            ['id' => 2, 'name' => 'manage inventory', 'description' => fake('id')->word(5), 'code' => 'manage_inventory', 'resource' => 'inventory', 'min_scope_level' => 2, 'created_by' => 1, 'created_at' => now()],
            ['id' => 3, 'name' => 'pos transaction', 'description' => fake('id')->word(5), 'code' => 'pos_transaction', 'resource' => 'transactions', 'min_scope_level' => 1, 'created_by' => 1, 'created_at' => now()],
        ]);
    }
}
