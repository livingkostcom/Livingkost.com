<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToOwner;
    protected $fillable = ['owner_id', 'name', 'address', 'description', 'status', 'is_featured', 'featured_image', 'gallery', 'location_label', 'badge_text'];

    protected $casts = [
        'is_featured' => 'boolean',
        'gallery' => 'array',
    ];

    /**
     * Starting (cheapest) monthly price derived from this property's room types.
     */
    public function getPriceFromAttribute(): ?float
    {
        $min = $this->roomTypes()->min('price');
        return $min !== null ? (float) $min : null;
    }

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
