<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        DB::table('role_permissions')->insert([
            ['role_id' => 1, 'permission_id' => 1, 'created_by' => 1, 'created_at' => now()],
            ['role_id' => 1, 'permission_id' => 2, 'created_by' => 1, 'created_at' => now()],
            ['role_id' => 1, 'permission_id' => 3, 'created_by' => 1, 'created_at' => now()],
        ]);
    }
}
