<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(
        public int $orderId,
        public string $type
    ) {
    }

    public function handle(NotificationService $notificationService): void
    {
        /** @var Order|null $order */
        $order = Order::query()->where('id', '=', $this->orderId)->first();
        if (! $order) {
            return;
        }

        try {
            match ($this->type) {
                'order_created' => $notificationService->sendOrderCreated($order),
                'order_paid' => $notificationService->sendOrderPaid($order),
                'order_shipped' => $notificationService->sendOrderShipped($order),
                default => Log::warning("Unknown notification type: {$this->type}"),
            };
        } catch (\Throwable $e) {
            Log::error("SendWhatsAppNotificationJob failed for Order #{$order->order_number}: {$e->getMessage()}");
            throw $e;
        }
    }
}
