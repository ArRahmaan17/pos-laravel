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
        \App\Models\User::insert(
            [
                [
                    'name' => 'maman developer',
                    'username' => 'dev.rahmaan',
                    'email' => 'rahmaan@ms.dev',
                    'phone_number' => '89522983270',
                    'password' => Hash::make('mamanrecing'),
                    'hr' => true,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'name' => 'maman manager',
                    'username' => 'manager.rahmaan',
                    'email' => 'rahmaan@ms.man',
                    'phone_number' => '89522983271',
                    'password' => Hash::make('mamanrecing'),
                    'hr' => true,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'name' => 'maman cashier',
                    'username' => 'cashier.rahmaan',
                    'email' => 'rahmaan@ms.cash',
                    'phone_number' => '89522983272',
                    'password' => Hash::make('mamanrecing'),
                    'hr' => false,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'name' => 'maman admin',
                    'username' => 'admin.rahmaan',
                    'email' => 'rahmaan@ms.adm',
                    'phone_number' => '89522983273',
                    'password' => Hash::make('mamanrecing'),
                    'hr' => false,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
            ]
        );
        \App\Models\AppRole::insert([
            [
                'name' => 'Developer',
                'description' => 'Developer App',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Manager',
                'description' => 'Customer Manager',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
        ]);
        \App\Models\BusinessType::insert([
            [
                'name' => 'Physical Retail Stores',
                'description' => 'Retail stores like supermarkets and clothing outlets.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Restaurants and Cafes',
                'description' => 'Establishments serving food and beverages where customers pay after ordering.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Beauty Salons and Spas',
                'description' => 'Businesses offering services like haircuts and massages.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Cinemas',
                'description' => 'Movie theaters where customers buy tickets and snacks.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Bookstores',
                'description' => 'Shops selling books and stationery.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Gyms and Fitness Centers',
                'description' => 'Gyms that may use cashiers for payments for classes.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Gas Stations',
                'description' => 'Fuel stations where customers pay after filling up.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Home Goods Stores',
                'description' => 'Stores selling household items and furniture.',
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ]
        ]);
        \App\Models\CustomerCompany::insert([
            [
                'name' => 'Doglex Code',
                'userId' => 1,
                'picture' => 'default-picture.png',
                'phone_number' => fake('ID')->phoneNumber(),
                'email' => fake('ID')->email(),
                'businessId' => 1,
                'affiliate_code' => generateAffiliateCode(),
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'name' => 'Doglex Cafe',
                'userId' => 2,
                'picture' => 'default-picture.png',
                'phone_number' => fake('ID')->phoneNumber(),
                'email' => fake('ID')->email(),
                'businessId' => 2,
                'affiliate_code' => generateAffiliateCode(),
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
        ]);
        \App\Models\CompanyAddress::insert([
            [
                'companyId' => 1,
                'place' => 'test',
                'address' => 'test',
                'city' => 'test',
                'province' => 'test',
                'zipCode' => 'test'
            ],
            [
                'companyId' => 2,
                'place' => 'test',
                'address' => 'test',
                'city' => 'test',
                'province' => 'test',
                'zipCode' => 'test'
            ]
        ]);
        \App\Models\UserRole::insert(
            [
                [
                    'userId' => 1,
                    'roleId' => 1,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'userId' => 2,
                    'roleId' => 2,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
            ]
        );
        \App\Models\CustomerRole::insert(
            [
                [
                    'userId' => 1,
                    'name' => 'Administrator',
                    'description' => 'Customer Administrator',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta')
                ],
                [
                    'userId' => 1,
                    'name' => 'Cashier',
                    'description' => 'Customer Cashier',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta')
                ],
                [
                    'userId' => 2,
                    'name' => 'Administrator',
                    'description' => 'Customer Administrator',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta')
                ],
                [
                    'userId' => 2,
                    'name' => 'Cashier',
                    'description' => 'Customer Cashier',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta')
                ],
            ]
        );
        \App\Models\UserCustomerRole::insert(
            [
                'userId' => 3,
                'roleId' => 2,
                'companyId' => 1,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ],
            [
                'userId' => 4,
                'roleId' => 2,
                'companyId' => 2,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta')
            ],
        );
    }
}
