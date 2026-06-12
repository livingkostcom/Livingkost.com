<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;
    protected $fillable = ['room_type_id', 'room_number', 'floor', 'status'];

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
