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
     * Occupancy follows active leases; without one, only a maintenance flag blocks availability.
     * Manual status only allows 'available' or 'maintenance'; 'occupied' is derived from leases.
     */
    public function getComputedStatusAttribute()
    {
        if ($this->activeLease()) {
            return 'occupied';
        }

        // Legacy occupied flags must not keep an empty room locked.
        return ($this->attributes['status'] ?? null) === 'maintenance' ? 'maintenance' : 'available';
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
