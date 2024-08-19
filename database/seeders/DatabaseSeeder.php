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
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::create(
        // [
        //     'name' => 'maman developer',
        //     'username' => 'dev.rahmaan',
        //     'email' => 'rahmaan@ms.dev',
        //     'phone_number' => '89522983270',
        //     'password' => Hash::make('mamanrecing'),
        // ],
        // [
        //     'name' => 'maman manager',
        //     'username' => 'manager.rahmaan',
        //     'email' => 'rahmaan@ms.man',
        //     'phone_number' => '89522983271',
        //     'password' => Hash::make('mamanrecing'),
        // ],
        // [
        //     'name' => 'maman cashier',
        //     'username' => 'cashier.rahmaan',
        //     'email' => 'rahmaan@ms.cash',
        //     'phone_number' => '89522983272',
        //     'password' => Hash::make('mamanrecing'),
        // ]
        // );
        // \App\Models\AppRole::create([
        //     'name' => 'Developer',
        //     'description' => 'Developer App'
        // ], [
        //     'name' => 'Manager',
        //     'description' => 'Customer Manager'
        // ]);
        // \App\Models\UserRole::create(
        //     [
        //         'userId' => 1,
        //         'roleId' => 1
        //     ],
        //     [
        //         'userId' => 2,
        //         'roleId' => 2
        //     ]
        // );
        // \App\Models\CustomerRole::create([
        //     'userId' => 2,
        //     'name' => 'Cashier',
        //     'description' => 'Customer Cashier'
        // ]);
        // \App\Models\UserCustomerRole::create(
        //     [
        //         'userId' => 3,
        //         'customerRoleId' => 1
        //     ],
        // );
    }
}
