<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaService
{
    protected string $apiUrl;

    protected string $apiKey;

    protected string $session;

    public function __construct()
    {
        $this->apiUrl = rtrim((string) config('services.waha.api_url', 'http://localhost:3000'), '/');
        $this->apiKey = (string) config('services.waha.api_key', '');
        $this->session = (string) config('services.waha.session', 'default');
    }

    /**
     * Send WhatsApp text message via WAHA API.
     */
    public function sendTextMessage(string $phone, string $text): bool
    {
        $chatId = $this->formatChatId($phone);

        if (empty($this->apiUrl)) {
            Log::info("WAHA (Mock) message to {$chatId}: {$text}");

            return true;
        }

        try {
            $request = Http::timeout(5);
            if (! empty($this->apiKey)) {
                $request = $request->withHeaders(['X-Api-Key' => $this->apiKey]);
            }

            $response = $request->post("{$this->apiUrl}/api/sendText", [
                'session' => $this->session,
                'chatId' => $chatId,
                'text' => $text,
            ]);

            if ($response->successful()) {
                return true;
            }

            Log::warning("WAHA sendTextMessage failed: {$response->body()}");
        } catch (\Throwable $e) {
            Log::warning("WAHA sendTextMessage exception: {$e->getMessage()}");
        }

        return false;
    }

    /**
     * Format phone number to WAHA chatId format (e.g. 6281234567890@c.us).
     */
    public function formatChatId(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone) ?? '';

        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62'.substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62'.$cleaned;
        }

        return str_contains($cleaned, '@') ? $cleaned : "{$cleaned}@c.us";
    }
}
