<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['debit', 'credit', 'transfer']);
        
        return [
            'transaction_id' => Transaction::generateTransactionId(),
            'from_account_id' => $type !== 'credit' ? BankAccount::factory() : null,
            'to_account_id' => $type !== 'debit' ? BankAccount::factory() : null,
            'type' => $type,
            'amount' => $this->faker->randomFloat(2, 10, 1000),
            'fee' => $this->faker->randomFloat(2, 0, 10),
            'description' => $this->faker->optional()->sentence(),
            'reference' => $this->faker->optional()->regexify('[A-Z]{3}[0-9]{6}'),
            'status' => $this->faker->randomElement(['completed', 'completed', 'completed', 'pending', 'failed']), // Weighted toward completed
            'processed_by' => User::factory(),
            'metadata' => null,
        ];
    }

    /**
     * Indicate that the transaction is a transfer.
     */
    public function transfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'transfer',
            'from_account_id' => BankAccount::factory(),
            'to_account_id' => BankAccount::factory(),
        ]);
    }

    /**
     * Indicate that the transaction is a debit.
     */
    public function debit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'debit',
            'from_account_id' => BankAccount::factory(),
            'to_account_id' => null,
        ]);
    }

    /**
     * Indicate that the transaction is a credit.
     */
    public function credit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'credit',
            'from_account_id' => null,
            'to_account_id' => BankAccount::factory(),
        ]);
    }
}