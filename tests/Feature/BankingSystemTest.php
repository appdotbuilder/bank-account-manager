<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\BankAccount;
use App\Models\AccountType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BankingSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create basic account types
        AccountType::create([
            'name' => 'Savings Account',
            'description' => 'Basic savings account',
            'min_balance' => 100.00,
            'max_balance' => 50000.00,
            'per_transaction_limit' => 1000.00,
            'daily_transaction_limit' => 5000.00,
            'dormant_after_days' => 180,
            'reactivate_on_credit' => true,
            'auto_close_after_days' => 730,
            'is_active' => true,
        ]);
    }

    public function test_welcome_page_displays_correctly(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('welcome')
                ->has('auth')
        );
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->administrator()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('dashboard')
                ->where('userRole', 'administrator')
                ->has('stats')
        );
    }

    public function test_customer_can_access_dashboard(): void
    {
        $customer = User::factory()->customers()->create();

        $response = $this->actingAs($customer)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('dashboard')
                ->where('userRole', 'customer')
                ->has('stats')
        );
    }

    public function test_customer_can_view_own_accounts(): void
    {
        $customer = User::factory()->customers()->create();
        $accountType = AccountType::first();
        
        $account = BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer->id,
            'account_type_id' => $accountType->id,
            'balance' => 1000.00,
            'status' => 'active',
            'last_activity_at' => now(),
        ]);

        $response = $this->actingAs($customer)->get('/bank-accounts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('bank-accounts/index')
                ->where('userRole', 'customer')
                ->has('accounts.data', 1)
                ->where('accounts.data.0.id', $account->id)
        );
    }

    public function test_customer_cannot_view_other_accounts(): void
    {
        $customer1 = User::factory()->customers()->create();
        $customer2 = User::factory()->customers()->create();
        $accountType = AccountType::first();
        
        $account1 = BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer1->id,
            'account_type_id' => $accountType->id,
            'balance' => 1000.00,
            'status' => 'active',
            'last_activity_at' => now(),
        ]);
        
        $account2 = BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer2->id,
            'account_type_id' => $accountType->id,
            'balance' => 2000.00,
            'status' => 'active',
            'last_activity_at' => now(),
        ]);

        $response = $this->actingAs($customer1)->get('/bank-accounts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('bank-accounts/index')
                ->has('accounts.data', 1)
                ->where('accounts.data.0.id', $account1->id)
        );
    }

    public function test_admin_can_view_all_accounts(): void
    {
        $admin = User::factory()->administrator()->create();
        $customer = User::factory()->customers()->create();
        $accountType = AccountType::first();
        
        $account = BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer->id,
            'account_type_id' => $accountType->id,
            'balance' => 1000.00,
            'status' => 'active',
            'last_activity_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/bank-accounts');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('bank-accounts/index')
                ->where('userRole', 'administrator')
                ->has('accounts.data', 1)
        );
    }

    public function test_operator_can_create_account(): void
    {
        $operator = User::factory()->operator()->create();
        $customer = User::factory()->customers()->create();
        $accountType = AccountType::first();

        $response = $this->actingAs($operator)->get('/bank-accounts/create');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('bank-accounts/create')
                ->has('accountTypes')
                ->has('customers')
        );
    }

    public function test_customer_cannot_create_account(): void
    {
        $customer = User::factory()->customers()->create();

        $response = $this->actingAs($customer)->get('/bank-accounts/create');

        $response->assertStatus(302);
        $response->assertRedirect('/bank-accounts');
        $response->assertSessionHas('error', 'Only administrators and operators can create accounts.');
    }

    public function test_account_number_generation_is_unique(): void
    {
        $number1 = BankAccount::generateAccountNumber();
        $number2 = BankAccount::generateAccountNumber();

        $this->assertNotEquals($number1, $number2);
        $this->assertStringStartsWith('ACC', $number1);
        $this->assertStringStartsWith('ACC', $number2);
        $this->assertEquals(10, strlen($number1)); // ACC + 7 digits
    }

    public function test_available_balance_calculation(): void
    {
        $customer = User::factory()->customers()->create();
        $accountType = AccountType::first();
        
        $account = BankAccount::create([
            'account_number' => BankAccount::generateAccountNumber(),
            'user_id' => $customer->id,
            'account_type_id' => $accountType->id,
            'balance' => 1000.00,
            'status' => 'active',
            'last_activity_at' => now(),
        ]);

        // Test without holds
        $this->assertEquals(1000.00, $account->getAvailableBalance());

        // Test with active hold
        $account->holds()->create([
            'amount' => 200.00,
            'reason' => 'Test hold',
            'status' => 'active',
            'created_by' => $customer->id,
        ]);

        $this->assertEquals(800.00, $account->getAvailableBalance());
    }
}