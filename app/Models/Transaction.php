<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property string $transaction_id
 * @property int|null $from_account_id
 * @property int|null $to_account_id
 * @property string $type
 * @property string $amount
 * @property string $fee
 * @property string|null $description
 * @property string|null $reference
 * @property string $status
 * @property int|null $processed_by
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount|null $fromAccount
 * @property-read \App\Models\BankAccount|null $toAccount
 * @property-read \App\Models\User|null $processedBy
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction completed()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction forAccount($accountId)
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'transaction_id',
        'from_account_id',
        'to_account_id',
        'type',
        'amount',
        'fee',
        'description',
        'reference',
        'status',
        'processed_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the source account.
     */
    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'from_account_id');
    }

    /**
     * Get the destination account.
     */
    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'to_account_id');
    }

    /**
     * Get the user who processed this transaction.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope a query to only include completed transactions.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to transactions for a specific account.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $accountId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('from_account_id', $accountId)
            ->orWhere('to_account_id', $accountId);
    }

    /**
     * Generate a unique transaction ID.
     *
     * @return string
     */
    public static function generateTransactionId(): string
    {
        do {
            $id = 'TXN' . date('Ymd') . str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('transaction_id', $id)->exists());

        return $id;
    }
}