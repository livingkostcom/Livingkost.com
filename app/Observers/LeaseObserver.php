<?php

namespace App\Observers;

use App\Models\Lease;
use App\Models\Room;
use Illuminate\Support\Facades\Auth;

class LeaseObserver
{
    /**
     * Handle the Lease "creating" event.
     */
    public function creating(Lease $lease): void
    {
        // Auto-set created_by from authenticated user
        if (Auth::check() && !$lease->created_by) {
            $lease->created_by = Auth::id();
        }
    }

    /**
     * Handle the Lease "created" event.
     */
    public function created(Lease $lease): void
    {
        // Update room status to occupied when lease is created
        $lease->room->update(['status' => 'occupied']);
    }

    /**
     * Handle the Lease "updating" event.
     */
    public function updating(Lease $lease): void
    {
        // Prevent updating if lease is closed
        if ($lease->getOriginal('status') === 'closed') {
            throw new \Exception('Closed leases cannot be modified.');
        }

        // Auto-set updated_by
        if (Auth::check()) {
            $lease->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Lease "updated" event.
     */
    public function updated(Lease $lease): void
    {
        // Handle room status update when lease is terminated or closed
        if ($lease->isDirty('status') && in_array($lease->status, ['closed', 'terminated'])) {
            $lease->room->update(['status' => 'available']);
        }
    }

    /**
     * Handle the Lease "deleting" event.
     */
    public function deleting(Lease $lease): void
    {
        // Prevent hard deletion - use soft delete only
        if ($lease->isForceDeleting()) {
            throw new \Exception('Hard deletion of leases is not allowed.');
        }
    }

    /**
     * Handle the Lease "restored" event.
     */
    public function restored(Lease $lease): void
    {
        // Restore room to occupied status
        if ($lease->status === 'active') {
            $lease->room->update(['status' => 'occupied']);
        }
    }

    /**
     * Handle the Lease "force deleted" event.
     */
    public function forceDeleted(Lease $lease): void
    {
        //
    }
}
