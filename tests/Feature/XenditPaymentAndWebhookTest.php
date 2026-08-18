<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\XenditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class XenditPaymentAndWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_xendit_service_dapat_membuat_invoice(): void
    {
        $order = Order::create([
            'order_number' => 'WAY-20260802-TEST01',
            'guest_email' => 'customer@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Test Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Test No 123',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 45000.00,
            'shipping_cost' => 10000.00,
            'total' => 55000.00,
            'status' => 'pending_payment',
        ]);

        /** @var XenditService $xenditService */
        $xenditService = app(XenditService::class);
        $invoice = $xenditService->createInvoice($order);

        $this->assertNotEmpty($invoice);
        $this->assertArrayHasKey('invoice_url', $invoice);
        $this->assertEquals('WAY-20260802-TEST01', $invoice['external_id']);
    }

    public function test_webhook_xendit_menolak_token_invalid(): void
    {
        config(['services.xendit.webhook_token' => 'SECRET_WEBHOOK_TOKEN_123']);

        $response = $this->withHeaders([
            'x-callback-token' => 'WRONG_TOKEN',
        ])->postJson('/webhooks/xendit', [
            'id' => 'evt_12345',
            'external_id' => 'WAY-20260802-TEST02',
            'status' => 'PAID',
        ]);

        $response->assertStatus(403);
    }

    public function test_webhook_xendit_memproses_pembayaran_paid_dan_idempotent(): void
    {
        config(['services.xendit.webhook_token' => 'SECRET_WEBHOOK_TOKEN_123']);

        $order = Order::create([
            'order_number' => 'WAY-20260802-PAID01',
            'guest_email' => 'paid@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Paid Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Test Paid No 1',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 90000.00,
            'shipping_cost' => 10000.00,
            'total' => 100000.00,
            'status' => 'pending_payment',
        ]);

        $payload = [
            'id' => 'xendit_evt_998877',
            'external_id' => 'WAY-20260802-PAID01',
            'status' => 'PAID',
            'amount' => 100000.00,
            'payment_method' => 'QRIS',
        ];

        // First attempt
        $response1 = $this->withHeaders([
            'x-callback-token' => 'SECRET_WEBHOOK_TOKEN_123',
        ])->postJson('/webhooks/xendit', $payload);

        $response1->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'succeeded',
            'amount' => 100000.00,
        ]);

        // Duplicate attempt (idempotency check)
        $response2 = $this->withHeaders([
            'x-callback-token' => 'SECRET_WEBHOOK_TOKEN_123',
        ])->postJson('/webhooks/xendit', $payload);

        $response2->assertStatus(200);
        $response2->assertJson(['message' => 'Webhook event already processed']);
    }

    public function test_webhook_xendit_expired_mengembalikan_stok_varian(): void
    {
        config(['services.xendit.webhook_token' => 'SECRET_WEBHOOK_TOKEN_123']);

        $product = Product::factory()->create(['name' => 'Way Kopi Stock', 'is_active' => true]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-STOCK-200',
            'grind_type' => 'fine',
            'weight_grams' => 200,
            'price' => 50000.00,
            'stock' => 5,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'WAY-20260802-EXP01',
            'guest_email' => 'exp@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Exp Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Exp No 2',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 50000.00,
            'shipping_cost' => 10000.00,
            'total' => 60000.00,
            'status' => 'pending_payment',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_variant_id' => $variant->id,
            'product_name' => 'Way Kopi Stock',
            'variant_label' => 'Bubuk Halus, 200g',
            'price_at_purchase' => 50000.00,
            'quantity' => 2,
        ]);

        $payload = [
            'id' => 'xendit_evt_exp_112233',
            'external_id' => 'WAY-20260802-EXP01',
            'status' => 'EXPIRED',
        ];

        $response = $this->withHeaders([
            'x-callback-token' => 'SECRET_WEBHOOK_TOKEN_123',
        ])->postJson('/webhooks/xendit', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'expired',
        ]);

        // Stock restored from 5 to 7
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 7,
        ]);
    }
}
