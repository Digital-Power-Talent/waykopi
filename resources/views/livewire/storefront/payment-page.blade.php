<div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative">
    <!-- Header Banner -->
    <div class="bg-[var(--color-bg-surface)] p-6 md:p-8 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm mb-8 text-center relative overflow-hidden transition-colors duration-300">
        <div class="w-16 h-16 rounded-full bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 flex items-center justify-center text-3xl mx-auto mb-4">
            🏦
        </div>
        <span class="inline-block px-3 py-1 text-xs font-mono font-semibold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/20 rounded-full mb-3">
            Menunggu Transfer Bank
        </span>
        <h1 class="font-display text-2xl md:text-3xl font-bold text-[var(--color-text-primary)] mb-2 transition-colors duration-300">
            Pesanan #{{ $order->order_number }}
        </h1>
        <p class="text-xs font-mono text-[var(--color-text-muted)] transition-colors duration-300">
            Silakan lakukan transfer sebelum <strong class="text-[var(--color-accent-gold)]">{{ $order->expires_at ? $order->expires_at->format('d M Y, H:i WIB') : '1 jam kedepan' }}</strong>.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8" x-data="{ copiedAmount: false }">
        <!-- Left: Bank Transfer Details & Action (7 cols) -->
        <div class="md:col-span-7 space-y-6">
            <!-- Total Payment Card -->
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-accent-gold)]/40 shadow-sm space-y-4 transition-colors duration-300">
                <div class="flex items-center justify-between border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                    <span class="text-xs uppercase font-mono tracking-wider text-[var(--color-text-muted)] transition-colors duration-300">Total Tagihan Transfer</span>
                    <span class="text-[10px] font-mono bg-amber-500/10 text-amber-400 border border-amber-500/30 px-2 py-0.5 rounded">Termasuk 3 Digit Kode Unik</span>
                </div>

                <div class="text-center py-3 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] transition-colors duration-300">
                    <div class="font-display text-3xl md:text-4xl font-bold text-[var(--color-accent-gold)] tracking-tight">
                        Rp {{ number_format($order->total, 0, ',', '.') }}
                    </div>
                    <div class="text-xs font-mono text-[var(--color-text-muted)] mt-1 space-y-0.5 transition-colors duration-300">
                        <div>
                            Subtotal + Ongkir: Rp {{ number_format($order->subtotal + $order->shipping_cost, 0, ',', '.') }}
                            @if($order->discount_amount > 0)
                                <span class="text-emerald-400 font-bold"> - Subsidi Ongkir{{ $order->voucher_code ? " ({$order->voucher_code})" : '' }}: Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            @endif
                            + <span class="text-amber-400 font-bold">Kode Unik: Rp {{ number_format($order->unique_code, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button 
                        type="button"
                        @click="navigator.clipboard.writeText('{{ (int)$order->total }}'); copiedAmount = true; setTimeout(() => copiedAmount = false, 2000)"
                        class="mt-3 inline-flex items-center space-x-1.5 px-3 py-1.5 text-xs font-mono text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded hover:bg-[var(--color-accent-gold)] hover:text-black transition-all cursor-pointer">
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
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 transition-colors duration-300">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 flex items-center justify-between transition-colors duration-300">
                    <span>Rekening Tujuan Transfer</span>
                    <span class="text-xs font-mono font-normal text-[var(--color-text-muted)]">a/n PT GUDANG KITA PERKASA</span>
                </h3>

                <div class="space-y-4">
                    @foreach($bankAccounts as $index => $bank)
                        <div x-data="{ copiedBank: false }" class="p-4 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 hover:border-[var(--color-accent-gold)]/50 transition-colors">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-bold text-sm text-[var(--color-text-primary)] font-mono transition-colors duration-300">{{ $bank['bank'] }}</span>
                                </div>
                                <div class="font-mono text-lg font-bold text-[var(--color-accent-gold)] tracking-wide">
                                    {{ $bank['account_number'] }}
                                </div>
                                <div class="text-[11px] font-mono text-[var(--color-text-muted)] transition-colors duration-300">
                                    Atas Nama: <strong class="text-[var(--color-text-primary)] transition-colors duration-300">{{ $bank['account_name'] }}</strong>
                                </div>
                            </div>

                            <button 
                                type="button"
                                @click="navigator.clipboard.writeText('{{ $bank['account_number'] }}'); copiedBank = true; setTimeout(() => copiedBank = false, 2000)"
                                class="w-full sm:w-auto px-4 py-2 text-xs font-mono font-semibold bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 hover:border-[var(--color-accent-gold)] text-[var(--color-text-primary)] hover:text-[var(--color-accent-gold)] rounded transition-all cursor-pointer text-center">
                                <span x-text="copiedBank ? '✓ Rekening Tersalin' : 'Salin No. Rekening'"></span>
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- WhatsApp Confirmation CTA -->
            <div class="bg-gradient-to-br from-emerald-950/70 to-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-emerald-500/40 shadow-sm space-y-4 text-center transition-colors duration-300">
                <div class="space-y-1">
                    <h4 class="font-display font-bold text-lg text-emerald-300">
                        Sudah Selesai Transfer?
                    </h4>
                    <p class="text-xs text-[var(--color-text-muted)] leading-relaxed max-w-md mx-auto transition-colors duration-300">
                        Konfirmasikan pembayaran kamu ke Layanan WhatsApp WayKopi di <strong class="text-emerald-400 font-mono">6282160388791</strong> beserta bukti transfer agar pesanan langsung disangrai & dikirim.
                    </p>
                </div>

                <a href="{{ $whatsappUrl }}" 
                   target="_blank" 
                   rel="noopener noreferrer"
                   class="inline-flex items-center justify-center space-x-2.5 w-full py-4 px-6 font-bold text-sm bg-emerald-600 hover:bg-emerald-500 text-white rounded-[var(--radius-sm)] transition-all shadow-sm cursor-pointer">
                    <span class="text-xl">📲</span>
                    <span>Konfirmasi Pembayaran via WhatsApp</span>
                </a>

                <p class="text-[10px] font-mono text-[var(--color-text-muted)] transition-colors duration-300">
                    Link akan membuka aplikasi WhatsApp dengan pesan konfirmasi yang sudah terisi otomatis.
                </p>
            </div>
        </div>

        <!-- Right: Order Items & Delivery Summary (5 cols) -->
        <div class="md:col-span-5">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-6 sticky top-6 transition-colors duration-300">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                    Rincian Kopi & Alamat
                </h3>

                <div class="space-y-3 font-mono text-xs">
                    <div class="text-[var(--color-text-muted)] transition-colors duration-300">
                        <span class="block text-[10px] uppercase text-[var(--color-text-muted)]">Penerima</span>
                        <strong class="text-[var(--color-text-primary)] font-bold block mt-0.5 transition-colors duration-300">{{ $order->recipient_name }}</strong>
                        <span>{{ $order->recipient_phone }}</span>
                    </div>

                    <div class="text-[var(--color-text-muted)] pt-2 border-t border-black/10 dark:border-white/10 transition-colors duration-300">
                        <span class="block text-[10px] uppercase text-[var(--color-text-muted)]">Alamat Pengiriman</span>
                        <p class="text-[var(--color-text-primary)] mt-0.5 leading-relaxed transition-colors duration-300">{{ $order->shipping_address }}</p>
                    </div>

                    <div class="text-[var(--color-text-muted)] pt-2 border-t border-black/10 dark:border-white/10 transition-colors duration-300">
                        <span class="block text-[10px] uppercase text-[var(--color-text-muted)]">Kurir Pengiriman</span>
                        <p class="text-[var(--color-accent-gold)] font-bold mt-0.5">{{ $order->courier_name }}</p>
                    </div>
                </div>

                <div class="space-y-2 pt-4 border-t border-black/10 dark:border-white/10 text-xs font-mono transition-colors duration-300">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between">
                            <span class="text-[var(--color-text-muted)] transition-colors duration-300">{{ $item->quantity }}x {{ $item->product_name }}</span>
                            <span class="text-[var(--color-text-primary)] font-bold transition-colors duration-300">Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
                        </div>
                    @endforeach

                    <div class="pt-3 border-t border-black/10 dark:border-white/10 space-y-1 text-xs transition-colors duration-300">
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Ongkos Kirim</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Kode Unik</span>
                            <span class="text-amber-400 font-bold">+ Rp {{ number_format($order->unique_code, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-black/10 dark:border-white/10 flex items-center justify-between text-sm font-bold transition-colors duration-300">
                            <span class="text-[var(--color-text-primary)] transition-colors duration-300">Total akhir</span>
                            <span class="font-display font-bold text-[var(--color-accent-gold)]">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MANUAL: TOMBOL TEMA & WHATSAPP MELAYANG -->
    <!-- ========================================== -->

    <!-- TOMBOL GANTI TEMA (KIRI BAWAH) -->
    <button onclick="toggleGlobalTheme()" 
            class="fixed bottom-6 left-6 z-50 p-3.5 rounded-full bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 text-[var(--color-accent-gold)] shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center group cursor-pointer"
            title="Ubah Mode Gelap / Terang">
        <span id="global-mode-icon" class="text-base">🌙</span>
        <span class="absolute left-full ml-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-black/10 dark:border-white/10 uppercase tracking-wider pointer-events-none">
            Ubah Tema
        </span>
    </button>

    <!-- TOMBOL WHATSAPP (KANAN BAWAH) -->
    <a href="https://wa.me/6281234567890?text=Halo%20Way%20Kopi,%20saya%20tertarik%20untuk%20memesan%20kopi." 
       target="_blank" 
       class="fixed bottom-6 right-6 z-50 bg-green-600 hover:bg-green-700 text-white p-4 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 group cursor-pointer"
       title="Chat WhatsApp CS Way Kopi">
        <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
        <span class="absolute right-full mr-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-black/10 dark:border-white/10 uppercase tracking-wider pointer-events-none">
            Chat WhatsApp CS
        </span>
    </a>

    <!-- SCRIPT GANTI TEMA SINKRON -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('waykopi_theme');
            const icon = document.getElementById('global-mode-icon');
            if (savedTheme === 'light') {
                document.documentElement.classList.remove('dark');
                if (icon) icon.innerText = '☀️';
            } else {
                document.documentElement.classList.add('dark');
                if (icon) icon.innerText = '🌙';
            }
        })();

        if (typeof toggleGlobalTheme !== 'function') {
            function toggleGlobalTheme() {
                const html = document.documentElement;
                const icon = document.getElementById('global-mode-icon');
                
                if (html.classList.contains('dark')) {
                    html.classList.remove('dark');
                    localStorage.setItem('waykopi_theme', 'light');
                    if (icon) icon.innerText = '☀️';
                } else {
                    html.classList.add('dark');
                    localStorage.setItem('waykopi_theme', 'dark');
                    if (icon) icon.innerText = '🌙';
                }
            }
        }
    </script>
</div>