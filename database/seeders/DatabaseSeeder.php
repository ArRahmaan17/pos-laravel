<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AppGoodUnit;
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
            //1
            ["name" => "Home", "route" => "home", "icon" => "bx bxs-home", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //2
            ["name" => "Dev", "route" => "#dev", "icon" => "bx bxs-data", "parent" => 0, "dev_only" => 1, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //3
            ["name" => "App Menu", "route" => "dev.app-menu.index", "icon" => "bx bx-list-ol", "parent" => 2, "dev_only" => 1, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //4
            ["name" => "App Role", "route" => "dev.app-role.index", "icon" => "bx bxs-user-check", "parent" => 2, "dev_only" => 1, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //5
            ["name" => "Subscription", "route" => "dev.app-subscription.index", "icon" => "bx bx-layer-plus", "parent" => 2, "dev_only" => 1, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //6
            ["name" => "List Company", "route" => "man.customer-company.index", "icon" => "bx bxs-building", "parent" => 2, "dev_only" => 1, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //7
            ["name" => "Company", "route" => "man.customer-company.index", "icon" => "bx bxs-building-house", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //8
            ["name" => "Role", "route" => "man.customer-role.index", "icon" => "bx bxs-user-check", "parent" => 7, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //9
            ["name" => "Role Permission", "route" => "man.customer-role-accessibility.index", "icon" => "bx bxs-user-detail", "parent" => 7, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //10
            ["name" => "Employee", "route" => "man.customer-user.index", "icon" => "bx bxs-user-detail", "parent" => 7, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //11
            ["name" => "Library", "route" => "#library", "icon" => "bx bx-library", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //12
            ["name" => "Temp Product", "route" => "man.customer-temp-product.index", "icon" => "bx bx-package", "parent" => 11, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //13
            ["name" => "Product", "route" => "man.customer-company-good.index", "icon" => "bx bxs-book-content", "parent" => 11, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //14
            ["name" => "Discount", "route" => "man.customer-company-discount.index", "icon" => "bx bxs-discount", "parent" => 11, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //15
            ["name" => "Profile", "route" => "man.customer-user.profile", "icon" => "bx bxs-user-circle", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 1],
            //16
            ["name" => "Storage", "route" => "#store", "icon" => "bx bxs-data", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //17
            ["name" => "Warehouse", "route" => "man.customer-company-warehouse.index", "icon" => "bx bxs-grid", "parent" => 16, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //18
            ["name" => "Warehouse Shelf", "route" => "man.customer-warehouse-rack-good.index", "icon" => "bx bx-grid-vertical", "parent" => 16, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //19
            ["name" => "Transaction", "route" => "man.customer-product-transaction.index", "icon" => "bx bx-cart", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],
            //20
            ["name" => "Report", "route" => "man.report.index", "icon" => "bx bxs-report", "parent" => 0, "dev_only" => 0, "created_at" => now(), "updated_at" => now(), "place" => 0],

        ]);
        \App\Models\User::insert(
            [
                [
                    'name' => 'maman developer',
                    'username' => 'dev.rahmaan',
                    'email' => 'rahmaan@ms.dev',
                    'phone_number' => '89522983270',
                    'password' => Hash::make('mamanrecing'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'maman manager',
                    'username' => 'manager.rahmaan',
                    'email' => 'rahmaan@ms.man',
                    'phone_number' => '89522983271',
                    'password' => Hash::make('mamanrecing'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'maman cashier',
                    'username' => 'cashier.rahmaan',
                    'email' => 'rahmaan@ms.cash',
                    'phone_number' => '89522983272',
                    'password' => Hash::make('mamanrecing'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'maman admin',
                    'username' => 'admin.rahmaan',
                    'email' => 'rahmaan@ms.adm',
                    'phone_number' => '89522983273',
                    'password' => Hash::make('mamanrecing'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
        \App\Models\AppRole::insert([
            [
                'name' => 'Developer',
                'description' => 'Developer App',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager',
                'description' => 'Customer Manager',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        \App\Models\BusinessType::insert([
            [
                'name' => 'Physical Retail Stores',
                'description' => 'Retail stores like supermarkets and clothing outlets.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Restaurants and Cafes',
                'description' => 'Establishments serving food and beverages where customers pay after ordering.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Beauty Salons and Spas',
                'description' => 'Businesses offering services like haircuts and massages.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cinemas',
                'description' => 'Movie theaters where customers buy tickets and snacks.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bookstores',
                'description' => 'Shops selling books and stationery.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gyms and Fitness Centers',
                'description' => 'Gyms that may use cashiers for payments for classes.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Gas Stations',
                'description' => 'Fuel stations where customers pay after filling up.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Home Goods Stores',
                'description' => 'Stores selling household items and furniture.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        \App\Models\CustomerCompany::insert([
            [
                'name' => 'Doglex Code',
                'userId' => 1,
                'picture' => 'default-company.png',
                'phone_number' => fake('ID')->phoneNumber(),
                'email' => fake('ID')->email(),
                'businessId' => 1,
                'affiliate_code' => generateAffiliateCode(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Doglex Cafe',
                'userId' => 2,
                'picture' => 'default-company.png',
                'phone_number' => fake('ID')->phoneNumber(),
                'email' => fake('ID')->email(),
                'businessId' => 2,
                'affiliate_code' => generateAffiliateCode(),
                'created_at' => now(),
                'updated_at' => now(),
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'userId' => 2,
                    'roleId' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
        \App\Models\CustomerRole::insert(
            [
                [
                    'userId' => 1,
                    'name' => 'Administrator',
                    'description' => 'Customer Administrator',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'userId' => 1,
                    'name' => 'Cashier',
                    'description' => 'Customer Cashier',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'userId' => 2,
                    'name' => 'Administrator',
                    'description' => 'Customer Administrator',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'userId' => 2,
                    'name' => 'Cashier',
                    'description' => 'Customer Cashier',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );
        \App\Models\UserCustomerRole::insert(
            [
                [
                    'userId' => 3,
                    'roleId' => 2,
                    'companyId' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'userId' => 4,
                    'roleId' => 2,
                    'companyId' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]
        );

        \App\Models\AppGoodUnit::insert([
            ['name' => 'Liter (L)', 'description' => 'Used to measure liquid volumes, typically for beverages or cleaning products.'],
            ['name' => 'Gallon (Gal)', 'description' => 'A unit of liquid capacity commonly used in the U.S.'],
            ['name' => 'Milliliter (mL)', 'description' => 'Smaller volume measurement used for smaller liquid quantities, such as perfume or medicine.'],
            ['name' => 'Kilogram (kg)', 'description' => 'Used for measuring heavy products like food items, clothing, or large items.'],
            ['name' => 'Gram (g)', 'description' => 'Smaller unit of weight, typically used for food packaging or small goods.'],
            ['name' => 'Pound (lbs)', 'description' => 'Used in the U.S. for measuring the weight of products like food, furniture, etc.'],
            ['name' => 'Piece (pcs)', 'description' => 'Used for individual items, such as clothing or gadgets.'],
            ['name' => 'Pair (pr)', 'description' => 'Used for items that come in sets of two, like shoes or socks.'],
            ['name' => 'Set', 'description' => 'A collection of related items sold together, such as a cutlery set or bedding set.'],
            ['name' => 'Dozen (dz)', 'description' => 'Used for items sold in groups of 12, such as eggs or roses.'],
            ['name' => 'Box', 'description' => 'Refers to a box containing a number of products, typically food or accessories.'],
            ['name' => 'Pack', 'description' => 'Used for products sold in a grouped set, like a pack of juice boxes or tissues.'],
            ['name' => 'Meter (m)', 'description' => 'Used for measuring length, often for fabric, rope, or plumbing products.'],
            ['name' => 'Centimeter (cm)', 'description' => 'Smaller unit of length, commonly used for clothing sizes and small accessories.'],
            ['name' => 'Square Meter (m²)', 'description' => 'Used to measure the area of a surface, such as flooring or carpet.'],
            ['name' => 'Minute (min)', 'description' => 'Used to measure short durations of time, such as for gym sessions or car washes.'],
            ['name' => 'Hour (hr)', 'description' => 'Used for longer periods, like renting equipment or booking services.'],
            ['name' => 'Roll', 'description' => 'Used for items that come in a rolled form, like paper or fabric.'],
            ['name' => 'Sheet', 'description' => 'Used for products sold as individual sheets, like paper or towels.'],
            ['name' => 'Bottle', 'description' => 'Used for liquid products like water, soda, or wine, typically measured in milliliters or liters.'],
        ]);
    }
}
