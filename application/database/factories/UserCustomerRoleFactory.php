<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\UserManagement\Role;
use App\Models\User;
use App\Models\UserCustomerRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserCustomerRole>
 */
class UserCustomerRoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserCustomerRole::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'role_id' => Role::factory(),
            'company_id' => Company::factory(),
        ];
    }
}
