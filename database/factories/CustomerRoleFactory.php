<?php

namespace Database\Factories;

use App\Models\CustomerRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerRole>
 */
class CustomerRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userId' => User::factory(),
            'name' => $this->faker->unique()->randomElement(['Cashier', 'Warehouse', 'Administrator', 'Sales']),
            'description' => $this->faker->sentence(),
            'as_role' => $this->faker->randomElement(['cashier', 'sales', 'admin', 'warehouse']),
        ];
    }
}
