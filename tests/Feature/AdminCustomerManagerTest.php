<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminCustomerManagerTest extends TestCase
{
    use RefreshDatabase;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var User $admin */
        $admin = User::factory()->create([
            'name' => 'Admin Way Kopi',
            'email' => 'admin_test_'.time().'@waykopi.com',
            'role' => 'admin',
        ]);

        $this->admin = $admin;
    }

    public function test_admin_can_access_customer_manager(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.customers.index'))
            ->assertStatus(200)
            ->assertSee('Kelola Akun Pelanggan');
    }

    public function test_admin_can_create_customer(): void
    {
        Livewire::actingAs($this->admin)
            ->test('admin.customer-manager')
            ->call('openCustomerModal')
            ->set('name', 'Pelanggan Baru')
            ->set('email', 'pelanggan_baru@example.com')
            ->set('phone', '08123456789')
            ->set('role', 'customer')
            ->set('password', 'password123')
            ->call('saveCustomer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Pelanggan Baru',
            'email' => 'pelanggan_baru@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_admin_can_edit_customer(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create([
            'name' => 'Pelanggan Lama',
            'email' => 'lama@example.com',
            'role' => 'customer',
        ]);

        Livewire::actingAs($this->admin)
            ->test('admin.customer-manager')
            ->call('openCustomerModal', $customer->id)
            ->set('name', 'Pelanggan Lama Edit')
            ->call('saveCustomer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'Pelanggan Lama Edit',
        ]);
    }

    public function test_admin_can_delete_customer(): void
    {
        /** @var User $customer */
        $customer = User::factory()->create([
            'name' => 'Pelanggan Hapus',
            'email' => 'hapus@example.com',
            'role' => 'customer',
        ]);

        Livewire::actingAs($this->admin)
            ->test('admin.customer-manager')
            ->call('deleteCustomer', $customer->id);

        $this->assertDatabaseMissing('users', [
            'id' => $customer->id,
        ]);
    }
}
