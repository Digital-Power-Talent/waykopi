<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Shipment;
use App\Services\NotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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
        $this->selectedOrder = Order::query()->with(['items.productVariant.product', 'payment', 'shipment'])->find($orderId, ['*']);
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
        $this->selectedOrder->recordStatusChange($this->newStatus, Auth::id(), $note);

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

    public function sendToBiteship(int $orderId, ?\App\Services\BiteshipService $biteshipService = null): void
    {
        $order = Order::query()->with(['items.productVariant.product', 'payment', 'shipment'])->find($orderId, ['*']);
        if (! $order) {
            return;
        }

        $biteshipService = $biteshipService ?? app(\App\Services\BiteshipService::class);
        $result = $biteshipService->createOrder($order);

        if ($result['success']) {
            $order->refresh();
            $resiMsg = $order->shipment?->tracking_number ? " Resi: {$order->shipment->tracking_number}." : '';
            $this->statusMessage = "Pesanan #{$order->order_number} berhasil dikirim ke Biteship (ID: {$order->shipment?->biteship_order_id}).{$resiMsg}";

            if ($this->selectedOrder && $this->selectedOrder->id === $order->id) {
                $this->selectedOrder = $order;
                $this->trackingNumber = (string) ($order->shipment?->tracking_number ?? '');
            }
        } else {
            $this->statusMessage = "Gagal mengirim ke Biteship: {$result['message']}";
        }
    }

    public function syncBiteshipStatus(int $orderId, ?\App\Services\BiteshipService $biteshipService = null): void
    {
        $order = Order::query()->with(['items', 'payment', 'shipment'])->find($orderId, ['*']);
        if (! $order || ! $order->shipment?->biteship_order_id) {
            $this->statusMessage = 'Pesanan ini belum memiliki ID Biteship.';

            return;
        }

        $biteshipService = $biteshipService ?? app(\App\Services\BiteshipService::class);
        $biteshipOrder = $biteshipService->getOrder($order->shipment->biteship_order_id);

        if ($biteshipOrder) {
            $biteshipStatus = (string) ($biteshipOrder['status'] ?? '');
            $courierData = $biteshipOrder['courier'] ?? [];
            $waybill = (string) ($courierData['waybill_id'] ?? $courierData['tracking_id'] ?? '');
            $labelUrl = (string) ($courierData['link'] ?? '');

            $shipmentStatus = match (strtolower($biteshipStatus)) {
                'delivered' => 'delivered',
                'cancelled', 'rejected' => 'failed',
                'picking_up', 'picked', 'dropping_off', 'in_transit' => 'in_transit',
                default => 'booked',
            };

            $order->shipment->update([
                'status' => $shipmentStatus,
                'tracking_number' => $waybill ?: $order->shipment->tracking_number,
                'label_url' => $labelUrl ?: $order->shipment->label_url,
                'delivered_at' => $shipmentStatus === 'delivered' ? now() : $order->shipment->delivered_at,
                'shipped_at' => in_array($shipmentStatus, ['in_transit', 'delivered']) ? ($order->shipment->shipped_at ?: now()) : null,
            ]);

            if ($shipmentStatus === 'delivered' && $order->status !== 'delivered') {
                $order->recordStatusChange('delivered', Auth::id(), 'Sinkronisasi Biteship: Status DELIVERED');
            } elseif (in_array($shipmentStatus, ['in_transit']) && in_array($order->status, ['paid', 'processing'])) {
                $order->recordStatusChange('shipped', Auth::id(), "Sinkronisasi Biteship: Resi {$waybill}");
            }

            $order->refresh();
            $this->statusMessage = "Status pesanan #{$order->order_number} berhasil disinkronkan dari Biteship (Status: {$biteshipStatus}).";

            if ($this->selectedOrder && $this->selectedOrder->id === $order->id) {
                $this->selectedOrder = $order;
                $this->trackingNumber = (string) ($order->shipment?->tracking_number ?? '');
            }
        } else {
            $this->statusMessage = "Tidak dapat mengambil data status dari Biteship untuk pesanan #{$order->order_number}.";
        }
    }

    public function render(): View
    {
        $query = Order::query()->with(['items', 'payment', 'shipment'])->latest('id');

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
