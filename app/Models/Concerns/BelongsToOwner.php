<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Multi-tenant scoping by owner.
 *
 * - Adds a global scope that limits queries to the current user's owner context,
 *   so each owner (and their managers/tenants) only sees their own data.
 * - Super-admins and unauthenticated contexts (console commands, schedulers,
 *   seeders) are NOT scoped — they operate across all owners.
 * - Automatically stamps owner_id on create for non-super-admin users.
 */
trait BelongsToOwner
{
    public static function bootBelongsToOwner(): void
    {
        static::addGlobalScope('owner', function (Builder $builder) {
            $user = Auth::user();

            if (! $user || $user->isSuperAdmin()) {
                return;
            }

            $builder->where($builder->getModel()->getTable() . '.owner_id', $user->ownerId());
        });

        static::creating(function ($model) {
            if (! empty($model->owner_id)) {
                return;
            }

            $user = Auth::user();

            if ($user && ! $user->isSuperAdmin()) {
                $model->owner_id = $user->ownerId();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
