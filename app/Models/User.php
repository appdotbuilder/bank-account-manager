<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankAccount> $bankAccounts
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AccountHold> $createdHolds
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AutoDebit> $createdAutoDebits
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $processedTransactions
 * @property-read int|null $bank_accounts_count
 * @property-read int|null $created_holds_count
 * @property-read int|null $created_auto_debits_count
 * @property-read int|null $processed_transactions_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User administrators()
 * @method static \Illuminate\Database\Eloquent\Builder|User operators()
 * @method static \Illuminate\Database\Eloquent\Builder|User customers()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all bank accounts owned by this user.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Get all holds created by this user.
     */
    public function createdHolds(): HasMany
    {
        return $this->hasMany(AccountHold::class, 'created_by');
    }

    /**
     * Get all auto debits created by this user.
     */
    public function createdAutoDebits(): HasMany
    {
        return $this->hasMany(AutoDebit::class, 'created_by');
    }

    /**
     * Get all transactions processed by this user.
     */
    public function processedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'processed_by');
    }

    /**
     * Scope a query to only include administrators.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdministrators($query)
    {
        return $query->where('role', 'administrator');
    }

    /**
     * Scope a query to only include operators.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOperators($query)
    {
        return $query->where('role', 'operator');
    }

    /**
     * Scope a query to only include customers.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    /**
     * Check if user is an administrator.
     *
     * @return bool
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    /**
     * Check if user is an operator.
     *
     * @return bool
     */
    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    /**
     * Check if user is a customer.
     *
     * @return bool
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user can manage accounts (admin or operator).
     *
     * @return bool
     */
    public function canManageAccounts(): bool
    {
        return in_array($this->role, ['administrator', 'operator']);
    }
}