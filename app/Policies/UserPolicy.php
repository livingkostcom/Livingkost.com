<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage-users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage-users');
    }

    public function update(User $user, User $target): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $this->managesSubUser($user, $target);
    }

    public function delete(User $user, User $target): bool
    {
        // Nobody may delete their own account here.
        if ($user->id === $target->id) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        return $this->managesSubUser($user, $target);
    }

    /**
     * An owner may manage only their own managers & tenants.
     */
    private function managesSubUser(User $user, User $target): bool
    {
        return $user->hasPermissionTo('manage-users')
            && $user->isOwner()
            && $target->owner_id === $user->id
            && $target->hasAnyRole(['manager', 'tenant']);
    }
}
