<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditService
{
    protected string $secretKey;

    protected string $baseUrl = 'https://api.xendit.co/v2';

    public function __construct()
    {
        $this->secretKey = (string) config('services.xendit.secret_key', '');
    }

    /**
     * Create Xendit Payment Link / Invoice for order.
     *
     * @return array{id: string, invoice_url: string, status: string, external_id: string, amount: float}
     */
    public function createInvoice(Order $order): array
    {
        if (empty($this->secretKey)) {
            return $this->getMockInvoice($order);
        }

        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->post("{$this->baseUrl}/invoices", [
                    'external_id' => $order->order_number,
                    'amount' => (float) $order->total,
                    'payer_email' => $order->guest_email,
                    'description' => "Pembayaran Pesanan Way Kopi #{$order->order_number}",
                    'invoice_duration' => 3600,
                    'success_redirect_url' => route('checkout.success', ['orderNumber' => $order->order_number]),
                    'failure_redirect_url' => route('checkout.payment', ['orderNumber' => $order->order_number]),
                    'currency' => 'IDR',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'id' => $data['id'] ?? 'inv_mock_'.time(),
                    'invoice_url' => $data['invoice_url'] ?? route('checkout.payment', ['orderNumber' => $order->order_number]),
                    'status' => $data['status'] ?? 'PENDING',
                    'external_id' => $order->order_number,
                    'amount' => (float) $order->total,
                ];
            }

            Log::error('Xendit createInvoice failed: '.$response->body());
        } catch (\Throwable $e) {
            Log::error("Xendit createInvoice exception: {$e->getMessage()}");
        }

        return $this->getMockInvoice($order);
    }

    /**
     * Mock invoice fallback when secret key is empty or call fails.
     *
     * @return array{id: string, invoice_url: string, status: string, external_id: string, amount: float}
     */
    protected function getMockInvoice(Order $order): array
    {
        return [
            'id' => 'inv_mock_'.md5($order->order_number),
            'invoice_url' => route('checkout.payment', ['orderNumber' => $order->order_number]),
            'status' => 'PENDING',
            'external_id' => $order->order_number,
            'amount' => (float) $order->total,
        ];
    }
}
