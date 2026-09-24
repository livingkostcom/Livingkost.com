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
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
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
