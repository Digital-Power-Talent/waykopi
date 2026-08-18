<?php

namespace App\Livewire\Storefront;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
#[Title('Pembayaran Transfer Bank — Way Kopi')]
class PaymentPage extends Component
{
    public string $orderNumber = '';

    public ?Order $order = null;

    /** @var array<int, array{bank: string, account_number: string, account_name: string}> */
    public array $bankAccounts = [
        [
            'bank' => 'Bank Mandiri',
            'account_number' => '1330026414847',
            'account_name' => 'PT GUDANG KITA PERKASA',
        ],
        [
            'bank' => 'Bank BRI',
            'account_number' => '207401000502300',
            'account_name' => 'PT GUDANG KITA PERKASA',
        ],
    ];

    public string $whatsappNumber = '6282160388791';

    public function mount(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
        $this->order = Order::with(['items.productVariant.product', 'shipment'])
            ->where('order_number', $orderNumber)
            ->first();

        if (! $this->order) {
            abort(404, 'Pesanan tidak ditemukan.');
        }

        if ($this->order->status === 'paid') {
            $this->redirect(route('checkout.success', ['orderNumber' => $orderNumber]));

            return;
        }
    }

    #[Computed]
    public function whatsappUrl(): string
    {
        if (! $this->order) {
            return "https://wa.me/{$this->whatsappNumber}";
        }

        $formattedTotal = number_format((float) $this->order->total, 0, ',', '.');
        $text = "Halo Tim Way Kopi, saya mau konfirmasi pembayaran Transfer Bank untuk Pesanan #{$this->order->order_number} sebesar Rp {$formattedTotal}. Atas nama {$this->order->recipient_name}. Mohon segera diproses, terima kasih!";

        return "https://wa.me/{$this->whatsappNumber}?text=".rawurlencode($text);
    }

    public function render(): View
    {
        return view('livewire.storefront.payment-page', [
            'whatsappUrl' => $this->whatsappUrl(),
        ]);
    }
}
