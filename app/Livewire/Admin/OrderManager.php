<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Shipment;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Kelola Pesanan — Admin Way Kopi')]
class OrderManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    // Order Details & Status Modal State
    public bool $showOrderModal = false;

    public ?Order $selectedOrder = null;

    public string $newStatus = '';

    public string $trackingNumber = '';

    public string $statusMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openOrderModal(int $orderId): void
    {
        $this->selectedOrder = Order::with(['items.productVariant.product', 'payment', 'shipment'])->find($orderId);
        if ($this->selectedOrder) {
            $this->newStatus = $this->selectedOrder->status;
            /** @var Shipment|null $shipment */
            $shipment = $this->selectedOrder->shipment;
            $this->trackingNumber = $shipment ? (string) $shipment->tracking_number : '';
            $this->showOrderModal = true;
        }
    }

    public function closeOrderModal(): void
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
        $this->newStatus = '';
        $this->trackingNumber = '';
    }

    public function updateOrderStatus(?NotificationService $notificationService = null): void
    {
        if (! $this->selectedOrder) {
            return;
        }

        $this->validate([
            'newStatus' => ['required', 'string'],
            'trackingNumber' => ['nullable', 'string', 'max:100'],
        ]);

        $previousStatus = $this->selectedOrder->status;

        $note = "Diperbarui oleh Admin" . ($this->trackingNumber ? " (Resi: {$this->trackingNumber})" : '');
        $this->selectedOrder->recordStatusChange($this->newStatus, auth()->id(), $note);

        /** @var Shipment|null $shipment */
        $shipment = $this->selectedOrder->shipment;

        if ($shipment) {
            $shipmentStatus = match ($this->newStatus) {
                'shipped' => 'in_transit',
                'delivered' => 'delivered',
                'cancelled' => 'failed',
                default => 'pending',
            };

            $shipment->update([
                'status' => $shipmentStatus,
                'tracking_number' => $this->trackingNumber ?: $shipment->tracking_number,
                'shipped_at' => $this->newStatus === 'shipped' ? now() : $shipment->shipped_at,
                'delivered_at' => $this->newStatus === 'delivered' ? now() : $shipment->delivered_at,
            ]);
        }

        if ($this->newStatus === 'shipped' && $previousStatus !== 'shipped') {
            try {
                $notificationService = $notificationService ?? app(NotificationService::class);
                $notificationService->sendOrderShipped($this->selectedOrder);
            } catch (\Throwable $e) {
                // Ignore notification error
            }
        }

        $this->statusMessage = "Status pesanan #{$this->selectedOrder->order_number} berhasil diperbarui ke '{$this->newStatus}'.";
        $this->closeOrderModal();
    }

    public function render(): View
    {
        $query = Order::with(['items', 'payment', 'shipment'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%'.$this->search.'%')
                    ->orWhere('recipient_name', 'like', '%'.$this->search.'%')
                    ->orWhere('guest_email', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $orders = $query->paginate(12);

        return view('livewire.admin.order-manager', [
            'orders' => $orders,
        ]);
    }
}
