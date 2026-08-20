<?php

namespace Tests\Feature;

use App\Livewire\Storefront\CheckoutPage;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\OrderService;
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

        $product = Product::create([
            'name' => 'Kopi Arabika Toraja',
            'slug' => 'kopi-arabika-toraja',
            'description' => 'Kopi Arabika Toraja asli berkualitas tinggi.',
            'origin' => 'Toraja',
            'is_active' => true,
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'weight_grams' => 250,
            'grind_type' => 'medium',
            'price' => 75000,
            'stock' => 15,
            'sku' => 'TRJ-250-MED',
            'is_active' => true,
        ]);
    }

    public function test_voucher_waykopi100_applies_discount_up_to_10000(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        Livewire::test(CheckoutPage::class)
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
            ->set('voucherInput', 'waykopi100')
            ->call('applyVoucher')
            ->assertSet('appliedVoucherCode', 'WAYKOPI100')
            ->assertSee('WAYKOPI100')
            ->assertSee('Diskon Ongkir (WAYKOPI100)')
            ->assertSee('Rp 10.000');
    }

    public function test_voucher_caps_discount_to_exact_shipping_fee_if_below_10000(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        Livewire::test(CheckoutPage::class)
            ->set('shippingRates', [
                [
                    'courier_code' => 'jne',
                    'courier_name' => 'JNE',
                    'courier_service_code' => 'reg',
                    'courier_service_name' => 'REG',
                    'price' => 8000.0,
                    'duration' => '1-2 hari',
                ],
            ])
            ->set('selectedCourierIndex', 0)
            ->set('voucherInput', 'WAYKOPI100')
            ->call('applyVoucher')
            ->assertSet('appliedVoucherCode', 'WAYKOPI100')
            ->assertSee('- Rp 8.000');
    }

    public function test_invalid_voucher_shows_error_message(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        Livewire::test(CheckoutPage::class)
            ->set('voucherInput', 'INVALIDVOUCHER')
            ->call('applyVoucher')
            ->assertSet('appliedVoucherCode', '')
            ->assertSee('tidak valid');
    }

    public function test_user_can_remove_applied_voucher(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        Livewire::test(CheckoutPage::class)
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
            ->set('voucherInput', 'WAYKOPI100')
            ->call('applyVoucher')
            ->assertSet('appliedVoucherCode', 'WAYKOPI100')
            ->call('removeVoucher')
            ->assertSet('appliedVoucherCode', '')
            ->assertDontSee('Diskon Ongkir (WAYKOPI100)');
    }

    public function test_checkout_with_voucher_stores_voucher_and_discount_in_database(): void
    {
        /** @var CartService $cartService */
        $cartService = app(CartService::class);
        $cartService->addItem($this->variant->id, 1);

        /** @var OrderService $orderService */
        $orderService = app(OrderService::class);

        $order = $orderService->createOrder([
            'customer_name' => 'Siti Nurhaliza',
            'customer_email' => 'siti@example.com',
            'customer_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka No. 45',
            'destination_area_id' => 'IDNP9IDNC74IDND6752IDZ16320',
            'courier_code' => 'sicepat',
            'courier_service_code' => 'reg',
            'courier_service_name' => 'SiCepat REG',
            'shipping_fee' => 15000.0,
            'voucher_code' => 'WAYKOPI100',
            'discount_amount' => 10000.0,
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'recipient_name' => 'Siti Nurhaliza',
            'voucher_code' => 'WAYKOPI100',
            'discount_amount' => 10000.00,
            'subtotal' => 75000.00,
            'shipping_cost' => 15000.00,
        ]);

        // Expected total = 75000 (subtotal) + 15000 (shipping) - 10000 (discount) + unique_code
        $expectedWithoutCode = 75000 + 15000 - 10000;
        $this->assertEquals($expectedWithoutCode + $order->unique_code, (float) $order->total);
    }
}
