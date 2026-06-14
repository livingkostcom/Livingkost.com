<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    use HasFactory, SoftDeletes, \App\Models\Concerns\BelongsToOwner;

    protected $fillable = ['owner_id', 'tenant_id', 'room_id', 'start_date', 'end_date', 'due_date_per_month', 'deposit_amount', 'status', 'created_by', 'updated_by'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'deposit_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Roll the contract end date forward by one month. Called when a monthly
     * rent payment is verified (manual approval or online settlement) so the
     * lease "paid until" date extends automatically. Uses no-overflow month
     * math (e.g. Jan 31 + 1 month → Feb 28, not Mar 3). No-op if end_date unset.
     */
    public function extendByOneMonth(): void
    {
        if (! $this->end_date) {
            return;
        }

        $this->end_date = $this->end_date->copy()->addMonthNoOverflow();
        $this->save();
    }
}
