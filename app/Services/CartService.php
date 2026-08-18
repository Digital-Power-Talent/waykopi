<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'waykopi_cart';

    /** @var Collection<int, array{variant_id: int, quantity: int, variant: ProductVariant, item_price: float, total_price: float, item_weight_g: int}>|null */
    protected ?Collection $cachedItems = null;

    /**
     * Get all cart items with loaded variant and product relationships.
     *
     * @return Collection<int, array{variant_id: int, quantity: int, variant: ProductVariant, item_price: float, total_price: float, item_weight_g: int}>
     */
    public function getItems(): Collection
    {
        if ($this->cachedItems !== null) {
            return $this->cachedItems;
        }

        $sessionCart = Session::get($this->sessionKey, []);
        $items = collect();

        if (empty($sessionCart)) {
            $this->cachedItems = $items;
            return $this->cachedItems;
        }

        $variantIds = array_keys($sessionCart);
        $variants = ProductVariant::with(['product.primaryImage', 'product.images'])
            ->whereIn('id', $variantIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        foreach ($sessionCart as $variantId => $quantity) {
            /** @var ProductVariant|null $variant */
            $variant = $variants->get($variantId);
            if (! $variant || ! $variant->is_active) {
                continue;
            }

            $itemQty = min($quantity, $variant->stock);
            if ($itemQty <= 0) {
                continue;
            }

            $price = (float) $variant->price;
            $weightG = (int) $variant->weight_grams;

            $items->push([
                'variant_id' => $variant->id,
                'quantity' => $itemQty,
                'variant' => $variant,
                'item_price' => $price,
                'total_price' => $price * $itemQty,
                'item_weight_g' => $weightG * $itemQty,
            ]);
        }

        $this->cachedItems = $items;

        return $this->cachedItems;
    }

    /**
     * Add or update variant in cart.
     */
    public function addItem(int $variantId, int $quantity = 1): void
    {
        $sessionCart = Session::get($this->sessionKey, []);
        $currentQty = $sessionCart[$variantId] ?? 0;
        $sessionCart[$variantId] = $currentQty + $quantity;

        Session::put($this->sessionKey, $sessionCart);
        $this->cachedItems = null;
    }

    /**
     * Update item quantity directly.
     */
    public function updateQuantity(int $variantId, int $quantity): void
    {
        $sessionCart = Session::get($this->sessionKey, []);

        if ($quantity <= 0) {
            unset($sessionCart[$variantId]);
        } else {
            $sessionCart[$variantId] = $quantity;
        }

        Session::put($this->sessionKey, $sessionCart);
        $this->cachedItems = null;
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $variantId): void
    {
        $sessionCart = Session::get($this->sessionKey, []);
        unset($sessionCart[$variantId]);
        Session::put($this->sessionKey, $sessionCart);
        $this->cachedItems = null;
    }

    /**
     * Clear all items from cart.
     */
    public function clear(): void
    {
        Session::forget($this->sessionKey);
        $this->cachedItems = null;
    }

    /**
     * Calculate cart subtotal.
     */
    public function getSubtotal(): float
    {
        return (float) $this->getItems()->sum('total_price');
    }

    /**
     * Calculate total weight in grams.
     */
    public function getTotalWeightGrams(): int
    {
        return (int) $this->getItems()->sum('item_weight_g');
    }

    /**
     * Get total quantity of items in cart.
     */
    public function getItemCount(): int
    {
        return (int) $this->getItems()->sum('quantity');
    }
}
