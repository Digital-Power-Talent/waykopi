<div class="py-8 max-w-4xl mx-auto px-4">
    <!-- Header Banner -->
    <div class="bg-[var(--color-bg-surface)] p-6 md:p-8 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-xl mb-8 text-center relative overflow-hidden">
        <div class="w-16 h-16 rounded-full bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 flex items-center justify-center text-3xl mx-auto mb-4">
            🏦
        </div>
        <span class="inline-block px-3 py-1 text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/20 rounded-full mb-3">
            Menunggu Transfer Bank
        </span>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-[var(--color-text-primary)] mb-2">
            Pesanan #{{ $order->order_number }}
        </h1>
        <p class="text-xs font-mono text-[var(--color-text-muted)]">
            Silakan lakukan transfer sebelum <strong class="text-[var(--color-accent-gold)]">{{ $order->expires_at ? $order->expires_at->format('d M Y, H:i WIB') : '1 jam kedepan' }}</strong>.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" x-data="{ copiedAmount: false }">
        <!-- Left: Bank Transfer Details & Action (7 cols) -->
        <div class="md:col-span-7 space-y-6">
            <!-- Total Payment Card -->
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-accent-gold)]/40 shadow-xl space-y-4">
                <div class="flex items-center justify-between border-b border-[var(--color-coffee-brown)] pb-3">
                    <span class="text-xs uppercase font-mono tracking-wider text-[var(--color-text-muted)]">Total Tagihan Transfer</span>
                    <span class="text-[10px] font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded">Termasuk 3 Digit Kode Unik</span>
                </div>

                <div class="text-center py-3 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)]/60 rounded-[var(--radius-sm)]">
                    <div class="font-display text-3xl md:text-4xl font-bold text-[var(--color-accent-gold)] tracking-tight">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </div>
                    <div class="text-xs font-mono text-[var(--color-text-muted)] mt-1">
                        Subtotal + Ongkir: Rp {{ number_format($order->subtotal + $order->shipping_cost, 0, ',', '.') }} + <span class="text-amber-400 font-bold">Kode Unik: Rp {{ number_format($order->unique_code, 0, ',', '.') }}</span>
                    </div>

                    <button 
                        type="button"
                        @click="navigator.clipboard.writeText('{{ (int)$order->total }}'); copiedAmount = true; setTimeout(() => copiedAmount = false, 2000)"
                        class="mt-3 inline-flex items-center space-x-1.5 px-3 py-1.5 text-xs font-mono text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded hover:bg-[var(--color-accent-gold)] hover:text-[var(--color-bg-base)] transition-all cursor-pointer">
                        <span x-text="copiedAmount ? '✓ Nominal Tersalin!' : '📋 Salin Total Nominal Exact'"></span>
                    </button>
                </div>

                <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-[var(--radius-sm)] text-xs text-amber-200/90 leading-relaxed flex items-start space-x-2.5">
                    <span class="text-base leading-none">⚠️</span>
                    <div>
                        <strong>PENTING:</strong> Mohon transfer <strong>TEPAT</strong> sesuai nominal hingga 3 digit terakhir (<span class="font-mono text-amber-400 font-bold">{{ sprintf('%03d', $order->unique_code) }}</span>) agar pembayaran mudah diverifikasi oleh sistem & admin kami.
                    </div>
                </div>
            </div>

            <!-- Bank Accounts List -->
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3 flex items-center justify-between">
                    <span>Rekening Tujuan Transfer</span>
                    <span class="text-xs font-mono font-normal text-[var(--color-text-muted)]">a/n PT GUDANG KITA PERKASA</span>
                </h3>

                <div class="space-y-4">
                    @foreach($bankAccounts as $index => $bank)
                        <div x-data="{ copiedBank: false }" class="p-4 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 hover:border-[var(--color-accent-gold)]/50 transition-colors">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-sm text-[var(--color-text-primary)] font-mono">{{ $bank['bank'] }}</span>
                                </div>
                                <div class="font-mono text-lg font-bold text-[var(--color-accent-gold)] tracking-wide">
                                    {{ $bank['account_number'] }}
                                </div>
                                <div class="text-[11px] font-mono text-[var(--color-text-muted)]">
                                    Atas Nama: <strong class="text-[var(--color-text-primary)]">{{ $bank['account_name'] }}</strong>
                                </div>
                            </div>

                            <button 
                                type="button"
                                @click="navigator.clipboard.writeText('{{ $bank['account_number'] }}'); copiedBank = true; setTimeout(() => copiedBank = false, 2000)"
                                class="w-full sm:w-auto px-4 py-2 text-xs font-mono font-semibold bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)] text-[var(--color-text-primary)] hover:text-[var(--color-accent-gold)] rounded transition-all cursor-pointer text-center">
                                <span x-text="copiedBank ? '✓ Rekening Tersalin' : 'Salin No. Rekening'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- WhatsApp Confirmation CTA -->
            <div class="bg-gradient-to-br from-emerald-950/70 to-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-emerald-500/40 shadow-xl space-y-4 text-center">
                <div class="space-y-1">
                    <h4 class="font-display font-bold text-lg text-emerald-300">
                        Sudah Selesai Transfer?
                    </h4>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed max-w-md mx-auto">
                        Konfirmasikan pembayaran kamu ke Layanan WhatsApp WayKopi di <strong class="text-emerald-400 font-mono">6282160388791</strong> beserta bukti transfer agar pesanan langsung disangrai & dikirim.
                    </p>
                </div>

                <a href="{{ $whatsappUrl }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center justify-center space-x-2.5 w-full py-4 px-6 font-bold text-sm bg-emerald-600 hover:bg-emerald-500 text-white rounded-[var(--radius-sm)] transition-all shadow-lg hover:shadow-emerald-900/50 cursor-pointer">
                    <span class="text-xl">📲</span>
                    <span>Konfirmasi Pembayaran via WhatsApp</span>
                </a>

                <p class="text-[10px] font-mono text-[var(--color-text-muted)]">
                    Link akan membuka aplikasi WhatsApp dengan pesan konfirmasi yang sudah terisi otomatis.
                </p>
            </div>
        </div>

        <!-- Right: Order Items & Delivery Summary (5 cols) -->
        <div class="md:col-span-5">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-xl space-y-6 sticky top-6">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                    Rincian Kopi & Alamat
                </h3>

                <div class="space-y-3 font-mono text-xs">
                    <div class="text-[var(--color-text-muted)]">
                        <span class="block text-[10px] uppercase text-[var(--color-text-muted)]">Penerima</span>
                        <strong class="text-[var(--color-text-primary)] font-bold block mt-0.5">{{ $order->recipient_name }}</strong>
                        <span>{{ $order->recipient_phone }}</span>
                    </div>

                    <div class="text-[var(--color-text-muted)] pt-2 border-t border-[var(--color-coffee-brown)]/30">
                        <span class="block text-[10px] uppercase text-[var(--color-text-muted)]">Alamat Pengiriman</span>
                        <p class="text-[var(--color-text-primary)] mt-0.5 leading-relaxed">{{ $order->shipping_address }}</p>
                    </div>

                    <div class="text-[var(--color-text-muted)] pt-2 border-t border-[var(--color-coffee-brown)]/30">
                        <span class="block text-[10px] uppercase text-[var(--color-text-muted)]">Kurir Pengiriman</span>
                        <p class="text-[var(--color-accent-gold)] font-bold mt-0.5">{{ $order->courier_name }}</p>
                    </div>
                </div>

                <div class="space-y-2 pt-4 border-t border-[var(--color-coffee-brown)] text-xs font-mono">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-text-muted)]">{{ $item->quantity }}x {{ $item->product_name }}</span>
                            <span class="text-[var(--color-text-primary)] font-bold">Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <div class="pt-3 border-t border-[var(--color-coffee-brown)]/40 space-y-1 text-xs">
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Ongkos Kirim</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Kode Unik</span>
                            <span class="text-amber-400 font-bold">+ Rp {{ number_format($order->unique_code, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-[var(--color-coffee-brown)]/60 flex items-center justify-between text-sm font-bold">
                            <span class="text-[var(--color-text-primary)]">Total akhir</span>
                            <span class="font-display font-bold text-[var(--color-accent-gold)]">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
