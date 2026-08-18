<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BankTransferPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_pembayaran_menampilkan_detail_rekening_bank_dan_kode_unik(): void
    {
        $order = Order::create([
            'order_number' => 'WK-20260810-BANK01',
            'guest_email' => 'customer@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Test Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Test Transfer No 123',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 45000.00,
            'shipping_cost' => 10000.00,
            'unique_code' => 247,
            'total' => 55247.00,
            'status' => 'pending_payment',
        ]);

        $response = $this->get(route('checkout.payment', ['orderNumber' => $order->order_number]));
        $response->assertStatus(200);
        $response->assertSee('Bank Mandiri');
        $response->assertSee('1330026414847');
        $response->assertSee('Bank BRI');
        $response->assertSee('207401000502300');
        $response->assertSee('PT GUDANG KITA PERKASA');
        $response->assertSee('6282160388791');
        $response->assertSee('55.247');
    }

    public function test_payment_page_livewire_menghasilkan_whatsapp_url_konfirmasi(): void
    {
        $order = Order::create([
            'order_number' => 'WK-20260810-WA01',
            'guest_email' => 'wa@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Budi Susanto',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka 45',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 50000.00,
            'shipping_cost' => 10000.00,
            'unique_code' => 321,
            'total' => 60321.00,
            'status' => 'pending_payment',
        ]);

        Livewire::test(\App\Livewire\Storefront\PaymentPage::class, ['orderNumber' => $order->order_number])
            ->assertSee('Bank Mandiri')
            ->assertSee('1330026414847')
            ->assertSee('Bank BRI')
            ->assertSee('207401000502300')
            ->assertSee('PT GUDANG KITA PERKASA')
            ->assertViewHas('whatsappUrl', function ($url) use ($order) {
                return str_contains($url, '6282160388791') && str_contains($url, $order->order_number);
            });
    }

    public function test_order_lunas_diredirect_ke_halaman_sukses(): void
    {
        $order = Order::create([
            'order_number' => 'WK-20260810-PAID01',
            'guest_email' => 'paid@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Lunas Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Lunas No 1',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 90000.00,
            'shipping_cost' => 10000.00,
            'unique_code' => 112,
            'total' => 100112.00,
            'status' => 'paid',
        ]);

        $response = $this->get(route('checkout.payment', ['orderNumber' => $order->order_number]));
        $response->assertRedirect(route('checkout.success', ['orderNumber' => $order->order_number]));
    }
}
