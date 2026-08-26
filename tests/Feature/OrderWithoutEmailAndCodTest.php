<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\BiteshipService;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class OrderWithoutEmailAndCodTest extends TestCase
{
    use RefreshDatabase;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Product $product */
        $product = Product::create([
            'name' => 'Robusta Tanggamus Test',
            'slug' => 'robusta-tanggamus-test',
            'description' => 'Biji kopi robusta pilihan dari Tanggamus Lampung.',
            'roast_profile' => 'Medium Dark',
            'origin' => 'Tanggamus, Lampung',
            'is_active' => true,
        ]);

        /** @var ProductVariant $variant */
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-ROBUSTA-TEST-200',
            'grind_type' => 'whole_bean',
            'weight_grams' => 200,
            'price' => 50000,
            'stock' => 50,
            'is_active' => true,
        ]);

        $this->variant = $variant;
    }

    public function test_can_checkout_without_email_using_bank_transfer(): void
    {
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 2);

        $biteshipMock = Mockery::mock(BiteshipService::class);
        $biteshipMock->shouldReceive('calculateRates')->andReturn([
            [
                'courier_code' => 'jne',
                'courier_name' => 'JNE',
                'courier_service_code' => 'reg',
                'courier_service_name' => 'Reguler',
                'price' => 15000,
                'duration' => '1-2 Hari',
            ],
        ]);
        app()->instance(BiteshipService::class, $biteshipMock);

        Livewire::test('storefront.checkout-page')
            ->set('name', 'Budi Santoso')
            ->set('email', '') // Email omitted (optional)
            ->set('phone', '081299998888')
            ->set('address', 'Jl. Sudirman No. 10')
            ->set('destinationAreaId', 'ID12345')
            ->set('areaName', 'Bogor Barat')
            ->set('paymentMethod', 'bank_transfer')
            ->call('fetchShippingRates')
            ->call('processCheckout')
            ->assertHasNoErrors();

        /** @var Order $order */
        $order = Order::query()->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->guest_email);
        $this->assertEquals('Budi Santoso', $order->recipient_name);
        $this->assertEquals('pending_payment', $order->status);
        $this->assertEquals('bank_transfer', $order->payment?->method);
    }

    public function test_can_checkout_with_cod_payment_method(): void
    {
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        $biteshipMock = Mockery::mock(BiteshipService::class);
        $biteshipMock->shouldReceive('calculateRates')->andReturn([
            [
                'courier_code' => 'sicepat',
                'courier_name' => 'SiCepat',
                'courier_service_code' => 'reg',
                'courier_service_name' => 'Reguler',
                'price' => 12000,
                'duration' => '2-3 Hari',
            ],
        ]);
        app()->instance(BiteshipService::class, $biteshipMock);

        Livewire::test('storefront.checkout-page')
            ->set('name', 'Siti Aminah')
            ->set('email', 'siti@example.com')
            ->set('phone', '081377776666')
            ->set('address', 'Jl. Pajajaran No. 45')
            ->set('destinationAreaId', 'ID67890')
            ->set('areaName', 'Bogor Timur')
            ->set('paymentMethod', 'cod')
            ->call('fetchShippingRates')
            ->call('processCheckout')
            ->assertHasNoErrors();

        /** @var Order $order */
        $order = Order::query()->latest('id')->first();
        $this->assertNotNull($order);
        $this->assertEquals('siti@example.com', $order->guest_email);
        $this->assertEquals('processing', $order->status); // COD starts as processing
        $this->assertEquals('cod', $order->payment?->method);
    }
}
