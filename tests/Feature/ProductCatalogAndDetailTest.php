<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCatalogAndDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_katalog_dapat_diakses_dan_menampilkan_produk_aktif(): void
    {
        $product = Product::factory()->create([
            'name' => 'Way Kopi Robusta Premium',
            'slug' => 'way-kopi-robusta-premium',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-ROB-WB-200',
            'weight_grams' => 200,
            'price' => 45000.00,
            'stock' => 50,
            'is_active' => true,
        ]);

        $response = $this->get('/products');

        $response->assertStatus(200);
        $response->assertSee('Way Kopi Robusta Premium');
        $response->assertSee('45.000');
    }

    public function test_filter_katalog_berdasarkan_ukuran_kopi(): void
    {
        $product1 = Product::factory()->create(['name' => 'Way Kopi 200g', 'is_active' => true]);
        ProductVariant::create([
            'product_id' => $product1->id,
            'sku' => 'WK-200',
            'weight_grams' => 200,
            'price' => 45000.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $product2 = Product::factory()->create(['name' => 'Way Kopi 500g', 'is_active' => true]);
        ProductVariant::create([
            'product_id' => $product2->id,
            'sku' => 'WK-500',
            'weight_grams' => 500,
            'price' => 95000.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\Storefront\ProductCatalog::class)
            ->set('weightG', '200')
            ->assertSee('Way Kopi 200g')
            ->assertDontSee('Way Kopi 500g');
    }

    public function test_halaman_detail_produk_menampilkan_varian_dan_harga_dinamis(): void
    {
        $product = Product::factory()->create([
            'name' => 'Way Kopi Robusta Tanggamus',
            'slug' => 'way-kopi-robusta-tanggamus',
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-TANG-WB-200',
            'weight_grams' => 200,
            'price' => 45000.00,
            'stock' => 25,
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-TANG-WB-500',
            'weight_grams' => 500,
            'price' => 100000.00,
            'stock' => 15,
            'is_active' => true,
        ]);

        $response = $this->get('/products/way-kopi-robusta-tanggamus');
        $response->assertStatus(200);
        $response->assertSee('Way Kopi Robusta Tanggamus');
        $response->assertSee('45.000');

        Livewire::test(\App\Livewire\Storefront\ProductDetail::class, ['slug' => 'way-kopi-robusta-tanggamus'])
            ->call('selectWeight', 500)
            ->assertSee('100.000');
    }

    public function test_halaman_detail_produk_dapat_memilih_kombinasi_ukuran_dan_jenis_gilingan(): void
    {
        $product = Product::factory()->create([
            'name' => 'Way Kopi Robusta Blend',
            'slug' => 'way-kopi-robusta-blend',
            'is_active' => true,
        ]);

        $variant1 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-BLEND-WB-200',
            'grind_type' => 'whole_bean',
            'weight_grams' => 200,
            'price' => 45000.00,
            'stock' => 20,
            'is_active' => true,
        ]);

        $variant2 = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'WK-BLEND-FINE-200',
            'grind_type' => 'fine',
            'weight_grams' => 200,
            'price' => 48000.00,
            'stock' => 15,
            'is_active' => true,
        ]);

        Livewire::test(\App\Livewire\Storefront\ProductDetail::class, ['slug' => 'way-kopi-robusta-blend'])
            ->assertSet('selectedVariantId', $variant1->id)
            ->call('selectGrindType', 'fine')
            ->assertSet('selectedVariantId', $variant2->id)
            ->assertSee('48.000');
    }

    public function test_halaman_detail_produk_mengembalikan_404_jika_produk_tidak_aktif_atau_tidak_ada(): void
    {
        $response = $this->get('/products/produk-gaib');
        $response->assertStatus(404);
    }
}
