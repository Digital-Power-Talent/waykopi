<?php

namespace Tests\Feature;

use App\Livewire\Account\Dashboard;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerAuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_pelanggan_dapat_mendaftar_dan_login(): void
    {
        $response = $this->post('/register', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'phone' => '081999888777',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
        ]);
    }

    public function test_pelanggan_dapat_logout(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_halaman_akun_membutuhkan_autentikasi(): void
    {
        $response = $this->get('/account');
        $response->assertRedirect('/login');
    }

    public function test_halaman_akun_menampilkan_riwayat_pesanan_pelanggan(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'Rahmat Hidayat', 'email' => 'rahmat@example.com']);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'WAY-20260802-CUST01',
            'guest_email' => 'rahmat@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Rahmat Hidayat',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Merdeka No 45',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Tengah',
            'postal_code' => '16121',
            'subtotal' => 90000.00,
            'shipping_cost' => 10000.00,
            'total' => 100000.00,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
        $response->assertSee('WAY-20260802-CUST01');
        $response->assertSee('Rahmat Hidayat');
    }

    public function test_pelanggan_dapat_melihat_pesanan_guest_yang_menggunakan_email_sama(): void
    {
        /** @var User $user */
        $user = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@example.com']);

        $guestOrder = Order::create([
            'user_id' => null, // Guest checkout without login
            'order_number' => 'WAY-20260802-GUEST99',
            'guest_email' => 'budi@example.com',
            'guest_phone' => '081234567890',
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'shipping_address' => 'Jl. Pajajaran No 12',
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'district' => 'Bogor Timur',
            'postal_code' => '16143',
            'subtotal' => 45000.00,
            'shipping_cost' => 10000.00,
            'total' => 55000.00,
            'status' => 'paid',
        ]);

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
        $response->assertSee('WAY-20260802-GUEST99');
    }

    public function test_pelanggan_dapat_menambahkan_alamat_pengiriman_baru(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Dashboard::class)
            ->call('openAddressModal')
            ->set('label', 'Kantor')
            ->set('recipient_name', 'Rahmat (Kantor)')
            ->set('phone', '081234567890')
            ->set('full_address', 'Gedung Kopi Lt 3, Jl. Sudirman No 8')
            ->set('district', 'Kebayoran Baru')
            ->set('city', 'Kota Jakarta Selatan')
            ->set('province', 'DKI Jakarta')
            ->set('postal_code', '12110')
            ->set('is_default', true)
            ->call('saveAddress');

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'label' => 'Kantor',
            'recipient_name' => 'Rahmat (Kantor)',
            'city' => 'Kota Jakarta Selatan',
            'is_default' => true,
        ]);
    }
}
