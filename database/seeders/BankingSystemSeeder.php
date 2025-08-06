<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AccountType;
use App\Models\BankAccount;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BankingSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@securebank.com',
            'password' => Hash::make('password'),
            'role' => 'administrator',
            'email_verified_at' => now(),
        ]);

        // Create operator user
        $operator = User::create([
            'name' => 'Bank Operator',
            'email' => 'operator@securebank.com',
            'password' => Hash::make('password'),
            'role' => 'operator',
            'email_verified_at' => now(),
        ]);

        // Create customer user
        $customer = User::create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Create account types
        $savingsType = AccountType::create([
            'name' => 'Savings Account',
            'description' => 'Standard savings account with interest earning potential',
            'min_balance' => 100.00,
            'max_balance' => 50000.00,
            'per_transaction_limit' => 1000.00,
            'daily_transaction_limit' => 5000.00,
            'dormant_after_days' => 180,
            'reactivate_on_credit' => true,
            'auto_close_after_days' => 730,
            'is_active' => true,
        ]);

        $checkingType = AccountType::create([
            'name' => 'Checking Account',
            'description' => 'Everyday banking account for regular transactions',
            'min_balance' => 0.00,
            'max_balance' => null,
            'per_transaction_limit' => 2500.00,
            'daily_transaction_limit' => 10000.00,
            'dormant_after_days' => 365,
            'reactivate_on_credit' => true,
            'auto_close_after_days' => null,
            'is_active' => true,
        ]);

        $businessType = AccountType::create([
            'name' => 'Business Account',
            'description' => 'Commercial banking account for business operations',
            'min_balance' => 500.00,
            'max_balance' => null,
            'per_transaction_limit' => 10000.00,
            'daily_transaction_limit' => 50000.00,
            'dormant_after_days' => 90,
            'reactivate_on_credit' => true,
            'auto_close_after_days' => 1095,
            'is_active' => true,
        ]);

        // Create bank accounts for the customer
        BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer->id,
            'account_type_id' => $savingsType->id,
            'balance' => 2500.00,
            'status' => 'active',
            'last_activity_at' => now(),
            'notes' => 'Primary savings account',
        ]);

        BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer->id,
            'account_type_id' => $checkingType->id,
            'balance' => 750.00,
            'status' => 'active',
            'last_activity_at' => now(),
            'notes' => 'Primary checking account',
        ]);

        // Create additional customers with accounts
        User::factory(5)
            ->customers()
            ->create()
            ->each(function ($user) use ($savingsType, $checkingType, $businessType) {
                // Create 1-3 accounts per customer
                $accountCount = random_int(1, 3);
                for ($i = 0; $i < $accountCount; $i++) {
                    BankAccount::create([
                        'account_number' => BankAccount::generateAccountNumber(),
                        'user_id' => $user->id,
                        'account_type_id' => collect([$savingsType->id, $checkingType->id, $businessType->id])->random(),
                        'balance' => random_int(100, 5000),
                        'status' => collect(['active', 'active', 'active', 'blocked'])->random(), // Weighted toward active
                        'last_activity_at' => now()->subDays(random_int(0, 30)),
                    ]);
                }
            });
    }
}