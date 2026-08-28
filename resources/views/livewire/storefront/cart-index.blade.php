<div class="py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full relative">
    <h1 class="font-display text-3xl font-bold text-[var(--color-text-primary)] mb-8 transition-colors duration-300">
        Keranjang Belanja Kamu
    </h1>

    @if($cartItems->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Items Table (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 overflow-hidden shadow-sm transition-colors duration-300">
                    <div class="p-4 border-b border-black/10 dark:border-white/10 flex items-center justify-between font-mono text-xs text-[var(--color-text-muted)] transition-colors duration-300">
                        <span>Detail Item ({{ $itemCount }} Kopi)</span>
                        <button wire:click="clearCart" class="text-[var(--color-error)] hover:underline">
                            Kosongkan Keranjang
                        </button>
                    </div>

                    <div class="divide-y divide-black/10 dark:divide-white/10 transition-colors duration-300">
                        @foreach($cartItems as $item)
                            @php
                                $variant = $item['variant'];
                                $product = $variant->product;
                                $image = $product->primaryImage ?? $product->images->first();
                            @endphp
                            <div class="p-4 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 overflow-hidden flex-shrink-0 transition-colors duration-300">
                                        @if($image)
                                            <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs font-mono text-[var(--color-text-muted)]">
                                                Way Kopi
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] transition-colors duration-300">
                                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-[var(--color-accent-gold)] transition-colors">
                                                {{ $product->name }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center space-x-2 text-xs font-mono text-[var(--color-text-muted)] mt-1 transition-colors duration-300">
                                            <span>{{ $variant->grind_type_label }}</span>
                                            <span>•</span>
                                            <span>{{ $variant->weight_grams }}g</span>
                                        </div>
                                        <div class="text-xs font-mono text-[var(--color-accent-gold)] mt-1">
                                            Rp {{ number_format($item['item_price'], 0, ',', '.') }} / pack
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between md:justify-end space-x-6 pt-2 md:pt-0 border-t md:border-t-0 border-black/10 dark:border-white/10 transition-colors duration-300">
                                    <!-- Quantity Controller -->
                                    <div class="flex items-center border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] transition-colors duration-300">
                                        <button wire:click="updateQuantity({{ $variant->id }}, {{ $item['quantity'] - 1 }})" class="px-2.5 py-1 text-sm font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors">-</button>
                                        <span class="px-3 py-1 text-xs font-mono font-bold text-[var(--color-text-primary)]">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $variant->id }}, {{ $item['quantity'] + 1 }})" class="px-2.5 py-1 text-sm font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors">+</button>
                                    </div>

                                    <!-- Item Subtotal -->
                                    <div class="text-right font-display font-bold text-base text-[var(--color-accent-gold)] min-w-[100px]">
                                        Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                    </div>

                                    <!-- Remove Button -->
                                    <button wire:click="removeItem({{ $variant->id }})" class="text-[var(--color-text-muted)] hover:text-[var(--color-error)] transition-colors p-1" title="Hapus item">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar (4 cols) -->
            <div class="lg:col-span-4">
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-6 transition-colors duration-300">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                        Ringkasan Pesanan
                    </h3>

                    <div class="space-y-3 text-sm font-mono">
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Total Item</span>
                            <span class="text-[var(--color-text-primary)] font-bold">{{ $itemCount }} Kopi</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Total Berat</span>
                            <span class="text-[var(--color-text-primary)] font-bold">{{ number_format($totalWeightGrams / 1000, 2) }} kg ({{ $totalWeightGrams }}g)</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Subtotal Kopi</span>
                            <span class="text-[var(--color-text-primary)] font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-[var(--color-text-muted)] pt-2 border-t border-black/10 dark:border-white/10 transition-colors duration-300">
                            <span>Ongkos Kirim</span>
                            <span>Dihitung saat Checkout</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-black/10 dark:border-white/10 transition-colors duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-xs uppercase text-[var(--color-text-muted)] font-mono transition-colors duration-300">Total Sementara</span>
                            <span class="font-display text-2xl font-bold text-[var(--color-accent-gold)]">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="block w-full py-4 px-6 text-center font-bold text-sm bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-sm">
                            Lanjut ke Checkout &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm transition-colors duration-300">
            <svg class="w-16 h-16 text-[var(--color-text-muted)] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <h3 class="font-display text-xl font-bold text-[var(--color-text-primary)] mb-2 transition-colors duration-300">Keranjang Belanja Masih Kosong</h3>
            <p class="text-xs text-[var(--color-text-muted)] max-w-md mx-auto mb-6 transition-colors duration-300">
                Kamu belum menambahkan varian kopi Robusta Lampung ke dalam keranjang. Yuk jelajahi katalog panen terbaru kami!
            </p>
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 text-xs font-bold bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-sm">
                Lihat Katalog Kopi &rarr;
            </a>
        </div>
    @endif

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