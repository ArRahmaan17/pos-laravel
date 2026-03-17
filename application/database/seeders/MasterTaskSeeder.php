<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
