<?php

namespace Tests\Feature;

use App\Livewire\Storefront\CheckoutPage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\ShippingDiscountService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VoucherCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var Product $product */
        $product = Product::create([
            'name' => 'Kopi Arabika Toraja',
            'slug' => 'kopi-arabika-toraja',
            'description' => 'Kopi Arabika Toraja asli berkualitas tinggi.',
            'origin' => 'Toraja',
            'is_active' => true,
        ]);

        /** @var ProductVariant $variant */
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'weight_grams' => 250,
            'grind_type' => 'medium',
            'price' => 75000,
            'stock' => 50,
            'sku' => 'TRJ-250-MED',
            'is_active' => true,
        ]);

        $this->variant = $variant;
    }

    public function test_shipping_discount_service_calculates_jabar_jabodetabek_banten_tiers(): void
    {
        $service = new ShippingDiscountService();

        // 1 Bungkus = Ongkir - 5.000
        $res1 = $service->calculateDiscount(1, 15000.0, 'Bogor Barat, Kota Bogor, Jawa Barat');
        $this->assertEquals(5000.0, $res1['discount_amount']);
        $this->assertEquals(10000.0, $res1['final_shipping_fee']);
        $this->assertFalse($res1['is_free_shipping']);

        // 2 Bungkus = Ongkir - 10.000
        $res2 = $service->calculateDiscount(2, 15000.0, 'Kebayoran Baru, Jakarta Selatan, DKI Jakarta');
        $this->assertEquals(10000.0, $res2['discount_amount']);
        $this->assertEquals(5000.0, $res2['final_shipping_fee']);
        $this->assertFalse($res2['is_free_shipping']);

        // >2 Bungkus (3 bks) = Gratis Ongkir
        $res3 = $service->calculateDiscount(3, 15000.0, 'Serang, Banten');
        $this->assertEquals(15000.0, $res3['discount_amount']);
        $this->assertEquals(0.0, $res3['final_shipping_fee']);
        $this->assertTrue($res3['is_free_shipping']);
    }

    public function test_shipping_discount_service_calculates_jateng_diy_jatim_tiers(): void
    {
        $service = new ShippingDiscountService();

        // 1 Bungkus = Ongkir normal
        $res1 = $service->calculateDiscount(1, 15000.0, 'Semarang Tengah, Kota Semarang, Jawa Tengah');
        $this->assertEquals(0.0, $res1['discount_amount']);
        $this->assertEquals(15000.0, $res1['final_shipping_fee']);
        $this->assertFalse($res1['is_free_shipping']);

        // 2 Bungkus = Ongkir - 5.000
        $res2 = $service->calculateDiscount(2, 15000.0, 'Sleman, DI Yogyakarta');
        $this->assertEquals(5000.0, $res2['discount_amount']);
        $this->assertEquals(10000.0, $res2['final_shipping_fee']);
        $this->assertFalse($res2['is_free_shipping']);

        // 3 Bungkus = Ongkir - 10.000
        $res3 = $service->calculateDiscount(3, 15000.0, 'Gubeng, Kota Surabaya, Jawa Timur');
        $this->assertEquals(10000.0, $res3['discount_amount']);
        $this->assertEquals(5000.0, $res3['final_shipping_fee']);
        $this->assertFalse($res3['is_free_shipping']);

        // >3 Bungkus (4 bks) = Gratis Ongkir
        $res4 = $service->calculateDiscount(4, 15000.0, 'Malang, Jawa Timur');
        $this->assertEquals(15000.0, $res4['discount_amount']);
        $this->assertEquals(0.0, $res4['final_shipping_fee']);
        $this->assertTrue($res4['is_free_shipping']);
    }

    public function test_shipping_discount_service_calculates_other_regions_as_normal_rate(): void
    {
        $service = new ShippingDiscountService();

        // Luar Jawa (misal: Medan, Lampung, Bali, Makassar) = Ongkir normal
        $res1 = $service->calculateDiscount(1, 25000.0, 'Medan Kota, Kota Medan, Sumatera Utara');
        $this->assertEquals(0.0, $res1['discount_amount']);
        $this->assertEquals(25000.0, $res1['final_shipping_fee']);

        $res5 = $service->calculateDiscount(5, 30000.0, 'Denpasar, Bali');
        $this->assertEquals(0.0, $res5['discount_amount']);
        $this->assertEquals(30000.0, $res5['final_shipping_fee']);
    }

    public function test_checkout_page_automatically_applies_tiered_discount_on_jabar(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 2);

        Livewire::test(CheckoutPage::class)
            ->set('destinationAreaId', 'IDNP9IDNC74IDND6752IDZ16320')
            ->set('areaName', 'Bogor Barat, Kota Bogor, Jawa Barat')
            ->set('shippingRates', [
                [
                    'courier_code' => 'sicepat',
                    'courier_name' => 'SiCepat',
                    'courier_service_code' => 'reg',
                    'courier_service_name' => 'REG',
                    'price' => 15000.0,
                    'duration' => '1-2 hari',
                ],
            ])
            ->set('selectedCourierIndex', 0)
            ->assertSee('Subsidi Ongkir Rp 10.000 (2 Bungkus)')
            ->assertSee('- Rp 10.000');
    }

    public function test_checkout_page_automatically_applies_free_shipping_for_over_two_packs_in_jabodetabek(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 3);

        Livewire::test(CheckoutPage::class)
            ->set('destinationAreaId', 'IDNP31IDNC157IDND1280')
            ->set('areaName', 'Kebayoran Baru, Kota Jakarta Selatan, DKI Jakarta')
            ->set('shippingRates', [
                [
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE',
                    'courier_service_code' => 'reg',
                    'courier_service_name' => 'REG',
                    'price' => 12000.0,
                    'duration' => '1-2 hari',
                ],
            ])
            ->set('selectedCourierIndex', 0)
            ->assertSee('GRATIS ONGKIR OTOMATIS AKTIF')
            ->assertSee('- Rp 12.000');
    }

    public function test_voucher_input_form_is_retained_and_handles_invalid_code(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        Livewire::test(CheckoutPage::class)
            ->set('voucherInput', 'INVALIDVOUCHER')
            ->call('applyVoucher')
            ->assertSet('appliedVoucherCode', '')
            ->assertSee('tidak ditemukan');
    }

    public function test_checkout_creates_order_with_automatic_shipping_discount_in_database(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 2);

        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);

        $order = $orderService->createOrder([
            'customer_name' => 'Siti Nurhaliza',
            'customer_email' => 'siti@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka No. 45 (Bogor Barat, Kota Bogor, Jawa Barat)',
            'destination_area_id' => 'IDNP9IDNC74IDND6752IDZ16320',
            'courier_code' => 'sicepat',
            'courier_service_code' => 'reg',
            'courier_service_name' => 'SiCepat REG',
            'shipping_fee' => 15000.0,
            'discount_amount' => 10000.0,
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'recipient_name' => 'Siti Nurhaliza',
            'discount_amount' => 10000.00,
            'subtotal' => 150000.00,
            'shipping_cost' => 15000.00,
        ]);

        // Expected total = 150000 (subtotal for 2 bks) + 15000 (shipping) - 10000 (discount) + unique_code
        $expectedWithoutCode = 150000 + 15000 - 10000;
        $this->assertEquals($expectedWithoutCode + $order->unique_code, (float) $order->total);
    }
}
