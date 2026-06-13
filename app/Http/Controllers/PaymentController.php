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

        // Only the tenant who owns this invoice may pay it.
        $owns = $user?->tenant
            && $user->tenant->leases()->where('id', $invoice->lease_id)->exists();

        if (! $owns) {
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
