<?php

namespace App\Livewire\Storefront;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
class OrderSuccessPage extends Component
{
    public string $orderNumber = '';

    public ?Order $order = null;

    public function mount(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
        $this->order = Order::with(['items.productVariant.product', 'payment', 'shipment'])
            ->where('order_number', $orderNumber)
            ->first();

        if (! $this->order) {
            abort(404, 'Pesanan tidak ditemukan.');
        }
    }

    public function render(): View
    {
        return view('livewire.storefront.order-success-page');
    }
}
