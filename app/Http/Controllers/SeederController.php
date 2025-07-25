<?php

namespace App\Http\Controllers;

use App\Models\CustomerCompanyDiscount;
use App\Models\CustomerCompanyGood;
use App\Models\CustomerCompanyWarehouse;
use App\Models\CustomerWarehouseRack;

class SeederController extends Controller
{
    public function processSeeder()
    {
        $warehouseId = CustomerCompanyWarehouse::create([
            'name' => 'Warehouse '.fake()->word,
            'description' => 'Warehouse '.fake()->words,
            'companyId' => session('userLogged')['company']['id'],
        ]);
        CustomerWarehouseRack::insert([
            [
                'warehouseId' => $warehouseId,
                'name' => 'Shelf '.fake()->word,
                'description' => 'Shelf '.fake()->words,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouseId' => $warehouseId,
                'name' => 'Shelf '.fake()->word,
                'description' => 'Shelf '.fake()->words,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouseId' => $warehouseId,
                'name' => 'Shelf '.fake()->word,
                'description' => 'Shelf '.fake()->words,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        CustomerCompanyGood::insert([
            [
                'name' => fake('Id')->word,
                'picture' => 'default-product.png',
                'stock' => rand(100, 1000),
                'price' => rand(5000, 100000),
                'unitId' => rand(1, 20),
                'companyId' => session('userLogged')['company']['id'],
                'status' => shuffle(['archive', 'draft', 'publish']),
                'buyPrice' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => fake('Id')->word,
                'picture' => 'default-product.png',
                'stock' => rand(100, 1000),
                'price' => rand(5000, 100000),
                'unitId' => rand(1, 20),
                'companyId' => session('userLogged')['company']['id'],
                'status' => shuffle(['archive', 'draft', 'publish']),
                'buyPrice' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => fake('Id')->word,
                'picture' => 'default-product.png',
                'stock' => rand(100, 1000),
                'price' => rand(5000, 100000),
                'unitId' => rand(1, 20),
                'companyId' => session('userLogged')['company']['id'],
                'status' => shuffle(['archive', 'draft', 'publish']),
                'buyPrice' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        CustomerCompanyDiscount::insert([
            [
                'companyId' => session('userLogged')['company']['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
