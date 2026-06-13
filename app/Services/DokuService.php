<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * DOKU "Checkout" (Jokul, built-in payment page) integration.
 *
 * Flow: createCheckoutPayment() returns a hosted payment URL we redirect the
 * tenant to. When the tenant pays, DOKU POSTs a notification to our webhook;
 * verifyNotificationSignature() authenticates it before we settle.
 *
 * @see https://docs.doku.com/
 */
class DokuService
{
    private string $clientId;
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->clientId = (string) config('services.doku.client_id');
        $this->secretKey = (string) config('services.doku.secret_key');
        $this->baseUrl = rtrim((string) config('services.doku.base_url'), '/');
    }

    public function isConfigured(): bool
    {
        return $this->clientId !== '' && $this->secretKey !== '';
    }

    /**
     * Create a Checkout payment and return the hosted payment page details.
     *
     * @return array{success:bool, url?:string, reference?:string, raw:array, error?:string}
     */
    public function createCheckoutPayment(
        string $invoiceNumber,
        float $amount,
        string $customerName,
        ?string $customerEmail,
        string $callbackUrl
    ): array {
        $requestTarget = '/checkout/v1/payment';
        $requestId = (string) Str::uuid();
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');

        $body = [
            'order' => [
                'amount' => (int) round($amount), // DOKU expects integer rupiah
                'invoice_number' => $invoiceNumber,
                'currency' => 'IDR',
                'callback_url' => $callbackUrl,
            ],
            'payment' => [
                'payment_due_date' => 60, // minutes
            ],
            'customer' => array_filter([
                'name' => $customerName,
                'email' => $customerEmail,
            ]),
        ];

        $jsonBody = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->buildSignature($requestId, $timestamp, $requestTarget, $jsonBody);

        try {
            $response = Http::withHeaders([
                'Client-Id' => $this->clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $signature,
                'Content-Type' => 'application/json',
            ])->withBody($jsonBody, 'application/json')
              ->post($this->baseUrl . $requestTarget);

            $data = $response->json() ?? [];

            if (! $response->successful()) {
                Log::error('DOKU createCheckoutPayment failed', ['status' => $response->status(), 'body' => $data]);
                return ['success' => false, 'raw' => $data, 'error' => $data['error']['message'] ?? 'Gagal membuat pembayaran DOKU'];
            }

            $url = $data['response']['payment']['url'] ?? null;
            if (! $url) {
                return ['success' => false, 'raw' => $data, 'error' => 'URL pembayaran tidak ditemukan pada respons DOKU'];
            }

            return [
                'success' => true,
                'url' => $url,
                'reference' => $invoiceNumber,
                'raw' => $data,
            ];
        } catch (\Throwable $e) {
            Log::error('DOKU createCheckoutPayment exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'raw' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Verify the signature of an incoming DOKU notification (webhook).
     *
     * @param array  $headers  request headers (case-insensitive lookups handled)
     * @param string $rawBody  the exact raw request body
     * @param string $requestTarget  the path of our notification endpoint
     */
    public function verifyNotificationSignature(array $headers, string $rawBody, string $requestTarget): bool
    {
        $get = function (string $key) use ($headers) {
            foreach ($headers as $k => $v) {
                if (strcasecmp($k, $key) === 0) {
                    return is_array($v) ? ($v[0] ?? '') : $v;
                }
            }
            return '';
        };

        $clientId = $get('Client-Id');
        $requestId = $get('Request-Id');
        $timestamp = $get('Request-Timestamp');
        $received = $get('Signature');

        if (! $clientId || ! $requestId || ! $timestamp || ! $received) {
            return false;
        }

        $expected = $this->buildSignature($requestId, $timestamp, $requestTarget, $rawBody, $clientId);

        return hash_equals($expected, $received);
    }

    /**
     * Build the DOKU "HMACSHA256=..." signature header value.
     */
    private function buildSignature(string $requestId, string $timestamp, string $requestTarget, string $rawBody, ?string $clientId = null): string
    {
        $digest = base64_encode(hash('sha256', $rawBody, true));

        $componentSignature = implode("\n", [
            'Client-Id:' . ($clientId ?? $this->clientId),
            'Request-Id:' . $requestId,
            'Request-Timestamp:' . $timestamp,
            'Request-Target:' . $requestTarget,
            'Digest:' . $digest,
        ]);

        $hmac = base64_encode(hash_hmac('sha256', $componentSignature, $this->secretKey, true));

        return 'HMACSHA256=' . $hmac;
    }
}
