<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'email', 'nik', 'phone', 'emergency_contact', 'avatar', 'ktp_photo', 'status', 'user_id', 'created_by', 'updated_by'];

    protected $dates = ['deleted_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * Get display name: from user if connected, otherwise from tenant
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->user_id ? $this->user->name : $this->name;
    }
}
