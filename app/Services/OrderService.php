<?php

namespace App\Services;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\Shipment;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected CartService $cartService,
        protected ?NotificationService $notificationService = null,
        protected ?BiteshipService $biteshipService = null
    ) {
    }

    /**
     * Create new order from checkout details.
     *
     * @param  array{
     *     customer_name: string,
     *     customer_email?: string|null,
     *     customer_phone: string,
     *     shipping_address: string,
     *     province?: string|null,
     *     city?: string|null,
     *     district?: string|null,
     *     postal_code?: string|null,
     *     destination_area_id: string,
     *     courier_code: string,
     *     courier_service_code?: string|null,
     *     courier_service_name: string,
     *     shipping_fee: float,
     *     voucher_code?: string|null,
     *     discount_amount?: float|null,
     *     payment_method?: string|null,
     *     notes?: string|null,
     *     user_id?: int|null
     * }  $checkoutData
     */
    public function createOrder(array $checkoutData): Order
    {
        $cartItems = $this->cartService->getItems();

        if ($cartItems->isEmpty()) {
            throw new \RuntimeException('Keranjang belanja kosong.');
        }

        $order = DB::transaction(function () use ($checkoutData, $cartItems) {
            $subtotal = 0.0;
            $totalWeightG = 0;
            $orderItemsData = [];

            foreach ($cartItems as $item) {
                /** @var ProductVariant $variant */
                $variant = $item['variant'];

                /** @var ProductVariant|null $currentVariant */
                $currentVariant = ProductVariant::with('product')->lockForUpdate()->find($variant->id);
                if (! $currentVariant || $currentVariant->stock < $item['quantity']) {
                    $productName = $currentVariant ? $currentVariant->product->name : $variant->product->name;
                    throw new \RuntimeException("Stok untuk varian {$productName} tidak mencukupi.");
                }

                $itemSubtotal = (float) $currentVariant->price * $item['quantity'];
                $itemWeight = (int) $currentVariant->weight_grams * $item['quantity'];

                $subtotal += $itemSubtotal;
                $totalWeightG += $itemWeight;

                $orderItemsData[] = [
                    'product_variant_id' => $currentVariant->id,
                    'product_name' => $currentVariant->product->name,
                    'variant_label' => "{$currentVariant->grind_type_label}, {$currentVariant->weight_grams}g",
                    'price_at_purchase' => (float) $currentVariant->price,
                    'quantity' => $item['quantity'],
                ];

                $currentVariant->decrement('stock', $item['quantity'], []);
            }

            $shippingFee = max(0.0, (float) $checkoutData['shipping_fee']);
            $discountAmount = max(0.0, (float) ($checkoutData['discount_amount'] ?? 0.0));
            $voucherCode = ! empty($checkoutData['voucher_code']) ? strtoupper(trim((string) $checkoutData['voucher_code'])) : null;

            $uniqueCode = rand(100, 999);
            $totalAmount = max(0.0, $subtotal + $shippingFee - $discountAmount) + $uniqueCode;
            $paymentMethod = $checkoutData['payment_method'] ?? 'bank_transfer';
            $initialStatus = ($paymentMethod === 'cod') ? 'processing' : 'pending_payment';

            $order = Order::create([
                'user_id' => $checkoutData['user_id'] ?? null,
                'guest_email' => $checkoutData['customer_email'] ?? null,
                'guest_phone' => $checkoutData['customer_phone'],
                'recipient_name' => $checkoutData['customer_name'],
                'recipient_phone' => $checkoutData['customer_phone'],
                'shipping_address' => $checkoutData['shipping_address'],
                'province' => $checkoutData['province'] ?? 'Jawa Barat',
                'city' => $checkoutData['city'] ?? 'Kota Bogor',
                'district' => $checkoutData['district'] ?? 'Bogor Barat',
                'postal_code' => $checkoutData['postal_code'] ?? '16115',
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingFee,
                'voucher_code' => $voucherCode,
                'discount_amount' => $discountAmount,
                'unique_code' => $uniqueCode,
                'total' => $totalAmount,
                'status' => $initialStatus,
                'courier_name' => $checkoutData['courier_service_name'],
                'notes' => $checkoutData['notes'] ?? null,
            ]);

            $order->items()->createMany($orderItemsData);

            \App\Models\Payment::create([
                'order_id' => $order->id,
                'method' => $paymentMethod,
                'amount' => $totalAmount,
                'status' => 'pending',
            ]);

            Shipment::create([
                'order_id' => $order->id,
                'courier_code' => $checkoutData['courier_code'],
                'courier_service_code' => $checkoutData['courier_service_code'] ?? null,
                'courier_service' => $checkoutData['courier_service_name'],
                'destination_area_id' => $checkoutData['destination_area_id'] ?? null,
                'status' => 'pending',
            ]);

            $order->statusHistories()->create([
                'from_status' => null,
                'to_status' => $initialStatus,
                'changed_by' => $checkoutData['user_id'] ?? null,
                'note' => 'Pesanan baru dibuat via checkout.',
                'created_at' => now(),
            ]);

            $this->cartService->clear();

            return $order;
        });

        // Trigger Biteship order creation for COD or auto-create setting
        if (config('services.biteship.auto_create_order', true) && ($checkoutData['payment_method'] ?? 'bank_transfer') === 'cod') {
            try {
                $biteshipService = $this->biteshipService ?? app(BiteshipService::class);
                $biteshipService->createOrder($order);
            } catch (\Throwable $e) {
                // Keep order created even if Biteship request encounters an issue
            }
        }

        // Trigger WhatsApp Notification asynchronously
        try {
            \App\Jobs\SendWhatsAppNotificationJob::dispatch($order->id, 'order_created');
        } catch (\Throwable $e) {
            try {
                $notificationService = $this->notificationService ?? app(NotificationService::class);
                $notificationService->sendOrderCreated($order);
            } catch (\Throwable $ex) {
                // Ignore notification failure so order is created smoothly
            }
        }

        return $order;
    }
}
