<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Shipment;
use App\Models\WebhookEvent;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BiteshipWebhookController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $sigHeader = $request->header('x-biteship-signature');
        $expected = (string) config('services.biteship.webhook_secret', '');

        if (empty($expected) || ! is_string($sigHeader) || ! hash_equals($expected, $sigHeader)) {
            Log::warning('Biteship webhook signature invalid or unconfigured.', ['received' => $sigHeader]);

            return response()->json(['message' => 'Forbidden: Invalid signature'], 403);
        }

        $payload = $request->all();
        $eventId = (string) ($payload['id'] ?? $payload['event_id'] ?? '');

        if (empty($eventId)) {
            $orderIdPart = (string) ($payload['order_id'] ?? $payload['order']['id'] ?? '');
            $statusPart = (string) ($payload['status'] ?? '');
            $waybillPart = (string) ($payload['courier_waybill_id'] ?? $payload['waybill_id'] ?? '');
            $eventPart = (string) ($payload['event'] ?? 'status');

            if ($orderIdPart) {
                $eventId = "biteship_{$orderIdPart}_{$eventPart}_{$statusPart}_{$waybillPart}";
            } else {
                $eventId = 'biteship_evt_'.md5(json_encode($payload));
            }
        }

        if (WebhookEvent::alreadyProcessed('biteship', $eventId)) {
            return response()->json(['message' => 'Webhook event already processed'], 200);
        }

        $status = strtoupper((string) ($payload['status'] ?? ''));
        $biteshipOrderId = (string) ($payload['order_id'] ?? $payload['order']['id'] ?? '');

        $processedShipment = null;

        try {
            DB::transaction(function () use ($eventId, $payload, $status, $biteshipOrderId, &$processedShipment) {
                WebhookEvent::create([
                    'source' => 'biteship',
                    'event_id' => $eventId,
                    'event_type' => $status,
                    'payload' => $payload,
                ]);

                if (empty($biteshipOrderId)) {
                    return;
                }

                /** @var Shipment|null $shipment */
                $shipment = Shipment::query()
                    ->where('biteship_order_id', '=', $biteshipOrderId)
                    ->orWhereHas('order', function ($q) use ($payload) {
                        if (! empty($payload['reference_id'])) {
                            $q->where('order_number', '=', $payload['reference_id']);
                        }
                    })->first();

                if (! $shipment) {
                    return;
                }

                $processedShipment = $shipment;

                $courierData = $payload['courier'] ?? [];
                $waybillId = (string) ($payload['courier_waybill_id'] ?? $courierData['waybill_id'] ?? $payload['waybill_id'] ?? $payload['courier_tracking_id'] ?? $courierData['tracking_id'] ?? $payload['tracking_id'] ?? '');
                $labelUrl = (string) ($payload['courier_link'] ?? $courierData['link'] ?? $payload['label_url'] ?? '');

                if ($waybillId && empty($shipment->tracking_number)) {
                    $shipment->tracking_number = $waybillId;
                }
                if ($labelUrl && empty($shipment->label_url)) {
                    $shipment->label_url = $labelUrl;
                }
                if (empty($shipment->biteship_order_id)) {
                    $shipment->biteship_order_id = $biteshipOrderId;
                }

                /** @var Order|null $order */
                $order = Order::query()->find($shipment->order_id);

                if ($status === 'DELIVERED') {
                    $shipment->update([
                        'status' => 'delivered',
                        'delivered_at' => $shipment->delivered_at ?: now(),
                        'tracking_number' => $waybillId ?: $shipment->tracking_number,
                        'label_url' => $labelUrl ?: $shipment->label_url,
                    ]);

                    if ($order && $order->status !== 'delivered') {
                        $order->recordStatusChange('delivered', null, "Biteship Webhook (DELIVERED): {$eventId}");
                    }
                } elseif (in_array($status, ['CANCELLED', 'CANCELED', 'REJECTED'])) {
                    $shipment->update([
                        'status' => 'failed',
                        'tracking_number' => $waybillId ?: $shipment->tracking_number,
                    ]);

                    if ($order && $order->status !== 'cancelled') {
                        $order->recordStatusChange('cancelled', null, "Biteship Webhook ({$status}): {$eventId}");
                    }
                } elseif (in_array($status, ['PICKING_UP', 'PICKED', 'DROPPING_OFF', 'IN_TRANSIT'])) {
                    $shipment->update([
                        'status' => 'in_transit',
                        'shipped_at' => $shipment->shipped_at ?: now(),
                        'tracking_number' => $waybillId ?: $shipment->tracking_number,
                        'label_url' => $labelUrl ?: $shipment->label_url,
                    ]);

                    if ($order && in_array($order->status, ['pending_payment', 'paid', 'processing'])) {
                        $order->recordStatusChange('shipped', null, "Biteship Webhook ({$status}): {$eventId}" . ($waybillId ? " (Resi: {$waybillId})" : ''));
                    }
                } elseif (in_array($status, ['ALLOCATED', 'CONFIRMED', 'PLACED'])) {
                    $shipment->update([
                        'status' => 'booked',
                        'tracking_number' => $waybillId ?: $shipment->tracking_number,
                        'label_url' => $labelUrl ?: $shipment->label_url,
                    ]);

                    if ($order && $order->status === 'pending_payment' && $order->payment?->method === 'cod') {
                        $order->recordStatusChange('processing', null, "Biteship Webhook ({$status}): {$eventId}");
                    }
                } else {
                    $shipment->save();
                }
            });
        } catch (QueryException $e) {
            return response()->json(['message' => 'Webhook event already processed'], 200);
        }

        return response()->json(['message' => 'Webhook processed successfully'], 200);
    }
}
