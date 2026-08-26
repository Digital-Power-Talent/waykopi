<?php

namespace Tests\Feature;

use App\Livewire\Admin\ProductManager;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminProductManagerCRUDTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_product_manager_page(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $product = Product::create([
            'name' => 'Kopi Robusta Super',
            'slug' => 'kopi-robusta-super',
            'description' => 'Kopi Robusta pilihan Tanggamus',
            'roast_profile' => 'Medium Dark',
            'origin' => 'Tanggamus, Lampung',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Produk & Stok Kopi', false);
        $response->assertSee('Kopi Robusta Super');
    }

    public function test_admin_can_create_new_product(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ProductManager::class)
            ->set('name', 'Kopi Robusta Honey Process')
            ->set('slug', 'kopi-robusta-honey-process')
            ->set('description', 'Kopi Robusta proses honey fermentasi alami')
            ->set('roast_profile', 'Medium')
            ->set('origin', 'Liwa, Lampung')
            ->set('imageUrl', '/images/products/waykopi_robusta.png')
            ->set('weight_grams', 250)
            ->set('price', 60000)
            ->set('stock', 40)
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Robusta Honey Process',
            'slug' => 'kopi-robusta-honey-process',
        ]);
    }

    public function test_admin_can_edit_product(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $product = Product::create([
            'name' => 'Kopi Lama',
            'slug' => 'kopi-lama',
            'description' => 'Deskripsi lama',
            'roast_profile' => 'Dark',
            'origin' => 'Tanggamus',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ProductManager::class)
            ->call('openProductModal', $product->id)
            ->set('name', 'Kopi Baru Diperbarui')
            ->call('saveProduct')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Kopi Baru Diperbarui',
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();

        $product = Product::create([
            'name' => 'Kopi Yang Akan Dihapus',
            'slug' => 'kopi-yang-akan-dihapus',
            'description' => 'Deskripsi hapus',
            'roast_profile' => 'Medium',
            'origin' => 'Tanggamus',
            'is_active' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(ProductManager::class)
            ->call('deleteProduct', $product->id)
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
