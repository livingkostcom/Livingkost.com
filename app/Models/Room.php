<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToOwner;
    protected $fillable = ['owner_id', 'room_type_id', 'room_number', 'floor', 'status'];

    protected $appends = ['computed_status'];

    /**
     * Computed status: 'occupied' if has active lease, else returns the stored status ('available' or 'maintenance').
     * Manual status only allows 'available' or 'maintenance'; 'occupied' is derived from leases.
     */
    public function getComputedStatusAttribute()
    {
        return $this->activeLease() ? 'occupied' : $this->attributes['status'] ?? 'available';
    }

    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class);
    }

    public function leases(): HasMany
    {
        return $this->hasMany(Lease::class);
    }

    public function activeLease()
    {
        return $this->leases()
            ->where('status', 'active')
            ->latest('start_date')
            ->first();
    }

    public function getPropertyAttribute()
    {
        return $this->roomType->property;
    }
}
