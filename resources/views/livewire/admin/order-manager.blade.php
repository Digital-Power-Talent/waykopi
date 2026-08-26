<div class="py-8 max-w-7xl mx-auto space-y-8">
    <!-- Header Navigation -->
    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--color-coffee-brown)] shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded">
                Admin Panel
            </span>
            <h1 class="font-display text-2xl font-bold text-[var(--color-text-primary)] mt-1">
                Kelola Pesanan Kopi
            </h1>
        </div>

        <div class="flex items-center space-x-3 font-mono text-xs">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                &larr; Kembali ke Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                ☕ Kelola Produk
            </a>
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                👥 Kelola Pelanggan
            </a>
        </div>
    </div>

    <!-- Alert Status -->
    @if($statusMessage)
        <div class="p-4 bg-emerald-950/60 border border-emerald-500/40 rounded-[var(--radius-sm)] text-xs text-emerald-400 font-mono">
            ✓ {{ $statusMessage }}
        </div>
    @endif

    <!-- Controls Bar -->
    <div class="bg-[var(--color-bg-surface)] p-5 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] flex flex-col md:flex-row gap-4 justify-between font-mono text-xs">
        <div class="w-full md:w-80">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari no. pesanan, nama, email..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
        </div>

        <div>
            <select wire:model.live="statusFilter" class="w-full md:w-48 px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                <option value="">Semua Status</option>
                <option value="pending_payment">Menunggu Pembayaran</option>
                <option value="paid">Lunas (Paid)</option>
                <option value="processing">Diproses (Roasting/Packing)</option>
                <option value="shipped">Dikirim (Shipped)</option>
                <option value="delivered">Diterima (Delivered)</option>
                <option value="cancelled">Dibatalkan</option>
                <option value="expired">Expired</option>
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg">
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs divide-y divide-[var(--color-coffee-brown)]/40">
                    <thead>
                        <tr class="text-[var(--color-text-muted)] uppercase text-[10px]">
                            <th class="py-3 px-4">No. Pesanan</th>
                            <th class="py-3 px-4">Penerima & Alamat</th>
                            <th class="py-3 px-4">Kurir</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Total Tagihan</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-coffee-brown)]/30">
                        @foreach($orders as $order)
                            <tr class="hover:bg-[var(--color-bg-base)]/50 transition-colors">
                                <td class="py-3 px-4 font-bold text-[var(--color-accent-gold)]">
                                    {{ $order->order_number }}
                                    <span class="block text-[10px] text-[var(--color-text-muted)] font-normal">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <strong class="text-[var(--color-text-primary)] block">{{ $order->recipient_name }}</strong>
                                    <span class="text-[var(--color-text-muted)] text-[10px] block">{{ $order->recipient_phone }}</span>
                                    <span class="text-[var(--color-text-muted)] text-[10px] line-clamp-1 max-w-xs">{{ $order->shipping_address }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="text-[var(--color-text-primary)] block font-bold">{{ $order->courier_name }}</span>
                                    @if($order->shipment?->biteship_order_id)
                                        <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.5 text-[9px] bg-blue-950/80 border border-blue-600/50 text-blue-300 rounded font-mono" title="Biteship Order ID">
                                                🚚 {{ $order->shipment->biteship_order_id }}
                                            </span>
                                            <button wire:click="syncBiteshipStatus({{ $order->id }})" wire:loading.attr="disabled" class="text-[9px] text-amber-400 hover:text-amber-300 underline font-mono" title="Sinkronkan status dari Biteship">
                                                🔄 Sync
                                            </button>
                                        </div>
                                    @else
                                        <div class="mt-1">
                                            <button wire:click="sendToBiteship({{ $order->id }})" wire:loading.attr="disabled" class="px-2 py-0.5 text-[9px] font-bold bg-amber-500/20 border border-amber-500/50 text-amber-300 hover:bg-amber-500 hover:text-black rounded transition-colors">
                                                + Kirim Biteship
                                            </button>
                                        </div>
                                    @endif

                                    @if($order->shipment?->tracking_number)
                                        <span class="text-[10px] text-emerald-400 block font-mono mt-0.5">Resi: {{ $order->shipment->tracking_number }}</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @php
                                        $method = $order->payment?->method ?? ($order->status === 'paid' ? 'bank_transfer' : 'cod');
                                    @endphp
                                    @if($order->status === 'paid')
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-emerald-400 bg-emerald-950 border border-emerald-700/50 rounded block w-max">Lunas</span>
                                    @elseif($order->status === 'pending_payment')
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-amber-400 bg-amber-950 border border-amber-700/50 rounded block w-max">Pending</span>
                                    @elseif($order->status === 'shipped')
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-blue-400 bg-blue-950 border border-blue-700/50 rounded block w-max">Dikirim</span>
                                    @elseif($order->status === 'processing')
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-purple-400 bg-purple-950 border border-purple-700/50 rounded block w-max">Diproses</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-red-400 bg-red-950 border border-red-700/50 rounded block w-max">{{ strtoupper($order->status) }}</span>
                                    @endif

                                    <span class="text-[9px] text-[var(--color-text-muted)] mt-1 block uppercase font-mono">
                                        {{ $method === 'cod' ? '💵 COD' : '🏦 Transfer' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-bold text-[var(--color-text-primary)]">
                                    Rp {{ number_format($order->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right space-x-1.5 whitespace-nowrap">
                                    @if($order->shipment?->label_url)
                                        <a href="{{ $order->shipment->label_url }}" target="_blank" class="px-2.5 py-1.5 font-bold bg-blue-500/20 border border-blue-500/50 text-blue-300 hover:bg-blue-500 hover:text-black rounded transition-colors inline-flex items-center gap-1" title="Label Pengiriman Biteship">
                                            🏷️ Label
                                        </a>
                                    @endif
                                    @if(in_array($order->status, ['paid', 'processing', 'shipped', 'delivered']))
                                        <a href="{{ route('admin.orders.shipping-label', $order->id) }}" target="_blank" class="px-2.5 py-1.5 font-bold bg-amber-500/20 border border-amber-500/50 text-amber-300 hover:bg-amber-500 hover:text-black rounded transition-colors inline-flex items-center gap-1">
                                            📄 Resi
                                        </a>
                                    @endif
                                    <button wire:click="openOrderModal({{ $order->id }})" class="px-3 py-1.5 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                                        Update
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <p class="text-xs text-[var(--color-text-muted)] font-mono text-center py-12">
                Tidak ada data pesanan yang ditemukan.
            </p>
        @endif
    </div>

    <!-- Status Update Modal -->
    @if($showOrderModal && $selectedOrder)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-2xl max-w-lg w-full space-y-5 font-mono text-xs max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-[var(--color-coffee-brown)] pb-3">
                    <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] font-sans">
                        Detail Pesanan #{{ $selectedOrder->order_number }}
                    </h3>
                    <button wire:click="closeOrderModal" class="text-[var(--color-text-muted)] hover:text-white text-lg">&times;</button>
                </div>

                <!-- Items & Customer Summary -->
                <div class="p-3 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)]/40 rounded space-y-2">
                    <p><strong>Penerima:</strong> {{ $selectedOrder->recipient_name }} ({{ $selectedOrder->recipient_phone }})</p>
                    <p><strong>Alamat:</strong> {{ $selectedOrder->shipping_address }}</p>
                    <p><strong>Kurir Pilihan:</strong> {{ $selectedOrder->courier_name }}</p>
                    <div class="pt-2 border-t border-[var(--color-coffee-brown)]/30 space-y-1">
                        @foreach($selectedOrder->items as $item)
                            <div class="flex items-center justify-between">
                                <span>{{ $item->quantity }}x {{ $item->product_name }} ({{ $item->variant_label }})</span>
                                <span>Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="pt-2 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-between text-[11px] text-[var(--color-text-muted)]">
                            <span>Subtotal + Ongkir:</span>
                            <span>Rp {{ number_format($selectedOrder->subtotal + $selectedOrder->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if($selectedOrder->discount_amount > 0)
                            <div class="flex items-center justify-between text-[11px] text-emerald-400 font-bold">
                                <span>Potongan / Subsidi Ongkir{{ $selectedOrder->voucher_code ? " ({$selectedOrder->voucher_code})" : '' }}:</span>
                                <span>- Rp {{ number_format($selectedOrder->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between text-[11px] text-amber-400">
                            <span>Kode Unik 3 Digit:</span>
                            <span class="font-bold">+ Rp {{ number_format($selectedOrder->unique_code, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between font-bold text-xs text-[var(--color-accent-gold)] pt-1 border-t border-[var(--color-coffee-brown)]/40">
                            <span>Total Tagihan:</span>
                            <span>Rp {{ number_format($selectedOrder->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Biteship Shipping Integration Info -->
                <div class="p-3 bg-[var(--color-bg-base)] border border-blue-500/30 rounded space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-blue-400 uppercase text-[10px]">🚚 Integrasi Pengiriman Biteship</span>
                        @if($selectedOrder->shipment?->biteship_order_id)
                            <span class="px-2 py-0.5 bg-emerald-950 border border-emerald-700/50 text-emerald-400 text-[9px] rounded font-bold">Terdaftar</span>
                        @else
                            <span class="px-2 py-0.5 bg-amber-950 border border-amber-700/50 text-amber-400 text-[9px] rounded font-bold">Belum Dikirim ke Biteship</span>
                        @endif
                    </div>

                    @if($selectedOrder->shipment?->biteship_order_id)
                        <div class="grid grid-cols-2 gap-2 text-[11px] pt-1">
                            <div>
                                <span class="text-[var(--color-text-muted)] block text-[10px]">Biteship Order ID:</span>
                                <strong class="text-blue-300 font-mono">{{ $selectedOrder->shipment->biteship_order_id }}</strong>
                            </div>
                            <div>
                                <span class="text-[var(--color-text-muted)] block text-[10px]">Status Pengiriman:</span>
                                <strong class="text-[var(--color-text-primary)] uppercase">{{ $selectedOrder->shipment->status }}</strong>
                            </div>
                            @if($selectedOrder->shipment->tracking_number)
                                <div class="col-span-2">
                                    <span class="text-[var(--color-text-muted)] block text-[10px]">Nomor Resi (Waybill):</span>
                                    <strong class="text-emerald-400 font-mono text-xs">{{ $selectedOrder->shipment->tracking_number }}</strong>
                                </div>
                            @endif
                        </div>

                        <div class="pt-2 flex items-center gap-2">
                            <button type="button" wire:click="syncBiteshipStatus({{ $selectedOrder->id }})" wire:loading.attr="disabled" class="px-3 py-1.5 bg-blue-500/20 border border-blue-500/40 text-blue-300 hover:bg-blue-500 hover:text-black rounded text-[11px] font-bold transition-colors">
                                🔄 Sinkronkan Status dari Biteship
                            </button>
                            @if($selectedOrder->shipment->label_url)
                                <a href="{{ $selectedOrder->shipment->label_url }}" target="_blank" class="px-3 py-1.5 bg-amber-500/20 border border-amber-500/40 text-amber-300 hover:bg-amber-500 hover:text-black rounded text-[11px] font-bold transition-colors inline-flex items-center gap-1">
                                    🏷️ Cetak Label Biteship
                                </a>
                            @endif
                        </div>
                    @else
                        <div class="pt-1 flex items-center justify-between">
                            <p class="text-[10px] text-[var(--color-text-muted)]">
                                Daftarkan pesanan ke kurir Biteship untuk mendapatkan resi otomatis & request pickup.
                            </p>
                            <button type="button" wire:click="sendToBiteship({{ $selectedOrder->id }})" wire:loading.attr="disabled" class="px-3 py-1.5 bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] hover:bg-[var(--color-accent-gold-bright)] rounded font-bold text-[11px] transition-colors whitespace-nowrap">
                                📦 Daftarkan ke Biteship
                            </button>
                        </div>
                    @endif
                </div>

                <form wire:submit.prevent="updateOrderStatus" class="space-y-4">
                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Pilih Status Baru</label>
                        <select wire:model="newStatus" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded focus:outline-none focus:border-[var(--color-accent-gold)]">
                            <option value="pending_payment">Menunggu Pembayaran (pending_payment)</option>
                            <option value="paid">Lunas (paid)</option>
                            <option value="processing">Diproses Sangrai (processing)</option>
                            <option value="shipped">Dikirim (shipped)</option>
                            <option value="delivered">Diterima Pelanggan (delivered)</option>
                            <option value="cancelled">Dibatalkan (cancelled)</option>
                            <option value="expired">Expired (expired)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Nomor Resi Pengiriman (Khusus Dikirim)</label>
                        <input type="text" wire:model="trackingNumber" placeholder="misal: SOCAG001928374" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded focus:outline-none focus:border-[var(--color-accent-gold)]">
                    </div>

                    <div class="pt-4 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-between">
                        <div>
                            @if(in_array($selectedOrder->status, ['paid', 'processing', 'shipped', 'delivered']))
                                <a href="{{ route('admin.orders.shipping-label', $selectedOrder->id) }}" target="_blank" class="px-3 py-2 font-bold bg-amber-500/20 border border-amber-500/50 text-amber-300 hover:bg-amber-500 hover:text-black rounded transition-colors inline-flex items-center gap-1">
                                    🖨️ Cetak Resi
                                </a>
                            @endif
                        </div>
                        <div class="flex items-center space-x-3">
                            <button type="button" wire:click="closeOrderModal" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-muted)] rounded">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
