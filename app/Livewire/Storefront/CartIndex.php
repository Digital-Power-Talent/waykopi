<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
#[Title('Keranjang Belanja — Way Kopi')]
class CartIndex extends Component
{
    public function updateQuantity(int $variantId, int $quantity, CartService $cartService): void
    {
        $cartService->updateQuantity($variantId, $quantity);
    }

    public function removeItem(int $variantId, CartService $cartService): void
    {
        $cartService->removeItem($variantId);
    }

    public function clearCart(CartService $cartService): void
    {
        $cartService->clear();
    }

    public function render(CartService $cartService): View
    {
        return view('livewire.storefront.cart-index', [
            'cartItems' => $cartService->getItems(),
            'subtotal' => $cartService->getSubtotal(),
            'totalWeightGrams' => $cartService->getTotalWeightGrams(),
            'itemCount' => $cartService->getItemCount(),
        ]);
    }
}
