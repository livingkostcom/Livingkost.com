<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Services\DokuService;
use App\Services\PaymentSettlementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DokuWebhookController extends Controller
{
    /**
     * Handle DOKU payment notification (webhook). Public, no auth, no CSRF.
     */
    public function handle(Request $request)
    {
        $rawBody = $request->getContent();
        $headers = $request->headers->all();

        $doku = new DokuService();
        if (! $doku->verifyNotificationSignature($headers, $rawBody, '/doku/notification')) {
            Log::warning('DOKU webhook: invalid signature', ['ip' => $request->ip()]);
            return response()->json(['message' => 'invalid signature'], 401);
        }

        $payload = json_decode($rawBody, true) ?: [];
        $invoiceNumber = $payload['order']['invoice_number'] ?? null;
        $status = strtoupper($payload['transaction']['status'] ?? '');

        if (! $invoiceNumber) {
            return response()->json(['message' => 'no invoice_number'], 200);
        }

        $pt = PaymentTransaction::where('reference', $invoiceNumber)->first();
        if (! $pt) {
            Log::warning('DOKU webhook: transaction not found', ['invoice_number' => $invoiceNumber]);
            return response()->json(['message' => 'not found'], 200);
        }

        if ($status === 'SUCCESS') {
            PaymentSettlementService::settleOnline($pt, $payload);
        } elseif (in_array($status, ['FAILED', 'EXPIRED'], true)) {
            $pt->update([
                'status' => $status === 'EXPIRED' ? 'expired' : 'failed',
                'raw' => $payload,
            ]);
        }

        // Always 200 on a verified notification so DOKU stops retrying.
        return response()->json(['message' => 'ok'], 200);
    }
}
