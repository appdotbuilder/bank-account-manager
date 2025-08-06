<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AccountHold
 *
 * @property int $id
 * @property int $bank_account_id
 * @property string $amount
 * @property string $reason
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string $status
 * @property int $created_by
 * @property int|null $released_by
 * @property \Illuminate\Support\Carbon|null $released_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankAccount $bankAccount
 * @property-read \App\Models\User $createdBy
 * @property-read \App\Models\User|null $releasedBy
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|AccountHold newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountHold newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountHold query()
 * @method static \Illuminate\Database\Eloquent\Builder|AccountHold active()
 * @method static \Database\Factories\AccountHoldFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class AccountHold extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bank_account_id',
        'amount',
        'reason',
        'expires_at',
        'status',
        'created_by',
        'released_by',
        'released_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    /**
     * Get the bank account this hold is on.
     */
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    /**
     * Get the user who created this hold.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who released this hold.
     */
    public function releasedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by');
    }

    /**
     * Scope a query to only include active holds.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }
}