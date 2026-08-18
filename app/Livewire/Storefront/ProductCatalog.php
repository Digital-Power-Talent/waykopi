<?php

namespace App\Livewire\Storefront;

use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.storefront')]
#[Title('Katalog Kopi Robusta Lampung — Way Kopi')]
class ProductCatalog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $grindType = '';

    public string $weightG = '';

    public string $sort = 'newest';

    public bool $showHero = true;

    /**
     * Reset pagination when search or filters update.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingGrindType(): void
    {
        $this->resetPage();
    }

    public function updatingWeightG(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'grindType', 'weightG', 'sort']);
        $this->resetPage();
    }

    public function addToCart(int $variantId, CartService $cartService): void
    {
        /** @var ProductVariant|null $variant */
        $variant = ProductVariant::with('product')->find($variantId);
        if (! $variant || $variant->stock < 1) {
            session()->flash('error', 'Stok varian produk tidak mencukupi.');

            return;
        }

        $cartService->addItem($variant->id, 1);
        session()->flash('success', "1x {$variant->product->name} ({$variant->weight_grams}g) berhasil ditambahkan ke keranjang!");
        $this->dispatch('cartUpdated');
    }

    public function render(ProductService $productService): View
    {
        $products = $productService->getActiveProducts([
            'search' => $this->search,
            'grind_type' => $this->grindType,
            'weight_g' => $this->weightG,
            'sort' => $this->sort,
        ]);

        return view('livewire.storefront.product-catalog', [
            'products' => $products,
        ]);
    }
}
