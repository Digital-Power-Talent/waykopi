<?php

namespace App\Livewire\Storefront;

use App\Services\BiteshipService;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.storefront')]
#[Title('Checkout Pesanan — Way Kopi')]
class CheckoutPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $address = '';

    public string $areaSearch = '';

    public string $destinationAreaId = '';

    public string $areaName = '';

    public string $notes = '';

    /** @var array<int, array{id: string, name: string}> */
    public array $areaResults = [];

    /** @var array<int, array{courier_code: string, courier_name: string, courier_service_code: string, courier_service_name: string, price: float, duration: string}> */
    public array $shippingRates = [];

    public string $paymentMethod = 'bank_transfer';

    public int $selectedCourierIndex = 0;

    public string $voucherInput = '';

    public string $appliedVoucherCode = '';

    public string $voucherMessage = '';

    public string $voucherError = '';

    public string $errorMessage = '';

    public function mount(): void
    {
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $this->name = $user->name;
            $this->email = $user->email ?? '';
            $this->phone = $user->phone ?? '';
        }
    }

    public function updatedAreaSearch(): void
    {
        if ($this->areaSearch !== $this->areaName) {
            $this->searchArea();
        }
    }

    public function searchArea(?BiteshipService $biteshipService = null): void
    {
        $biteshipService = $biteshipService ?? app(BiteshipService::class);

        $query = trim($this->areaSearch);

        if (strlen($query) >= 3) {
            $results = $biteshipService->searchAreas($query);
            $this->areaResults = array_map(function ($area) {
                return [
                    'id' => (string) $area['id'],
                    'name' => (string) $area['name'],
                ];
            }, $results);
        } else {
            $this->areaResults = [];
        }
    }

    public function selectArea(int $index, ?BiteshipService $biteshipService = null, ?CartService $cartService = null): void
    {
        if (! isset($this->areaResults[$index])) {
            return;
        }

        $selected = $this->areaResults[$index];

        $biteshipService = $biteshipService ?? app(BiteshipService::class);
        $cartService = $cartService ?? app(CartService::class);

        $this->destinationAreaId = $selected['id'];
        $this->areaName = $selected['name'];
        $this->areaSearch = $selected['name'];
        $this->areaResults = [];

        $this->fetchShippingRates($biteshipService, $cartService);
    }

    public function clearAreaResults(): void
    {
        $this->areaResults = [];
    }

    public function fetchShippingRates(?BiteshipService $biteshipService = null, ?CartService $cartService = null): void
    {
        $biteshipService = $biteshipService ?? app(BiteshipService::class);
        $cartService = $cartService ?? app(CartService::class);

        if (empty($this->destinationAreaId)) {
            return;
        }

        $cartItems = $cartService->getItems();
        $items = [];

        foreach ($cartItems as $item) {
            $items[] = [
                'name' => $item['variant']->product->name,
                'value' => (float) $item['item_price'],
                'quantity' => $item['quantity'],
                'weight' => (int) $item['variant']->weight_grams,
            ];
        }

        $this->shippingRates = $biteshipService->calculateRates($this->destinationAreaId, $items);
        $this->selectedCourierIndex = 0;
    }

    public function selectCourier(int $index): void
    {
        $this->selectedCourierIndex = $index;
    }

    public function applyVoucher(): void
    {
        $this->voucherError = '';
        $this->voucherMessage = '';

        $clean = strtoupper(trim($this->voucherInput));

        if (empty($clean)) {
            $this->voucherError = 'Silakan masukkan kode voucher.';

            return;
        }

        if ($clean === 'WAYKOPI100') {
            $this->appliedVoucherCode = 'WAYKOPI100';
            $this->voucherInput = 'WAYKOPI100';
            $this->voucherMessage = 'Voucher WAYKOPI100 berhasil digunakan! Diskon ongkir s.d. Rp 10.000.';
        } else {
            $this->appliedVoucherCode = '';
            $this->voucherError = "Kode voucher '{$this->voucherInput}' tidak valid.";
        }
    }

    public function removeVoucher(): void
    {
        $this->appliedVoucherCode = '';
        $this->voucherInput = '';
        $this->voucherMessage = '';
        $this->voucherError = '';
    }

    public function getSelectedShippingFee(): float
    {
        return isset($this->shippingRates[$this->selectedCourierIndex])
            ? (float) $this->shippingRates[$this->selectedCourierIndex]['price']
            : 0.0;
    }

    public function processCheckout(?OrderService $orderService = null, ?CartService $cartService = null): void
    {
        $orderService = $orderService ?? app(OrderService::class);
        $cartService = $cartService ?? app(CartService::class);

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'destinationAreaId' => ['required', 'string'],
            'paymentMethod' => ['required', 'string', 'in:bank_transfer,cod'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor HP / WhatsApp wajib diisi.',
            'address.required' => 'Alamat lengkap pengiriman wajib diisi.',
            'destinationAreaId.required' => 'Silakan pilih area/kecamatan pengiriman.',
            'paymentMethod.required' => 'Silakan pilih metode pembayaran.',
        ]);

        if (empty($this->shippingRates) || ! isset($this->shippingRates[$this->selectedCourierIndex])) {
            $this->errorMessage = 'Silakan pilih metode pengiriman yang tersedia.';

            return;
        }

        $selectedRate = $this->shippingRates[$this->selectedCourierIndex];
        $shippingFee = (float) $selectedRate['price'];
        $discountAmount = ($this->appliedVoucherCode === 'WAYKOPI100') ? min($shippingFee, 10000.0) : 0.0;

        try {
            $order = $orderService->createOrder([
                'customer_name' => $this->name,
                'customer_email' => $this->email ?: null,
                'customer_phone' => $this->phone,
                'shipping_address' => $this->address.' ('.$this->areaName.')',
                'destination_area_id' => $this->destinationAreaId,
                'courier_code' => $selectedRate['courier_code'],
                'courier_service_code' => $selectedRate['courier_service_code'] ?? null,
                'courier_service_name' => $selectedRate['courier_name'].' '.$selectedRate['courier_service_name'],
                'shipping_fee' => $shippingFee,
                'voucher_code' => $this->appliedVoucherCode ?: null,
                'discount_amount' => $discountAmount,
                'payment_method' => $this->paymentMethod,
                'notes' => $this->notes,
                'user_id' => Auth::id(),
            ]);

            if ($this->paymentMethod === 'cod') {
                $this->redirect(route('checkout.success', ['orderNumber' => $order->order_number]));
            } else {
                $this->redirect(route('checkout.payment', ['orderNumber' => $order->order_number]));
            }
        } catch (\Throwable $e) {
            $this->errorMessage = 'Gagal memproses checkout: '.$e->getMessage();
        }
    }

    public function render(CartService $cartService): View
    {
        $cartItems = $cartService->getItems();
        $subtotal = $cartService->getSubtotal();
        $totalWeight = $cartService->getTotalWeightGrams();

        $selectedShippingFee = $this->getSelectedShippingFee();
        $discountAmount = ($this->appliedVoucherCode === 'WAYKOPI100') ? min($selectedShippingFee, 10000.0) : 0.0;
        $grandTotal = max(0.0, $subtotal + $selectedShippingFee - $discountAmount);

        return view('livewire.storefront.checkout-page', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'totalWeight' => $totalWeight,
            'selectedShippingFee' => $selectedShippingFee,
            'discountAmount' => $discountAmount,
            'grandTotal' => $grandTotal,
        ]);
    }
}
