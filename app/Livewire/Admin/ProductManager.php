<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Kelola Produk — Admin Way Kopi')]
class ProductManager extends Component
{
    use WithPagination;

    public string $search = '';

    // Product Modal State
    public bool $showProductModal = false;

    public ?int $editingProductId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public string $roast_profile = 'Medium Dark';

    public string $origin = 'Tanggamus, Lampung, Indonesia';

    public string $imageUrl = '/images/products/waykopi_robusta.png';

    // Variant fields
    public string $grind_type = 'whole_bean';

    public int $weight_grams = 200;

    public float $price = 45000;

    public int $stock = 50;

    public string $statusMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openProductModal(?int $productId = null): void
    {
        $this->resetForm();
        $this->showProductModal = true;

        if ($productId) {
            /** @var Product|null $product */
            $product = Product::with(['variants', 'primaryImage'])->find($productId);
            if ($product) {
                $this->editingProductId = $product->id;
                $this->name = $product->name;
                $this->slug = $product->slug;
                $this->description = $product->description;
                $this->roast_profile = $product->roast_profile ?? 'Medium Dark';
                $this->origin = $product->origin ?? 'Tanggamus, Lampung, Indonesia';
                $this->imageUrl = $product->primaryImage ? $product->primaryImage->url : '/images/products/waykopi_robusta.png';

                $firstVariant = $product->variants->first();
                if ($firstVariant) {
                    $this->grind_type = $firstVariant->grind_type;
                    $this->weight_grams = $firstVariant->weight_grams;
                    $this->price = (float) $firstVariant->price;
                    $this->stock = $firstVariant->stock;
                }
            }
        }
    }

    public function closeProductModal(): void
    {
        $this->showProductModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingProductId = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->roast_profile = 'Medium Dark';
        $this->origin = 'Tanggamus, Lampung, Indonesia';
        $this->imageUrl = '/images/products/waykopi_robusta.png';
        $this->grind_type = 'whole_bean';
        $this->weight_grams = 200;
        $this->price = 45000;
        $this->stock = 50;
    }

    public function updatedName(string $value): void
    {
        if (empty($this->editingProductId)) {
            $this->slug = Str::slug($value);
        }
    }

    public function saveProduct(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug,'.$this->editingProductId],
            'description' => ['required', 'string'],
            'roast_profile' => ['required', 'string', 'max:100'],
            'origin' => ['required', 'string', 'max:100'],
            'imageUrl' => ['required', 'string', 'max:500'],
            'weight_grams' => ['required', 'integer', 'min:50'],
            'price' => ['required', 'numeric', 'min:1000'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product = Product::updateOrCreate(
            ['id' => $this->editingProductId],
            [
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'roast_profile' => $this->roast_profile,
                'origin' => $this->origin,
                'is_active' => true,
            ]
        );

        // Update primary image
        ProductImage::updateOrCreate(
            ['product_id' => $product->id, 'sort_order' => 1],
            ['url' => $this->imageUrl, 'alt_text' => $this->name]
        );

        // Update or create main variant
        /** @var ProductVariant|null $primaryVariant */
        $primaryVariant = $product->variants()->first();
        ProductVariant::updateOrCreate(
            ['id' => $primaryVariant?->id],
            [
                'product_id' => $product->id,
                'sku' => 'WK-'.strtoupper(Str::slug($this->name)).'-'.$this->weight_grams,
                'grind_type' => $this->grind_type,
                'weight_grams' => $this->weight_grams,
                'price' => $this->price,
                'stock' => $this->stock,
                'is_active' => true,
            ]
        );

        $this->statusMessage = "Produk '{$product->name}' berhasil disimpan.";
        $this->closeProductModal();
    }

    public function deleteProduct(int $productId): void
    {
        $product = Product::find($productId, ['*']);
        if ($product) {
            $name = $product->name;
            $product->delete();
            $this->statusMessage = "Produk '{$name}' berhasil dihapus.";
        }
    }

    public function render(): View
    {
        $products = Product::with(['variants', 'primaryImage'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(9);

        return view('livewire.admin.product-manager', [
            'products' => $products,
        ]);
    }
}
