<?php

namespace Database\Factories;

use App\Models\BankAccount;
use App\Models\User;
use App\Models\AccountType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => User::factory(),
            'account_type_id' => AccountType::factory(),
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'blocked', 'dormant']), // Weighted toward active
            'last_activity_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'dormant_at' => $this->faker->optional(0.1)->dateTimeBetween('-6 months', 'now'),
            'closed_at' => null,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the account is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'dormant_at' => null,
            'closed_at' => null,
        ]);
    }

    /**
     * Indicate that the account is blocked.
     */
    public function blocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'blocked',
        ]);
    }

    /**
     * Indicate that the account is dormant.
     */
    public function dormant(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dormant',
            'dormant_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ]);
    }
}