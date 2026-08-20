<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BiteshipWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_biteship_rejects_invalid_signature(): void
    {
        config(['services.biteship.webhook_secret' => 'TEST_SECRET_123']);

        $response = $this->withHeaders([
            'x-biteship-signature' => 'WRONG',
        ])->postJson('/webhooks/biteship', [
            'id' => 'evt_1',
            'order' => ['id' => 'BTS-1'],
            'status' => 'DELIVERED',
        ]);

        $response->assertStatus(403);
    }

    public function test_webhook_biteship_marks_shipment_delivered_and_idempotent(): void
    {
        config(['services.biteship.webhook_secret' => 'TEST_SECRET_123']);

        $order = Order::create([
            'order_number' => 'WAY-BTS-DEL-01',
            'guest_email' => 'x@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Delivery Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Test',
            'province' => 'Prov',
            'city' => 'City',
            'district' => 'Dist',
            'postal_code' => '16115',
            'subtotal' => 10000.00,
            'shipping_cost' => 5000.00,
            'total' => 15000.00,
            'status' => 'shipped',
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'biteship_order_id' => 'BTS-ORDER-001',
            'tracking_number' => 'TRK-1',
            'courier_code' => 'jne',
            'courier_service' => 'REG',
            'status' => 'in_transit',
        ]);

        $payload = [
            'id' => 'biteship_evt_100',
            'order' => ['id' => 'BTS-ORDER-001'],
            'status' => 'DELIVERED',
        ];

        // First attempt
        $response1 = $this->withHeaders([
            'x-biteship-signature' => 'TEST_SECRET_123',
        ])->postJson('/webhooks/biteship', $payload);

        $response1->assertStatus(200);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'delivered',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'delivered',
        ]);

        // Duplicate attempt should be idempotent
        $response2 = $this->withHeaders([
            'x-biteship-signature' => 'TEST_SECRET_123',
        ])->postJson('/webhooks/biteship', $payload);

        $response2->assertStatus(200);
        $response2->assertJson(['message' => 'Webhook event already processed']);
    }

    public function test_webhook_biteship_marks_shipment_cancelled(): void
    {
        config(['services.biteship.webhook_secret' => 'TEST_SECRET_123']);

        $order = Order::create([
            'order_number' => 'WAY-BTS-CAN-01',
            'guest_email' => 'y@example.com',
            'guest_phone' => '081234567891',
            'recipient_name' => 'Cancel Customer',
            'recipient_phone' => '081234567891',
            'shipping_address' => 'Jl. Cancel',
            'province' => 'Prov',
            'city' => 'City',
            'district' => 'Dist',
            'postal_code' => '16115',
            'subtotal' => 20000.00,
            'shipping_cost' => 5000.00,
            'total' => 25000.00,
            'status' => 'shipped',
        ]);

        $shipment = Shipment::create([
            'order_id' => $order->id,
            'biteship_order_id' => 'BTS-ORDER-002',
            'tracking_number' => 'TRK-2',
            'courier_code' => 'jne',
            'courier_service' => 'REG',
            'status' => 'in_transit',
        ]);

        $payload = [
            'id' => 'biteship_evt_200',
            'order' => ['id' => 'BTS-ORDER-002'],
            'status' => 'CANCELLED',
        ];

        $response = $this->withHeaders([
            'x-biteship-signature' => 'TEST_SECRET_123',
        ])->postJson('/webhooks/biteship', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'failed',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
    }
}
