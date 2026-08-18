<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelExpiredOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending payment orders that have passed their expiration time and restore product variant stock';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $expiredOrders = Order::query()
            ->with('items')
            ->where('status', 'pending_payment')
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredOrders->isEmpty()) {
            $this->info('No expired orders found.');
            return self::SUCCESS;
        }

        $cancelledCount = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order, &$cancelledCount) {
                // Double-check status inside transaction
                /** @var Order|null $freshOrder */
                $freshOrder = Order::lockForUpdate()->find($order->id);

                if (! $freshOrder || $freshOrder->status !== 'pending_payment') {
                    return;
                }

                $freshOrder->recordStatusChange('expired', null, 'Dibatalkan otomatis oleh sistem karena melewati batas waktu pembayaran.');

                foreach ($freshOrder->items as $item) {
                    /** @var OrderItem $item */
                    $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                    }
                }

                $cancelledCount++;
            });
        }

        $message = "Successfully cancelled {$cancelledCount} expired order(s) and restored stock.";
        $this->info($message);
        Log::info("CancelExpiredOrdersCommand: {$message}");

        return self::SUCCESS;
    }
}
