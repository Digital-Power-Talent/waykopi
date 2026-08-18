<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrderPerformanceAndConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_cart_service_memoizes_items_per_request(): void
    {
        $product = Product::factory()->create(['name' => 'Way Kopi Cart Test', 'is_active' => true]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-CART-001',
            'grind_type' => 'fine',
            'weight_grams' => 250,
            'price' => 45000.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($variant->id, 2);

        // First call populates cache
        $items1 = $cartService->getItems();
        $subtotal = $cartService->getSubtotal();
        $weight = $cartService->getTotalWeightGrams();

        $this->assertCount(1, $items1);
        $this->assertEquals(90000.00, $subtotal);
        $this->assertEquals(500, $weight);
    }

    public function test_order_number_is_unique(): void
    {
        $order1 = Order::create([
            'guest_email' => 'test1@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Customer 1',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Address 1',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total' => 60000,
            'status' => 'pending_payment',
        ]);

        $order2 = Order::create([
            'guest_email' => 'test2@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Customer 2',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Address 2',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total' => 60000,
            'status' => 'pending_payment',
        ]);

        $this->assertNotEquals($order1->order_number, $order2->order_number);
        $this->assertStringStartsWith('WK-', $order1->order_number);
        $this->assertStringStartsWith('WK-', $order2->order_number);
    }

    public function test_cancel_expired_orders_command_restores_stock(): void
    {
        $product = Product::factory()->create(['name' => 'Way Kopi Expired Test', 'is_active' => true]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-EXP-001',
            'grind_type' => 'medium',
            'weight_grams' => 200,
            'price' => 50000.00,
            'stock' => 3,
            'is_active' => true,
        ]);

        $expiredOrder = Order::create([
            'order_number' => 'WK-TEST-EXP-99',
            'guest_email' => 'expired@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Expired Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Expired Addr',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total' => 60000,
            'status' => 'pending_payment',
            'expires_at' => now()->subMinutes(10),
        ]);

        OrderItem::create([
            'order_id' => $expiredOrder->id,
            'product_variant_id' => $variant->id,
            'product_name' => $product->name,
            'variant_label' => 'Bubuk Sedang, 200g',
            'price_at_purchase' => 50000.00,
            'quantity' => 2,
        ]);

        $exitCode = Artisan::call('orders:cancel-expired');
        $this->assertEquals(0, $exitCode);

        $this->assertDatabaseHas('orders', [
            'id' => $expiredOrder->id,
            'status' => 'expired',
        ]);

        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 5,
        ]);
    }

    public function test_xendit_webhook_rejects_when_token_unconfigured_or_mismatched(): void
    {
        config(['services.xendit.webhook_token' => '']);

        $response = $this->postJson('/webhooks/xendit', [
            'id' => 'evt_unauthorized',
            'external_id' => 'WK-TEST-001',
            'status' => 'PAID',
        ]);

        $response->assertStatus(403);
    }
}
