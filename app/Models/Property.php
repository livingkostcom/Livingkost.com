<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToOwner;
    protected $fillable = ['owner_id', 'name', 'address', 'description', 'status'];

    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class);
    }

    public function getTotalRoomsAttribute()
    {
        return $this->roomTypes()->with('rooms')->get()->sum(function ($roomType) {
            return $roomType->rooms->count();
        });
    }
}
