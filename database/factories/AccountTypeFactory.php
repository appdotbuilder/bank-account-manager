<?php

namespace Database\Factories;

use App\Models\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountType>
 */
class AccountTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Savings', 'Checking', 'Business', 'Premium', 'Student']),
            'description' => $this->faker->sentence(),
            'min_balance' => $this->faker->randomFloat(2, 0, 100),
            'max_balance' => $this->faker->randomElement([null, 10000, 50000, 100000]),
            'per_transaction_limit' => $this->faker->randomElement([null, 1000, 5000, 10000]),
            'daily_transaction_limit' => $this->faker->randomElement([null, 5000, 10000, 25000]),
            'dormant_after_days' => $this->faker->randomElement([null, 90, 180, 365]),
            'reactivate_on_credit' => $this->faker->boolean(80),
            'auto_close_after_days' => $this->faker->randomElement([null, 730, 1095]),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}