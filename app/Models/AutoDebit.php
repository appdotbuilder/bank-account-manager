<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AutoDebit
 *
 * @property int $id
 * @property int $bank_account_id
 * @property string $name
 * @property string $amount
 * @property string $frequency
 * @property \Illuminate\Support\Carbon $next_debit_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property bool $is_active
 * @property int $created_by
 * @property array|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount $bankAccount
 * @property-read \App\Models\User $createdBy
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|AutoDebit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoDebit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoDebit query()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoDebit active()
 * @method static \Illuminate\Database\Eloquent\Builder|AutoDebit dueForProcessing()
 * @method static \Database\Factories\AutoDebitFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class AutoDebit extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bank_account_id',
        'name',
        'amount',
        'frequency',
        'next_debit_date',
        'end_date',
        'is_active',
        'created_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'next_debit_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the bank account this auto debit is for.
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Get the user who created this auto debit.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope a query to only include active auto debits.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            });
    }

    /**
     * Scope a query to auto debits due for processing.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDueForProcessing($query)
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->where('next_debit_date', '<=', now()->toDateString());
    }
}