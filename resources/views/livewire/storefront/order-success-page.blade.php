<div class="py-12 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative">
    <!-- Success Banner -->
    <div class="bg-[var(--color-bg-surface)] p-8 md:p-12 rounded-[var(--radius-lg)] border border-black/10 dark:border-white/10 shadow-sm text-center mb-10 transition-colors duration-300">
        <div class="w-20 h-20 rounded-full bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-500/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-4xl mx-auto mb-6 shadow-inner transition-colors duration-300">
            ✓
        </div>
        <span class="inline-block px-3 py-1 text-xs font-mono font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800/40 rounded-full mb-3 transition-colors duration-300">
            Pesanan Terkonfirmasi
        </span>
        <h1 class="font-display text-3xl md:text-4xl font-bold text-[var(--color-text-primary)] mb-3 transition-colors duration-300">
            Terima Kasih Atas Pesanan Kamu!
        </h1>
        <p class="text-sm text-[var(--color-text-muted)] max-w-md mx-auto mb-6 transition-colors duration-300">
            Biji kopi Robusta pilihan dari petani Lampung sedang kami persiapkan untuk segera disangrai & dikirim ke alamat kamu.
        </p>

        <div class="inline-flex items-center space-x-2 px-4 py-2 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] font-mono text-sm transition-colors duration-300">
            <span class="text-[var(--color-text-muted)]">Nomor Pesanan:</span>
            <strong class="text-[var(--color-accent-gold)]">{{ $order->order_number }}</strong>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Order Items (7 cols) -->
        <div class="md:col-span-7 bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-6 transition-colors duration-300">
            <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                Item Kopi yang Dipesan
            </h3>

            <div class="divide-y divide-black/10 dark:divide-white/10 font-mono text-xs transition-colors duration-300">
                @foreach($order->items as $item)
                    <div class="py-3 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-[var(--color-text-primary)] block text-sm transition-colors duration-300">{{ $item->product_name }}</span>
                            <span class="text-[var(--color-text-muted)] transition-colors duration-300">{{ $item->variant_label }} (x{{ $item->quantity }})</span>
                        </div>
                        <span class="font-bold text-[var(--color-accent-gold)]">
                            Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="pt-4 border-t border-black/10 dark:border-white/10 space-y-2 text-xs font-mono transition-colors duration-300">
                <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                    <span>Subtotal Kopi</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                    <span>Ongkos Kirim ({{ $order->courier_name }})</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="flex items-center justify-between text-emerald-400 font-bold">
                        <span>Potongan / Subsidi Ongkir{{ $order->voucher_code ? " ({$order->voucher_code})" : '' }}</span>
                        <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-sm font-bold pt-2 border-t border-black/10 dark:border-white/10 transition-colors duration-300">
                    <span class="text-[var(--color-text-primary)] transition-colors duration-300">Total Akhir</span>
                    <span class="font-display text-lg text-[var(--color-accent-gold)]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Delivery & Support (5 cols) -->
        <div class="md:col-span-5 space-y-6">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 font-mono text-xs transition-colors duration-300">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 font-sans transition-colors duration-300">
                    Alamat Pengiriman
                </h3>

                <div>
                    <span class="text-[var(--color-text-muted)] block text-[10px] uppercase transition-colors duration-300">Penerima:</span>
                    <span class="text-[var(--color-text-primary)] font-bold block text-sm transition-colors duration-300">{{ $order->recipient_name }}</span>
                    <span class="text-[var(--color-text-muted)] transition-colors duration-300">{{ $order->recipient_phone }}</span>
                </div>

                <div>
                    <span class="text-[var(--color-text-muted)] block text-[10px] uppercase transition-colors duration-300">Alamat Lengkap:</span>
                    <p class="text-[var(--color-text-primary)] leading-relaxed mt-0.5 transition-colors duration-300">{{ $order->shipping_address }}</p>
                </div>
            </div>

            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 text-center transition-colors duration-300">
                <h4 class="font-display font-bold text-base text-[var(--color-text-primary)] transition-colors duration-300">Butuh Bantuan Pesanan?</h4>
                <p class="text-xs text-[var(--color-text-muted)] transition-colors duration-300"> Tim Way Kopi siap membantu pertanyaan seputar sangrai, pengiriman, & seduh kopi.</p>
                <a href="https://wa.me/6282160388791?text=Halo%20Way%20Kopi,%20saya%20ingin%20tanya%20pesanan%20%23{{ $order->order_number }}" target="_blank" class="inline-flex items-center justify-center space-x-2 w-full py-3 px-4 font-bold text-xs bg-emerald-700 text-white rounded-[var(--radius-sm)] hover:bg-emerald-600 transition-colors">
                    <span>💬 Chat CS via WhatsApp</span>
                </a>
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