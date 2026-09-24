<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantRegistration extends Model
{
    use \App\Models\Concerns\BelongsToOwner;

    protected $fillable = [
        'owner_id', 'name', 'email', 'phone', 'nik', 'emergency_contact',
        'ktp_photo', 'note', 'status', 'reviewed_at', 'reviewed_by', 'tenant_id',
        'dp_amount', 'dp_method', 'dp_status', 'dp_reference', 'dp_proof', 'dp_paid_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'dp_paid_at' => 'datetime',
        'dp_amount' => 'decimal:2',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
