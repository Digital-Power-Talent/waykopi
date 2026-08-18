<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductService
{
    /**
     * Get paginated active products with filtering and sorting.
     *
     * @param  array{search?: string, grind_type?: string, weight_g?: int|string, sort?: string}  $filters
     */
    public function getActiveProducts(array $filters = [], int $perPage = 12): LengthAwarePaginator
    {
        $query = Product::query()
            ->with(['variants', 'images'])
            ->where('is_active', true);

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('origin', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['grind_type'])) {
            $grindType = $filters['grind_type'];
            $query->whereHas('variants', function (Builder $q) use ($grindType) {
                $q->where('is_active', true)
                    ->where('grind_type', $grindType);
            });
        }

        if (! empty($filters['weight_g'])) {
            $weightGrams = (int) $filters['weight_g'];
            $query->whereHas('variants', function (Builder $q) use ($weightGrams) {
                $q->where('is_active', true)
                    ->where('weight_grams', $weightGrams);
            });
        }

        $sort = $filters['sort'] ?? 'newest';
        match ($sort) {
            'price_asc' => $query->orderBy(
                \App\Models\ProductVariant::select('price')
                    ->whereColumn('product_id', 'products.id')
                    ->orderBy('price', 'asc')
                    ->limit(1),
                'asc'
            ),
            'price_desc' => $query->orderBy(
                \App\Models\ProductVariant::select('price')
                    ->whereColumn('product_id', 'products.id')
                    ->orderBy('price', 'desc')
                    ->limit(1),
                'desc'
            ),
            default => $query->latest(),
        };

        return $query->paginate($perPage);
    }

    /**
     * Find active product by slug.
     */
    public function findActiveProductBySlug(string $slug): ?Product
    {
        /** @var Product|null $product */
        $product = Product::query()
            ->with([
                'variants' => function ($q) {
                    $q->where('is_active', true);
                },
                'images',
            ])
            ->where('is_active', true)
            ->where('slug', $slug)
            ->first();

        return $product;
    }
}
