<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory, \App\Models\Concerns\BelongsToOwner;
    protected $fillable = ['owner_id', 'name', 'address', 'description', 'status', 'gender_type', 'is_featured', 'featured_image', 'gallery', 'location_label', 'badge_text', 'maps_url', 'common_facilities', 'platform_fee_percent'];

    protected $casts = [
        'is_featured' => 'boolean',
        'gallery' => 'array',
        'common_facilities' => 'array',
        'platform_fee_percent' => 'decimal:2',
    ];

    /**
     * Co-owners of this property with their profit-share percentage (pivot
     * `share_percent`). Revenue is split among these owners after the platform fee.
     */
    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'property_owners', 'property_id', 'owner_id')
            ->withPivot('share_percent')
            ->withTimestamps();
    }

    /**
     * Owner-share map [owner_id => share_percent]; falls back to the single
     * owner_id at 100% when no co-owners are configured.
     */
    public function ownerShares(): array
    {
        $shares = $this->owners()->pluck('share_percent', 'users.id')->map(fn ($v) => (float) $v)->toArray();

        return ! empty($shares) ? $shares : ($this->owner_id ? [$this->owner_id => 100.0] : []);
    }

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
