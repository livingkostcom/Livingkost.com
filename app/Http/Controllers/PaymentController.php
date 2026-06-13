<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\PaymentInitiator;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Deep link from reminders: start the DOKU payment for an invoice and
     * redirect the (authenticated) tenant straight to the DOKU payment page.
     */
    public function pay(Request $request, Invoice $invoice)
    {
        $user = $request->user();
        $ownerId = $invoice->owner_id ?? $invoice->lease?->tenant?->owner_id;

        // The owning tenant may pay it; the managing owner/manager or a
        // super-admin may also open it (to assist a tenant or to test).
        $isOwningTenant = $user?->tenant
            && $user->tenant->leases()->where('id', $invoice->lease_id)->exists();
        $isStaff = $user?->isSuperAdmin()
            || ($user && $user->ownerId() === $ownerId && ($user->isOwner() || $user->hasRole('manager')));

        if (! $isOwningTenant && ! $isStaff) {
            abort(403, 'Invoice ini bukan milik Anda.');
        }

        $res = PaymentInitiator::startForInvoice($invoice);

        if (empty($res['success'])) {
            return redirect()->route('tenant.invoices.index')
                ->with('error', $res['error'] ?? 'Gagal memulai pembayaran.');
        }

        return redirect()->away($res['url']);
    }
}
