<?php

namespace App\Policies;

use App\Models\Lease;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        if ($user->hasRole('tenant')) {
            return true; // Tenant can view own leases
        }
        return $user->hasPermissionTo('view-leases');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lease $lease): bool
    {
        // Tenant can only view own leases
        if ($user->hasRole('tenant')) {
            return $user->tenant?->id === $lease->tenant_id;
        }

        return $user->hasPermissionTo('view-leases');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create-lease');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lease $lease): bool
    {
        // Cannot update completed or terminated leases
        if (in_array($lease->status, ['completed', 'terminated', 'cancelled'])) {
            return false;
        }

        return $user->hasPermissionTo('edit-lease');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lease $lease): bool
    {
        // Only owner can soft delete pending or cancelled leases
        if (!in_array($lease->status, ['pending', 'cancelled'])) {
            return false;
        }
        return $user->hasPermissionTo('delete-lease');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lease $lease): bool
    {
        return $user->hasRole('owner');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lease $lease): bool
    {
        return $user->hasRole('owner');
    }
}
