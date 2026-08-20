<div class="py-12 max-w-4xl mx-auto">
    <!-- Success Banner -->
    <div class="bg-[var(--color-bg-surface)] p-8 md:p-12 rounded-[var(--radius-lg)] border border-[var(--color-coffee-brown)] shadow-2xl text-center mb-10">
        <div class="w-20 h-20 rounded-full bg-emerald-950/80 border border-emerald-500/40 text-emerald-400 flex items-center justify-center text-4xl mx-auto mb-6 shadow-inner">
            ✓
        </div>
        <span class="inline-block px-3 py-1 text-xs font-mono font-semibold uppercase tracking-wider text-emerald-400 bg-emerald-950/60 border border-emerald-800/40 rounded-full mb-3">
            Pesanan Terkonfirmasi
        </span>
        <h1 class="font-display text-3xl md:text-4xl font-bold text-[var(--color-text-primary)] mb-3">
            Terima Kasih Atas Pesanan Kamu!
        </h1>
        <p class="text-sm text-[var(--color-text-muted)] max-w-md mx-auto mb-6">
            Biji kopi Robusta pilihan dari petani Lampung sedang kami persiapkan untuk segera disangrai & dikirim ke alamat kamu.
        </p>

        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] font-mono text-sm">
            <span class="text-[var(--color-text-muted)]">Nomor Pesanan:</span>
            <strong class="text-[var(--color-accent-gold)]">{{ $order->order_number }}</strong>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Order Items (7 cols) -->
        <div class="md:col-span-7 bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-6">
            <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                Item Kopi yang Dipesan
            </h3>

            <div class="divide-y divide-[var(--color-coffee-brown)]/40 font-mono text-xs">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-[var(--color-text-primary)] block text-sm">{{ $item->product_name }}</span>
                            <span class="text-[var(--color-text-muted)]">{{ $item->variant_label }} (x{{ $item->quantity }})</span>
                        </div>
                        <span class="font-bold text-[var(--color-accent-gold)]">
                            Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-[var(--color-coffee-brown)] space-y-2 text-xs font-mono">
                <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                    <span>Subtotal Kopi</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                    <span>Ongkos Kirim ({{ $order->courier_name }})</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex items-center justify-between text-emerald-400 font-bold">
                        <span>Diskon Ongkir ({{ $order->voucher_code ?: 'Voucher' }})</span>
                        <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-sm font-bold pt-2 border-t border-[var(--color-coffee-brown)]/40">
                    <span class="text-[var(--color-text-primary)]">Total Akhir</span>
                    <span class="font-display text-lg text-[var(--color-accent-gold)]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Delivery & Support (5 cols) -->
        <div class="md:col-span-5 space-y-6">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4 font-mono text-xs">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3 font-sans">
                    Alamat Pengiriman
                </h3>

                <div>
                    <span class="text-[var(--color-text-muted)] block text-[10px] uppercase">Penerima:</span>
                    <span class="text-[var(--color-text-primary)] font-bold block text-sm">{{ $order->recipient_name }}</span>
                    <span class="text-[var(--color-text-muted)]">{{ $order->recipient_phone }}</span>
                </div>

                <div>
                    <span class="text-[var(--color-text-muted)] block text-[10px] uppercase">Alamat Lengkap:</span>
                    <p class="text-[var(--color-text-primary)] leading-relaxed mt-0.5">{{ $order->shipping_address }}</p>
                </div>
            </div>

            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4 text-center">
                <h4 class="font-display font-bold text-base text-[var(--color-text-primary)]">Butuh Bantuan Pesanan?</h4>
                <p class="text-xs text-[var(--color-text-muted)]"> Tim Way Kopi siap membantu pertanyaan seputar sangrai, pengiriman, & seduh kopi.</p>
                <a href="https://wa.me/6282160388791?text=Halo%20Way%20Kopi,%20saya%20ingin%20tanya%20pesanan%20%23{{ $order->order_number }}" target="_blank" class="inline-flex items-center justify-center space-x-2 w-full py-3 px-4 font-bold text-xs bg-emerald-700 text-white rounded-[var(--radius-sm)] hover:bg-emerald-600 transition-colors">
                    <span>💬 Chat CS via WhatsApp</span>
                </a>
            </div>
        </div>
    </div>
</div>
