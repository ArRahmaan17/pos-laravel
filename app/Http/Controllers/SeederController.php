<?php

namespace App\Http\Controllers;

use App\Models\CustomerCompanyGood;
use App\Models\CustomerCompanyWarehouse;
use App\Models\CustomerWarehouseRack;

class SeederController extends Controller
{
    public function processSeeder()
    {
        $warehouse_id = CustomerCompanyWarehouse::create([
            'name' => 'Warehouse '.fake()->word,
            'description' => 'Warehouse '.fake()->words,
            'company_id' => session('userLogged')['company']['id'],
        ]);
        CustomerWarehouseRack::insert([
            [
                'warehouse_id' => $warehouse_id,
                'name' => 'Shelf '.fake()->word,
                'description' => 'Shelf '.fake()->words,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => $warehouse_id,
                'name' => 'Shelf '.fake()->word,
                'description' => 'Shelf '.fake()->words,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'warehouse_id' => $warehouse_id,
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
                'unit_id' => rand(1, 20),
                'company_id' => session('userLogged')['company']['id'],
                'status' => shuffle(['archive', 'draft', 'publish']),
                'buy_price' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => fake('Id')->word,
                'picture' => 'default-product.png',
                'stock' => rand(100, 1000),
                'price' => rand(5000, 100000),
                'unit_id' => rand(1, 20),
                'company_id' => session('userLogged')['company']['id'],
                'status' => shuffle(['archive', 'draft', 'publish']),
                'buy_price' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => fake('Id')->word,
                'picture' => 'default-product.png',
                'stock' => rand(100, 1000),
                'price' => rand(5000, 100000),
                'unit_id' => rand(1, 20),
                'company_id' => session('userLogged')['company']['id'],
                'status' => shuffle(['archive', 'draft', 'publish']),
                'buy_price' => rand(50000, 100000),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
