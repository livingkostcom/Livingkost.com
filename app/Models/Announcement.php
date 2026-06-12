<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id',
        'created_by',
        'title',
        'content',
        'priority',
        'target',
        'published_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'date',
        'expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_reads')
            ->withPivot('read_at');
    }

    public function isReadBy(User $user): bool
    {
        return $this->readByUsers()->where('user_id', $user->id)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('published_at', '<=', now())
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>=', now());
            });
    }

    public static function getPriorityLabel(string $priority): string
    {
        return match ($priority) {
            'urgent' => 'Darurat',
            'important' => 'Penting',
            default => 'Normal',
        };
    }
}
