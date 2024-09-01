<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'maman developer',
                'username' => 'dev.rahmaan',
                'email' => 'rahmaan@ms.dev',
                'phone_number' => '89522983270',
                'password' => Hash::make('mamanrecing'),
                'hr' => true,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ],
            [
                'name' => 'maman manager',
                'username' => 'manager.rahmaan',
                'email' => 'rahmaan@ms.man',
                'phone_number' => '89522983271',
                'password' => Hash::make('mamanrecing'),
                'hr' => true,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ],
            [
                'name' => 'maman cashier',
                'username' => 'cashier.rahmaan',
                'email' => 'rahmaan@ms.cash',
                'phone_number' => '89522983272',
                'password' => Hash::make('mamanrecing'),
                'hr' => false,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ]
        ];
        \App\Models\User::insert(
            $users
        );
        \App\Models\AppRole::insert([
            [
                'name' => 'Developer',
                'description' => 'Developer App',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ],
            [
                'name' => 'Manager',
                'description' => 'Customer Manager',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ]
        ]);
        \App\Models\UserRole::insert(
            [
                [
                    'userId' => 1,
                    'roleId' => 1,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta')
                ],
                [
                    'userId' => 2,
                    'roleId' => 2,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta')
                ]
            ]
        );
        \App\Models\CustomerRole::insert([
            'userId' => 2,
            'name' => 'Cashier',
            'description' => 'Customer Cashier',
            'created_at' => now('Asia/Jakarta'),
            'updated_at' => now('Asia/Jakarta')
        ]);
        \App\Models\UserCustomerRole::insert(
            [
                'userId' => 3,
                'roleId' => 1,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ],
        );
    }
}
