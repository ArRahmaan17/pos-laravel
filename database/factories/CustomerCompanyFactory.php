<?php

namespace Database\Factories;

use App\Models\BusinessType;
use App\Models\CustomerCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerCompany>
 */
class CustomerCompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CustomerCompany::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'phone_number' => $this->faker->phoneNumber(),
            'businessId' => BusinessType::factory(),
            'userId' => User::factory(),
        ];
    }
}
