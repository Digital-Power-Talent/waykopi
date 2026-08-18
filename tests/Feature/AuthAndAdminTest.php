<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_utama_dapat_diakses_publik(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_halaman_login_dapat_diakses_publik(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_customer_tidak_dapat_mengakses_admin_panel_dan_ter_redirect(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $response = $this->actingAs($customer)->get('/admin');

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Akses khusus admin.');
    }

    public function test_admin_dapat_mengakses_admin_panel(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_user_dapat_login_dengan_credential_valid(): void
    {
        $user = User::factory()->create([
            'email' => 'test@waykopi.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@waykopi.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }
}
