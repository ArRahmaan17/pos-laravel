<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    public function run()
    {
        DB::table('transaction_types')->insert([
            [
                'id' => 1,
                'code' => 'SALE',
                'description' => 'Sales transaction',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'id' => 2,
                'code' => 'RETURN',
                'description' => 'Return transaction',
                'created_by' => 1,
                'created_at' => now()
            ],
            [
                'id' => 3,
                'code' => 'STOCK_IN',
                'description' => 'Stock in transaction',
                'created_by' => 1,
                'created_at' => now(),
            ],
        ]);
    }
}
