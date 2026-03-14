<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MasterTaskSeeder extends Seeder
{
    public function run()
    {
        DB::table('master_tasks')->insert([
            [
                'id' => 1,
                'name' => 'Daily Stock Check',
                'description' => 'Check stock levels every day',
                'recurrence_type' => 'daily',
                'recurrence_interval' => 1,
                'is_active' => true,
                'company_id' => 1,
                'created_by' => 1,
            ],
        ]);
    }
}
