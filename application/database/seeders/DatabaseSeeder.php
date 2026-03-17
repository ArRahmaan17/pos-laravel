<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BusinessTypeSeeder::class,
            CompanySeeder::class,
            ScopeSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            RoleScopeInharitanceSeeder::class,
            RolePermissionSeeder::class,
            UserCompanySeeder::class,
            UserRoleSeeder::class,
            ProductWeightUnitSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            KitSeeder::class,
            KitProductSeeder::class,
            WarehouseSeeder::class,
            WarehouseInventorySeeder::class,
            DiscountTypeSeeder::class,
            DiscountCodeSeeder::class,
            DiscountSeeder::class,
            TransactionTypeSeeder::class,
            InventoryMovementTypeSeeder::class,
            MasterTaskSeeder::class,
        ]);
    }
}
