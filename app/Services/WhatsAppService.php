<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public static function send(string $phone, string $message): bool
    {
        $token = config('services.fonnte.token');

        if (!$token) {
            return false;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::withHeaders(['Authorization' => $token])
                ->post('https://api.fonnte.com/send', [
                    'target' => $phone,
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::warning('WhatsApp send failed', ['phone' => $phone, 'status' => $response->status()]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('WhatsApp exception', ['phone' => $phone, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
