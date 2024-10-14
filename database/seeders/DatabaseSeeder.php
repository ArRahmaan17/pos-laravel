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
        \App\Models\AppMenu::insert([
            [
                'name' => 'Home',
                'route' => 'home',
                'icon' => 'bx bxs-home',
                'parent' => 0,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:39:18',
                'updated_at' => '2024-10-04 01:40:03',
                'place' => 0,
            ],
            [
                'name' => 'Dev',
                'route' => '#dev',
                'icon' => 'bx bxs-data',
                'parent' => 0,
                'dev_only' => 1,
                'created_at' => '2024-10-04 01:40:39',
                'updated_at' => '2024-10-04 01:40:39',
                'place' => 0,
            ],
            [
                'name' => 'App Menu',
                'route' => 'dev.app-menu.index',
                'icon' => 'bx bx-list-ol',
                'parent' => 2,
                'dev_only' => 1,
                'created_at' => '2024-10-04 01:42:46',
                'updated_at' => '2024-10-04 01:42:46',
                'place' => 0,
            ],
            [
                'name' => 'App Role',
                'route' => 'dev.app-role.index',
                'icon' => 'bx bxs-user-check',
                'parent' => 2,
                'dev_only' => 1,
                'created_at' => '2024-10-04 01:43:20',
                'updated_at' => '2024-10-04 01:43:20',
                'place' => 0,
            ],
            [
                'name' => 'Company',
                'route' => 'man.customer-company.index',
                'icon' => 'bx bxs-building-house',
                'parent' => 0,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:44:19',
                'updated_at' => '2024-10-04 01:44:19',
                'place' => 0,
            ],
            [
                'name' => 'Role',
                'route' => 'man.customer-role.index',
                'icon' => 'bx bxs-user-check',
                'parent' => 5,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:45:49',
                'updated_at' => '2024-10-04 01:45:49',
                'place' => 0,
            ],
            [
                'name' => 'Employee',
                'route' => 'man.customer-user.index',
                'icon' => 'bx bxs-user-detail',
                'parent' => 5,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:46:47',
                'updated_at' => '2024-10-04 01:46:47',
                'place' => 0,
            ],
            [
                'name' => 'Role Permission',
                'route' => 'man.customer-role-accessibility.index',
                'icon' => 'bx bxs-user-detail',
                'parent' => 5,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:47:35',
                'updated_at' => '2024-10-04 01:47:35',
                'place' => 0,
            ],
            [
                'name' => 'Library',
                'route' => '#library',
                'icon' => 'bx bx-library',
                'parent' => 0,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:50:38',
                'updated_at' => '2024-10-04 01:50:38',
                'place' => 0,
            ],
            [
                'name' => 'Product',
                'route' => 'man.customer-company-good.index',
                'icon' => 'bx bxs-book-content',
                'parent' => 9,
                'dev_only' => 0,
                'created_at' => '2024-10-04 01:55:58',
                'updated_at' => '2024-10-04 01:55:58',
                'place' => 0,
            ],
        ]);
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
            ],
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
                'zipCode' => 'test',
            ],
            [
                'companyId' => 2,
                'place' => 'test',
                'address' => 'test',
                'city' => 'test',
                'province' => 'test',
                'zipCode' => 'test',
            ],
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
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'userId' => 1,
                    'name' => 'Cashier',
                    'description' => 'Customer Cashier',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'userId' => 2,
                    'name' => 'Administrator',
                    'description' => 'Customer Administrator',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
                [
                    'userId' => 2,
                    'name' => 'Cashier',
                    'description' => 'Customer Cashier',
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ],
            ]
        );
        \App\Models\UserCustomerRole::insert(
            [
                'userId' => 3,
                'roleId' => 2,
                'companyId' => 1,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
            [
                'userId' => 4,
                'roleId' => 2,
                'companyId' => 2,
                'created_at' => now('Asia/Jakarta'),
                'updated_at' => now('Asia/Jakarta'),
            ],
        );
        \App\Models\AppSubscription::insert([
            [
                'name' => 'Basic',
                'description' => 'A simple start for everyone',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Standard',
                'description' => 'For small to medium businesses',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'description' => 'Solution for big organizations',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
