<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\WahaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WahaNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_waha_service_memformat_nomor_telepon_dengan_benar(): void
    {
        $waha = new WahaService();

        $this->assertEquals('6281234567890@c.us', $waha->formatChatId('081234567890'));
        $this->assertEquals('6281999888777@c.us', $waha->formatChatId('81999888777'));
        $this->assertEquals('6281234567890@c.us', $waha->formatChatId('+6281234567890'));
    }

    public function test_pembuatan_pesanan_merekam_log_notifikasi_wa_order_created(): void
    {
        Http::fake([
            '*/api/sendText' => Http::response(['status' => 'success'], 200),
        ]);

        $product = Product::factory()->create();
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-TEST-250',
            'grind_type' => 'whole_bean',
            'weight_grams' => 250,
            'price' => 50000.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $cartService = app(CartService::class);
        $cartService->addItem($variant->id, 2);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrder([
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Kopi No. 12',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'destination_area_id' => 'IDNP6IDNC385IDND3366',
            'courier_code' => 'jne',
            'courier_service_name' => 'JNE REG',
            'shipping_fee' => 12000.0,
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'order_id' => $order->id,
            'recipient' => '081234567890',
            'template_key' => 'order_created',
            'status' => 'sent',
        ]);
    }

    public function test_pembayaran_lunas_merekam_log_notifikasi_wa_pelanggan_dan_admin(): void
    {
        Http::fake([
            '*/api/sendText' => Http::response(['status' => 'success'], 200),
        ]);

        config(['services.xendit.webhook_token' => 'test_token_123']);
        config(['services.waha.admin_phone' => '6281234567890']);

        $order = Order::create([
            'order_number' => 'WAY-20260803-WA01',
            'guest_email' => 'wa@example.com',
            'guest_phone' => '081999888777',
            'recipient_name' => 'WA Test Customer',
            'recipient_phone' => '081999888777',
            'shipping_address' => 'Jl. WA Test No 5',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 90000.00,
            'shipping_cost' => 10000.00,
            'total' => 100000.00,
            'status' => 'pending_payment',
            'courier_name' => 'JNE REG',
        ]);

        $response = $this->withHeaders(['x-callback-token' => 'test_token_123'])
            ->postJson('/webhooks/xendit', [
                'id' => 'evt_wa_paid_123',
                'external_id' => 'WAY-20260803-WA01',
                'status' => 'PAID',
                'amount' => 100000,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('notification_logs', [
            'order_id' => $order->id,
            'recipient' => '081999888777',
            'template_key' => 'order_paid_customer',
            'status' => 'sent',
        ]);

        $this->assertDatabaseHas('notification_logs', [
            'order_id' => $order->id,
            'template_key' => 'order_paid_admin',
            'status' => 'sent',
        ]);
    }
}
