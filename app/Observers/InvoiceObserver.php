<?php

namespace App\Observers;

use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    /**
     * Handle the Invoice "creating" event.
     */
    public function creating(Invoice $invoice): void
    {
        // Auto-set created_by from authenticated user
        if (Auth::check() && !$invoice->created_by) {
            $invoice->created_by = Auth::id();
        }

        // Inherit owner from the parent lease (works in console/scheduler too)
        if (empty($invoice->owner_id) && $invoice->lease_id) {
            $invoice->owner_id = \App\Models\Lease::withoutGlobalScopes()
                ->whereKey($invoice->lease_id)
                ->value('owner_id');
        }
    }

    /**
     * Handle the Invoice "created" event.
     */
    public function created(Invoice $invoice): void
    {
        // Log or dispatch event if needed
    }

    /**
     * Handle the Invoice "updating" event.
     */
    public function updating(Invoice $invoice): void
    {
        // Prevent update if already verified/paid
        if ($invoice->isDirty('status') && $invoice->getOriginal('status') === 'paid') {
            throw new \Exception('Verified invoices cannot be modified.');
        }
    }

    /**
     * Handle the Invoice "updated" event.
     */
    public function updated(Invoice $invoice): void
    {
        // Track verification - use updateQuietly to prevent infinite loop
        if ($invoice->isDirty('status') && $invoice->status === 'paid') {
            // Atomic update with verification timestamp
            DB::table('invoices')
                ->where('id', $invoice->id)
                ->update([
                    'verified_by' => Auth::id(),
                    'verified_at' => now(),
                ]);
        }
    }

    /**
     * Handle the Invoice "deleting" event.
     */
    public function deleting(Invoice $invoice): void
    {
        // Prevent deletion/soft delete of verified invoices
        if ($invoice->verified_at) {
            throw new \Exception('Verified invoices cannot be deleted.');
        }
    }

    /**
     * Handle the Invoice "restored" event.
     */
    public function restored(Invoice $invoice): void
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     */
    public function forceDeleted(Invoice $invoice): void
    {
        //
    }
}
