<div class="py-8 max-w-7xl mx-auto space-y-8">
    <!-- Admin Header Banner -->
    <div class="bg-[var(--color-bg-surface)] p-6 md:p-8 rounded-[var(--radius-lg)] border border-[var(--color-coffee-brown)] shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <div class="flex items-center space-x-3">
                <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded">
                    Admin Panel
                </span>
                <h1 class="font-display text-2xl font-bold text-[var(--color-text-primary)]">
                    Dashboard Way Kopi
                </h1>
            </div>
            <p class="text-xs font-mono text-[var(--color-text-muted)] mt-1">
                Selamat datang kembali, <strong>{{ Auth::user()->name }}</strong>. Berikut ringkasan performa penjualan kopi.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 font-mono text-xs">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                📦 Kelola Pesanan
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] transition-colors">
                ☕ Kelola Produk
            </a>
            <a href="{{ route('admin.posts.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] transition-colors">
                📰 Kelola Blog
            </a>
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] transition-colors">
                👥 Kelola Pelanggan
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 font-mono">
        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-2">
            <span class="text-[10px] uppercase text-[var(--color-text-muted)] tracking-wider">Total Omzet Penjualan</span>
            <div class="font-display text-2xl font-bold text-[var(--color-accent-gold)]">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <span class="text-[10px] text-emerald-400">Dari pesanan berstatus Lunas</span>
        </div>

        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-2">
            <span class="text-[10px] uppercase text-[var(--color-text-muted)] tracking-wider">Total Pesanan</span>
            <div class="font-display text-2xl font-bold text-[var(--color-text-primary)]">
                {{ $totalOrders }}
            </div>
            <span class="text-[10px] text-[var(--color-text-muted)]">{{ $paidOrders }} Lunas • {{ $pendingOrders }} Pending</span>
        </div>

        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-2">
            <span class="text-[10px] uppercase text-[var(--color-text-muted)] tracking-wider">Total Produk Kopi</span>
            <div class="font-display text-2xl font-bold text-[var(--color-text-primary)]">
                {{ $totalProducts }}
            </div>
            <span class="text-[10px] text-[var(--color-text-muted)]">Katalog Kopi Robusta</span>
        </div>

        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-2">
            <span class="text-[10px] uppercase text-[var(--color-text-muted)] tracking-wider">Stok Rendah (< 10)</span>
            <div class="font-display text-2xl font-bold {{ $lowStockVariants->count() > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                {{ $lowStockVariants->count() }} Varian
            </div>
            <span class="text-[10px] text-[var(--color-text-muted)]">Perlu restock sangrai</span>
        </div>
    </div>

    <!-- Low Stock Alert Box -->
    @if($lowStockVariants->count() > 0)
        <div class="bg-amber-950/60 border border-amber-500/40 p-5 rounded-[var(--radius-md)] space-y-3 font-mono text-xs text-amber-300">
            <div class="flex items-center space-x-2 font-bold text-sm">
                <span>⚠️ Peringatan Stok Kopi Rendah:</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($lowStockVariants as $variant)
                    <span class="px-2.5 py-1 bg-amber-900/60 border border-amber-700/50 rounded text-[11px]">
                        {{ $variant->product->name }} ({{ $variant->weight_grams }}g) — <strong>Sisa {{ $variant->stock }} pcs</strong>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Orders Table -->
    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-6">
        <div class="flex items-center justify-between border-b border-[var(--color-coffee-brown)] pb-3">
            <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)]">
                Pesanan Terbaru
            </h3>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-mono text-[var(--color-accent-gold)] hover:underline">
                Lihat Semua Pesanan &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left font-mono text-xs divide-y divide-[var(--color-coffee-brown)]/40">
                <thead>
                    <tr class="text-[var(--color-text-muted)] uppercase text-[10px]">
                        <th class="py-3 px-4">No. Pesanan</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-coffee-brown)]/30">
                    @foreach($recentOrders as $order)
                        <tr class="hover:bg-[var(--color-bg-base)]/50 transition-colors">
                            <td class="py-3 px-4 font-bold text-[var(--color-accent-gold)]">
                                {{ $order->order_number }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-[var(--color-text-primary)] block font-bold">{{ $order->recipient_name }}</span>
                                <span class="text-[var(--color-text-muted)] text-[10px]">{{ $order->guest_email }}</span>
                            </td>
                            <td class="py-3 px-4 text-[var(--color-text-muted)]">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($order->status === 'paid')
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-emerald-400 bg-emerald-950 border border-emerald-700/50 rounded">Lunas</span>
                                @elseif($order->status === 'pending_payment')
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-amber-400 bg-amber-950 border border-amber-700/50 rounded">Pending</span>
                                @elseif($order->status === 'shipped')
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-blue-400 bg-blue-950 border border-blue-700/50 rounded">Dikirim</span>
                                @else
                                    <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-red-400 bg-red-950 border border-red-700/50 rounded">{{ strtoupper($order->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-bold text-[var(--color-text-primary)]">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.orders.index') }}" class="text-[var(--color-accent-gold)] hover:underline">Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
