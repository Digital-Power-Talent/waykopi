<?php

namespace Tests\Feature;

use App\Livewire\Admin\OrderManager;
use App\Livewire\Admin\ProductManager;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_admin_menolak_pengguna_biasa_dan_guest(): void
    {
        // Guest redirect to login
        $this->get('/admin')->assertRedirect('/login');

        // Normal customer redirect to login
        $customer = User::factory()->create(['role' => 'customer']);
        $this->actingAs($customer)->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_dapat_mengakses_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Way Kopi');
    }

    public function test_admin_dapat_mengubah_status_pesanan_dan_nomor_resi(): void
    {
        $admin = User::factory()->admin()->create();

        $order = Order::create([
            'order_number' => 'WAY-20260802-ADM01',
            'guest_email' => 'adm@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Admin Test Customer',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Admin Test No 1',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Barat',
            'postal_code' => '16115',
            'subtotal' => 90000.00,
            'shipping_cost' => 10000.00,
            'total' => 100000.00,
            'status' => 'paid',
        ]);

        Shipment::create([
            'order_id' => $order->id,
            'courier_code' => 'sicepat',
            'courier_service' => 'SiCepat REG',
            'status' => 'pending',
        ]);

        Livewire::actingAs($admin)
            ->test(OrderManager::class)
            ->call('openOrderModal', $order->id)
            ->set('newStatus', 'shipped')
            ->set('trackingNumber', 'SOCAG00112233')
            ->call('updateOrderStatus');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'shipped',
        ]);

        $this->assertDatabaseHas('shipments', [
            'order_id' => $order->id,
            'tracking_number' => 'SOCAG00112233',
            'status' => 'in_transit',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'from_status' => 'paid',
            'to_status' => 'shipped',
            'changed_by' => $admin->id,
        ]);
    }

    public function test_admin_dapat_menambahkan_produk_dan_varian_kopi_baru(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProductManager::class)
            ->call('openProductModal')
            ->set('name', 'Way Kopi Honey Process')
            ->set('slug', 'way-kopi-honey-process')
            ->set('description', 'Kopi Robusta dengan proses pasca-panen Honey yang manis & harum.')
            ->set('roast_profile', 'Medium Roast')
            ->set('origin', 'Liwa, Lampung Barat')
            ->set('imageUrl', '/images/products/waykopi_robusta.png')
            ->set('weight_grams', 250)
            ->set('price', 60000)
            ->set('stock', 40)
            ->call('saveProduct');

        $this->assertDatabaseHas('products', [
            'slug' => 'way-kopi-honey-process',
            'name' => 'Way Kopi Honey Process',
        ]);

        $product = Product::query()->where('slug', 'way-kopi-honey-process')->firstOrFail();

        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'weight_grams' => 250,
            'price' => 60000.00,
            'stock' => 40,
        ]);
    }
}
