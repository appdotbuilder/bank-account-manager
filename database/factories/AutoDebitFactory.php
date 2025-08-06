<?php

namespace Database\Factories;

use App\Models\AutoDebit;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutoDebit>
 */
class AutoDebitFactory extends Factory
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
            'name' => $this->faker->randomElement([
                'Monthly Insurance',
                'Utility Bill',
                'Loan Payment',
                'Subscription Service',
                'Rent Payment',
            ]),
            'amount' => $this->faker->randomFloat(2, 25, 500),
            'frequency' => $this->faker->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'next_debit_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => $this->faker->optional(0.3)->dateTimeBetween('+3 months', '+2 years'),
            'is_active' => $this->faker->boolean(85),
            'created_by' => User::factory(),
            'metadata' => null,
        ];
    }

    /**
     * Indicate that the auto debit is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the auto debit is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the auto debit is monthly.
     */
    public function monthly(): static
    {
        return $this->state(fn (array $attributes) => [
            'frequency' => 'monthly',
            'next_debit_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ]);
    }
}