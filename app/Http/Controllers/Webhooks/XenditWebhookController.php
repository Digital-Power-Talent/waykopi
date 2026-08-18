<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\WebhookEvent;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        // 1. Verify Callback Token
        $tokenHeader = $request->header('x-callback-token');
        $expectedToken = (string) config('services.xendit.webhook_token', '');

        if (empty($expectedToken) || ! is_string($tokenHeader) || ! hash_equals($expectedToken, $tokenHeader)) {
            Log::warning('Xendit webhook token invalid or unconfigured.', ['received' => $tokenHeader]);

            return response()->json(['message' => 'Forbidden: Invalid callback token'], 403);
        }

        $payload = $request->all();
        $eventId = (string) ($payload['id'] ?? $payload['external_id'] ?? '');

        if (empty($eventId)) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // 2. Idempotency Check via WebhookEvent helper
        if (WebhookEvent::alreadyProcessed('xendit', $eventId)) {
            return response()->json(['message' => 'Webhook event already processed'], 200);
        }

        $orderNumber = (string) ($payload['external_id'] ?? '');
        $status = strtoupper((string) ($payload['status'] ?? ''));

        /** @var Order|null $processedOrder */
        $processedOrder = null;

        try {
            DB::transaction(function () use ($eventId, $payload, $orderNumber, $status, &$processedOrder) {
                // Save Webhook Event log (payload is cast to array in WebhookEvent model)
                WebhookEvent::create([
                    'source' => 'xendit',
                    'event_id' => $eventId,
                    'event_type' => $status,
                    'payload' => $payload,
                ]);

                $order = Order::with('items')->where('order_number', $orderNumber)->first();
                if (! $order) {
                    return;
                }

                $processedOrder = $order;

                if (in_array($status, ['PAID', 'SETTLED'])) {
                    $order->recordStatusChange('paid', null, "Xendit Webhook ({$status}): Invoice {$eventId}");

                    Payment::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'xendit_invoice_id' => $eventId,
                            'method' => $payload['payment_type'] ?? $payload['payment_method'] ?? 'XENDIT_INVOICE',
                            'amount' => (float) ($payload['amount'] ?? $order->total),
                            'status' => 'succeeded',
                            'paid_at' => now(),
                        ]
                    );
                } elseif ($status === 'EXPIRED') {
                    if ($order->status !== 'expired') {
                        $order->recordStatusChange('expired', null, "Xendit Webhook (EXPIRED): Invoice {$eventId}");

                        // Restore variant stock with row lock
                        foreach ($order->items as $item) {
                            /** @var OrderItem $item */
                            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                            if ($variant) {
                                $variant->increment('stock', $item->quantity);
                            }
                        }
                    }
                }
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle unique constraint failure for concurrent duplicate webhook events
            return response()->json(['message' => 'Webhook event already processed'], 200);
        }

        if ($processedOrder && in_array($status, ['PAID', 'SETTLED'])) {
            try {
                \App\Jobs\SendWhatsAppNotificationJob::dispatch($processedOrder->id, 'order_paid');
            } catch (\Throwable $e) {
                try {
                    /** @var NotificationService $notificationService */
                    $notificationService = app(NotificationService::class);
                    $notificationService->sendOrderPaid($processedOrder);
                } catch (\Throwable $ex) {
                    Log::warning("Failed to send order paid notification: {$ex->getMessage()}");
                }
            }
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
