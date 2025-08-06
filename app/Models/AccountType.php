<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\AccountType
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $min_balance
 * @property string|null $max_balance
 * @property string|null $per_transaction_limit
 * @property string|null $daily_transaction_limit
 * @property int|null $dormant_after_days
 * @property bool $reactivate_on_credit
 * @property int|null $auto_close_after_days
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankAccount> $bankAccounts
 * @property-read int|null $bank_accounts_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|AccountType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountType query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountType active()
 * @method static \Database\Factories\AccountTypeFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class AccountType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
        'min_balance',
        'max_balance',
        'per_transaction_limit',
        'daily_transaction_limit',
        'dormant_after_days',
        'reactivate_on_credit',
        'auto_close_after_days',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'min_balance' => 'decimal:2',
        'max_balance' => 'decimal:2',
        'per_transaction_limit' => 'decimal:2',
        'daily_transaction_limit' => 'decimal:2',
        'reactivate_on_credit' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get all bank accounts of this type.
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    /**
     * Scope a query to only include active account types.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}