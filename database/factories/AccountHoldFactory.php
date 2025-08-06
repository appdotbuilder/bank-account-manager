<?php

namespace Database\Factories;

use App\Models\AccountHold;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountHold>
 */
class AccountHoldFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_account_id' => BankAccount::factory(),
            'amount' => $this->faker->randomFloat(2, 50, 500),
            'reason' => $this->faker->sentence(),
            'expires_at' => $this->faker->optional(0.7)->dateTimeBetween('now', '+6 months'),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'released', 'expired']), // Weighted toward active
            'created_by' => User::factory(),
            'released_by' => null,
            'released_at' => null,
        ];
    }

    /**
     * Indicate that the hold is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'released_by' => null,
            'released_at' => null,
        ]);
    }

    /**
     * Indicate that the hold is released.
     */
    public function released(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'released',
            'released_by' => User::factory(),
            'released_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the hold is indefinite.
     */
    public function indefinite(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => null,
        ]);
    }
}