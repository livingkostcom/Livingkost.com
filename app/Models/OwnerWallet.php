<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OwnerWallet extends Model
{
    protected $fillable = [
        'owner_id',
        'online_payment_enabled',
        'platform_fee_percent',
        'balance',
        'total_earned',
        'total_disbursed',
    ];

    protected $casts = [
        'online_payment_enabled' => 'boolean',
        'platform_fee_percent' => 'decimal:2',
        'balance' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'total_disbursed' => 'decimal:2',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'owner_id', 'owner_id');
    }

    public function disbursements(): HasMany
    {
        return $this->hasMany(Disbursement::class, 'owner_id', 'owner_id');
    }

    /**
     * Whether online payment is enabled for a given owner id (cheap lookup).
     */
    public static function onlineEnabledFor(?int $ownerId): bool
    {
        if (! $ownerId) {
            return false;
        }

        return static::where('owner_id', $ownerId)->value('online_payment_enabled') ? true : false;
    }
}
