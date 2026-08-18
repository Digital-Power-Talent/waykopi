<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\BiteshipService;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CartAndCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_dapat_menambahkan_item_ke_keranjang_dan_menghitung_subtotal(): void
    {
        $product = Product::factory()->create(['name' => 'Way Kopi Fine Robusta', 'is_active' => true]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-TEST-200',
            'grind_type' => 'fine',
            'weight_grams' => 200,
            'price' => 50000.00,
            'stock' => 20,
            'is_active' => true,
        ]);

        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($variant->id, 2);

        $this->assertEquals(1, $cartService->getItems()->count());
        $this->assertEquals(100000.00, $cartService->getSubtotal());
        $this->assertEquals(400, $cartService->getTotalWeightGrams());
    }

    public function test_biteship_service_dapat_mencari_area_dan_menghitung_ongkir(): void
    {
        /** @var BiteshipService $biteshipService */
        $biteshipService = app(BiteshipService::class);

        $areas = $biteshipService->searchAreas('Bogor');
        $this->assertNotEmpty($areas);
        $this->assertStringContainsString('Bogor', $areas[0]['name']);

        $items = [
            ['name' => 'Kopi Robusta', 'value' => 50000, 'quantity' => 2, 'weight' => 200],
        ];

        // 1. By Area ID
        $ratesArea = $biteshipService->getRatesByAreaId('IDNP6IDNC385IDND3366', $items);
        $this->assertNotEmpty($ratesArea);
        $this->assertGreaterThan(0, $ratesArea[0]['price']);

        // 2. By Postal Codes
        $ratesPostal = $biteshipService->getRatesByPostalCodes(12110, $items);
        $this->assertNotEmpty($ratesPostal);

        // 3. By Coordinates
        $ratesCoord = $biteshipService->getRatesByCoordinates(-6.2415, 106.8026, $items);
        $this->assertNotEmpty($ratesCoord);

        // 4. By Mix
        $ratesMix = $biteshipService->getRatesByMix(
            ['origin_area_id' => 'IDNP6IDNC385IDND3366'],
            ['destination_postal_code' => 12110],
            $items
        );
        $this->assertNotEmpty($ratesMix);

        // 5. By Type
        $ratesType = $biteshipService->getRatesByType('IDNP6IDNC385IDND3366', $items, 'standard');
        $this->assertNotEmpty($ratesType);
    }

    public function test_order_service_membuat_order_order_item_dan_shipment_dengan_perhitungan_server(): void
    {
        $product = Product::factory()->create(['name' => 'Way Kopi Bold', 'is_active' => true]);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-BOLD-500',
            'grind_type' => 'whole_bean',
            'weight_grams' => 500,
            'price' => 90000.00,
            'stock' => 15,
            'is_active' => true,
        ]);

        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($variant->id, 1);

        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);

        $order = $orderService->createOrder([
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Pajajaran No 10 (Bogor Barat, Kota Bogor)',
            'destination_area_id' => 'IDNP6IDNC385IDND3366',
            'courier_code' => 'sicepat',
            'courier_service_name' => 'SiCepat REG (Reguler)',
            'shipping_fee' => 10000.00,
        ]);

        $this->assertGreaterThanOrEqual(100, $order->unique_code);
        $this->assertLessThanOrEqual(999, $order->unique_code);
        $this->assertEquals(90000.00 + 10000.00 + $order->unique_code, (float) $order->total);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'recipient_name' => 'Budi Santoso',
            'guest_email' => 'budi@example.com',
            'subtotal' => 90000.00,
            'shipping_cost' => 10000.00,
            'unique_code' => $order->unique_code,
            'total' => 100000.00 + $order->unique_code,
            'status' => 'pending_payment',
        ]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_name' => 'Way Kopi Bold',
            'quantity' => 1,
            'price_at_purchase' => 90000.00,
        ]);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'courier_code' => 'sicepat',
            'courier_service' => 'SiCepat REG (Reguler)',
            'status' => 'pending',
        ]);

        $this->assertEquals(0, $cartService->getItems()->count());
    }

    public function test_halaman_checkout_dapat_diakses_dan_menampilkan_form(): void
    {
        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertSee('Checkout Pesanan');

        Livewire::test(\App\Livewire\Storefront\CheckoutPage::class)
            ->set('areaSearch', 'Bogor')
            ->call('searchArea')
            ->assertSet('areaResults', function ($results) {
                return ! empty($results);
            });
    }
}
