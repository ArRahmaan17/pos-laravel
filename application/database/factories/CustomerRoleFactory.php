<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserManagement\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Role>
 */
class CustomerRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->randomElement(['Cashier', 'Warehouse', 'Administrator', 'Sales']),
            'description' => $this->faker->sentence(),
            'as_role' => $this->faker->randomElement(['cashier', 'sales', 'admin', 'warehouse']),
        ];
    }
}
