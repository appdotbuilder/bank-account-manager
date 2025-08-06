<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\BankAccount
 *
 * @property int $id
 * @property string $account_number
 * @property int $user_id
 * @property int $account_type_id
 * @property string $balance
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $last_activity_at
 * @property \Illuminate\Support\Carbon|null $dormant_at
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\AccountType $accountType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $fromTransactions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $toTransactions
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AccountHold> $holds
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AutoDebit> $autoDebits
 * @property-read int|null $from_transactions_count
 * @property-read int|null $to_transactions_count
 * @property-read int|null $holds_count
 * @property-read int|null $auto_debits_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount active()
 * @method static \Illuminate\Database\Eloquent\Builder|BankAccount forUser($userId)
 * @method static \Database\Factories\BankAccountFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class BankAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'account_number',
        'user_id',
        'account_type_id',
        'balance',
        'status',
        'last_activity_at',
        'dormant_at',
        'closed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'balance' => 'decimal:2',
        'last_activity_at' => 'datetime',
        'dormant_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account type.
     */
    public function accountType(): BelongsTo
    {
        return $this->belongsTo(AccountType::class);
    }

    /**
     * Get transactions where this account is the sender.
     */
    public function fromTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    /**
     * Get transactions where this account is the receiver.
     */
    public function toTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    /**
     * Get all holds on this account.
     */
    public function holds(): HasMany
    {
        return $this->hasMany(AccountHold::class);
    }

    /**
     * Get all auto debits for this account.
     */
    public function autoDebits(): HasMany
    {
        return $this->hasMany(AutoDebit::class);
    }

    /**
     * Scope a query to only include active accounts.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to accounts for a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Generate a unique account number.
     *
     * @return string
     */
    public static function generateAccountNumber(): string
    {
        do {
            $number = 'ACC' . str_pad((string)random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        } while (self::where('account_number', $number)->exists());

        return $number;
    }

    /**
     * Get available balance (balance minus active holds).
     *
     * @return float
     */
    public function getAvailableBalance(): float
    {
        $activeHolds = $this->holds()
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->sum('amount');

        return max(0, (float)$this->balance - (float)$activeHolds);
    }
}