<?php

namespace App\Livewire\Storefront;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
class ProductDetail extends Component
{
    public string $slug = '';

    public ?Product $product = null;

    public ?int $selectedVariantId = null;

    public string $selectedGrindType = 'medium';

    public int $selectedWeightG = 200;

    public int $quantity = 1;

    public string $selectedImageUrl = '';

    public string $cartMessage = '';

    public function mount(string $slug, ProductService $productService): void
    {
        $this->slug = $slug;
        $this->product = $productService->findActiveProductBySlug($slug);

        if (! $this->product) {
            abort(404, 'Produk kopi tidak ditemukan atau sedang tidak aktif.');
        }

        // Set initial image
        $primary = $this->product->primaryImage;
        if ($primary) {
            $this->selectedImageUrl = $primary->url;
        } elseif ($this->product->images->isNotEmpty()) {
            /** @var ProductImage $firstImg */
            $firstImg = $this->product->images->first();
            $this->selectedImageUrl = $firstImg->url;
        }

        // Select initial variant
        /** @var ProductVariant|null $firstVariant */
        $firstVariant = $this->product->variants->first();
        if ($firstVariant) {
            $this->selectedVariantId = $firstVariant->id;
            $this->selectedGrindType = $firstVariant->grind_type ?? 'whole_bean';
            $this->selectedWeightG = $firstVariant->weight_grams;
        }
    }

    public function selectGrindType(string $grindType): void
    {
        $this->selectedGrindType = $grindType;
        $this->updateSelectedVariant();
    }

    public function selectWeight(int $weightG): void
    {
        $this->selectedWeightG = $weightG;
        $this->updateSelectedVariant();
    }

    protected function updateSelectedVariant(): void
    {
        if (! $this->product) {
            return;
        }

        // Try exact match for both weight and grind_type
        /** @var ProductVariant|null $variant */
        $variant = $this->product->variants->first(function (ProductVariant $v) {
            return $v->weight_grams === $this->selectedWeightG && $v->grind_type === $this->selectedGrindType;
        });

        // If not found, try matching weight first
        if (! $variant) {
            $variant = $this->product->variants->firstWhere('weight_grams', $this->selectedWeightG);
            if ($variant) {
                $this->selectedGrindType = $variant->grind_type ?? 'whole_bean';
            }
        }

        // Fallback to first available variant
        if (! $variant) {
            /** @var ProductVariant|null $fallback */
            $fallback = $this->product->variants->first();
            if ($fallback) {
                $variant = $fallback;
                $this->selectedGrindType = $fallback->grind_type ?? 'whole_bean';
                $this->selectedWeightG = $fallback->weight_grams;
            }
        }

        if ($variant) {
            $this->selectedVariantId = $variant->id;
        }
    }

    public function selectImage(string $url): void
    {
        $this->selectedImageUrl = $url;
    }

    public function incrementQuantity(): void
    {
        $variant = $this->getSelectedVariant();
        $maxStock = $variant ? $variant->stock : 1;

        if ($this->quantity < $maxStock) {
            $this->quantity++;
        }
    }

    public function decrementQuantity(): void
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function getSelectedVariant(): ?ProductVariant
    {
        if (! $this->product || ! $this->selectedVariantId) {
            return null;
        }

        /** @var ProductVariant|null $variant */
        $variant = $this->product->variants->firstWhere('id', $this->selectedVariantId);

        return $variant;
    }

    public function addToCart(CartService $cartService): void
    {
        $variant = $this->getSelectedVariant();
        if (! $variant || $variant->stock < $this->quantity) {
            $this->cartMessage = 'Stok varian tidak mencukupi.';

            return;
        }

        $cartService->addItem($variant->id, $this->quantity);
        $this->cartMessage = '';

        session()->flash('success', "{$this->quantity}x {$this->product?->name} ({$variant->grind_type_label}, {$variant->weight_grams}g) berhasil ditambahkan ke keranjang!");
        $this->dispatch('cartUpdated');
    }

    public function buyNow(CartService $cartService): void
    {
        $this->addToCart($cartService);
        $this->redirect(route('cart.index'));
    }

    public function render(): View
    {
        return view('livewire.storefront.product-detail', [
            'selectedVariant' => $this->getSelectedVariant(),
        ]);
    }
}
