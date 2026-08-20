<?php

namespace Tests\Feature;

use App\Livewire\Admin\OrderManager;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\User;
use App\Services\BiteshipService;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class BiteshipOrderIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create([
            'email' => 'admin@waykopi.com',
            'role' => 'admin',
        ]);
    }

    public function test_order_service_stores_destination_area_and_courier_code(): void
    {
        $product = Product::create([
            'name' => 'Kopi Arabika Gayo',
            'slug' => 'kopi-arabika-gayo',
            'description' => 'Kopi Arabika Gayo berkualitas tinggi dengan aroma rempah khas.',
            'origin' => 'Aceh Gayo',
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'weight_grams' => 250,
            'grind_type' => 'whole_bean',
            'price' => 85000,
            'stock' => 10,
            'sku' => 'GAYO-250-WHOLE',
            'is_active' => true,
        ]);

        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($variant->id, 2);

        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);

        $order = $orderService->createOrder([
            'customer_name' => 'Budi Pratama',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Kebon Jeruk No. 25',
            'destination_area_id' => 'IDNP9IDNC74IDND6752IDZ16320',
            'courier_code' => 'sicepat',
            'courier_service_code' => 'reg',
            'courier_service_name' => 'SiCepat REG',
            'shipping_fee' => 15000,
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'recipient_name' => 'Budi Pratama',
            'courier_name' => 'SiCepat REG',
        ]);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'courier_code' => 'sicepat',
            'courier_service_code' => 'reg',
            'destination_area_id' => 'IDNP9IDNC74IDND6752IDZ16320',
        ]);
    }

    public function test_biteship_service_creates_order_via_api(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/orders' => Http::response([
                'success' => true,
                'message' => 'Order successfully created',
                'object' => 'order',
                'id' => '660105377589b8dea565208b',
                'status' => 'confirmed',
                'courier' => [
                    'tracking_id' => 'TRK-BITESHIP-9988',
                    'waybill_id' => 'WYB-JNE-123456789',
                    'company' => 'jne',
                    'type' => 'reg',
                    'link' => 'https://biteship.com/labels/660105377589b8dea565208b',
                ],
                'price' => 12000,
            ], 200),
        ]);

        config(['services.biteship.api_key' => 'biteship_test_live_key_123']);

        $order = Order::create([
            'order_number' => 'WK-20260820-TEST1',
            'recipient_name' => 'John Doe',
            'recipient_phone' => '081298765432',
            'shipping_address' => 'Jl. Sudirman No. 10, Jakarta',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'district' => 'Menteng',
            'postal_code' => '10310',
            'subtotal' => 100000,
            'shipping_cost' => 12000,
            'total' => 112500,
            'status' => 'paid',
            'courier_name' => 'JNE REG',
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'courier_code' => 'jne',
            'courier_service_code' => 'reg',
            'courier_service' => 'JNE REG',
            'destination_area_id' => 'IDNP31IDNC157IDND1280',
            'status' => 'pending',
        ]);

        /** @var BiteshipService $biteshipService */
        $biteshipService = app(BiteshipService::class);
        $result = $biteshipService->createOrder($order);

        $this->assertTrue($result['success']);
        $this->assertEquals('660105377589b8dea565208b', $result['order_id']);
        $this->assertEquals('WYB-JNE-123456789', $result['tracking_number']);

        $shipment->refresh();
        $this->assertEquals('660105377589b8dea565208b', $shipment->biteship_order_id);
        $this->assertEquals('WYB-JNE-123456789', $shipment->tracking_number);
        $this->assertEquals('https://biteship.com/labels/660105377589b8dea565208b', $shipment->label_url);
    }

    public function test_admin_can_send_order_to_biteship_and_sync_status(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/orders' => Http::response([
                'success' => true,
                'id' => 'BTS-ORDER-999',
                'status' => 'placed',
                'courier' => [
                    'waybill_id' => 'WYB-SICEPAT-888',
                    'company' => 'sicepat',
                    'type' => 'reg',
                    'link' => 'https://biteship.com/labels/BTS-ORDER-999',
                ],
            ], 200),
            'https://api.biteship.com/v1/orders/BTS-ORDER-999' => Http::response([
                'success' => true,
                'id' => 'BTS-ORDER-999',
                'status' => 'in_transit',
                'courier' => [
                    'waybill_id' => 'WYB-SICEPAT-888',
                    'company' => 'sicepat',
                    'type' => 'reg',
                    'link' => 'https://biteship.com/labels/BTS-ORDER-999',
                ],
            ], 200),
        ]);

        config(['services.biteship.api_key' => 'biteship_test_key']);

        $order = Order::create([
            'order_number' => 'WK-20260820-ADM01',
            'recipient_name' => 'Admin Customer',
            'recipient_phone' => '081299998888',
            'shipping_address' => 'Jl. Pajajaran No. 88, Bogor',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Tengah',
            'postal_code' => '16121',
            'subtotal' => 150000,
            'shipping_cost' => 10000,
            'total' => 160000,
            'status' => 'paid',
            'courier_name' => 'SiCepat REG',
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'courier_code' => 'sicepat',
            'courier_service_code' => 'reg',
            'courier_service' => 'SiCepat REG',
            'destination_area_id' => 'IDNP6IDNC385IDND3367',
            'status' => 'pending',
        ]);

        $this->actingAs($this->adminUser);

        // Test Livewire sendToBiteship action
        Livewire::test(OrderManager::class)
            ->call('sendToBiteship', $order->id)
            ->assertSee('berhasil dikirim ke Biteship');

        $shipment->refresh();
        $this->assertEquals('BTS-ORDER-999', $shipment->biteship_order_id);
        $this->assertEquals('WYB-SICEPAT-888', $shipment->tracking_number);

        // Test Livewire syncBiteshipStatus action
        Livewire::test(OrderManager::class)
            ->call('syncBiteshipStatus', $order->id)
            ->assertSee('berhasil disinkronkan dari Biteship');

        $shipment->refresh();
        $this->assertEquals('in_transit', $shipment->status);
    }

    public function test_biteship_service_retrieves_and_cancels_order(): void
    {
        Http::fake([
            'https://api.biteship.com/v1/orders/BTS-GET-123' => Http::response([
                'success' => true,
                'id' => 'BTS-GET-123',
                'status' => 'allocated',
                'courier' => ['waybill_id' => 'WYB-12345'],
            ], 200),
            'https://api.biteship.com/v1/orders/BTS-GET-123/cancel' => Http::response([
                'success' => true,
                'message' => 'Order successfully deleted',
                'id' => 'BTS-GET-123',
                'status' => 'cancelled',
            ], 200),
        ]);

        config(['services.biteship.api_key' => 'biteship_test_key']);

        /** @var BiteshipService $biteshipService */
        $biteshipService = app(BiteshipService::class);

        $orderData = $biteshipService->getOrder('BTS-GET-123');
        $this->assertNotNull($orderData);
        $this->assertEquals('BTS-GET-123', $orderData['id']);

        $cancelResult = $biteshipService->cancelOrder('BTS-GET-123', 'change_courier', 'Ganti ekspedisi');
        $this->assertTrue($cancelResult['success']);
    }

    public function test_webhook_biteship_updates_waybill_and_in_transit_status(): void
    {
        config(['services.biteship.webhook_secret' => 'TEST_SECRET_123']);

        $order = Order::create([
            'order_number' => 'WK-BTS-TRANSIT-01',
            'recipient_name' => 'Transit Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Transit No. 1',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 50000,
            'shipping_cost' => 10000,
            'total' => 60000,
            'status' => 'processing',
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'biteship_order_id' => 'BTS-ORDER-TRANSIT-1',
            'courier_code' => 'jne',
            'courier_service' => 'REG',
            'status' => 'booked',
        ]);

        $payload = [
            'id' => 'biteship_evt_transit_1',
            'order_id' => 'BTS-ORDER-TRANSIT-1',
            'status' => 'IN_TRANSIT',
            'courier' => [
                'waybill_id' => 'WYB-JNE-TRANSIT-888',
                'link' => 'https://biteship.com/labels/BTS-ORDER-TRANSIT-1',
            ],
        ];

        $response = $this->withHeaders([
            'x-biteship-signature' => 'TEST_SECRET_123',
        ])->postJson('/webhooks/biteship', $payload);

        $response->assertStatus(200);

        $shipment->refresh();
        $order->refresh();

        $this->assertEquals('in_transit', $shipment->status);
        $this->assertEquals('WYB-JNE-TRANSIT-888', $shipment->tracking_number);
        $this->assertEquals('https://biteship.com/labels/BTS-ORDER-TRANSIT-1', $shipment->label_url);
        $this->assertEquals('shipped', $order->status);
    }
}
