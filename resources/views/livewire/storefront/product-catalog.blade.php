<div class="py-4 sm:py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full space-y-8 relative">
    <!-- Success Alert -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 dark:bg-emerald-950/80 border border-emerald-200 dark:border-emerald-800 rounded-[var(--radius-md)] text-xs text-emerald-700 dark:text-emerald-400 font-mono flex flex-col sm:flex-row items-center justify-between gap-2 shadow-sm transition-colors duration-300">
            <span>✓ {{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="font-bold underline text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-white">Lihat Keranjang Belanja &rarr;</a>
        </div>
    @endif

    <!-- Hero Banner -->
    @if($showHero)
    <div class="relative overflow-hidden rounded-[var(--radius-lg)] bg-white dark:bg-[var(--color-bg-surface)] border border-white dark:border-[#3A2E28] p-6 sm:p-8 md:p-12 shadow-sm transition-colors duration-300">
        <div class="max-w-2xl relative z-10">
            <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/20 rounded-full mb-4">
                Direct-to-Consumer Lampung
            </span>
            <h1 class="font-display text-2xl sm:text-4xl md:text-5xl font-bold text-main leading-tight mb-4 transition-colors">
                Kopi Robusta Petani Tanggamus
            </h1>
            <p class="text-xs sm:text-sm md:text-base text-muted leading-relaxed mb-6 transition-colors">
                Dipetik merah dari kebun lereng Gunung Tanggamus, Lampung pada ketinggian 800–1000 mdpl. Diproses secara etis dan disangrai presisi untuk memberikan karakter rasa <strong class="text-[var(--color-accent-gold)] font-medium">Dark Chocolate, Nutty, & Heavy Body</strong> yang mantap.
            </p>
            <div class="flex flex-wrap gap-3 sm:gap-4 text-xs font-mono text-muted transition-colors">
                <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-[var(--color-accent-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>100% Fine Robusta</span>
                </span>
                <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-[var(--color-accent-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Freshly Roasted</span>
                </span>
                <span class="flex items-center space-x-1">
                    <svg class="w-4 h-4 text-[var(--color-accent-gold)]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Langsung dari Petani</span>
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Filter & Search Controls Bar -->
    <div class="bg-white dark:bg-[var(--color-bg-surface)] p-4 sm:p-6 rounded-[var(--radius-md)] border border-white dark:border-[#3A2E28] shadow-sm transition-colors duration-300">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Cari Produk</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari rasa, daerah..." class="w-full pl-9 pr-4 py-2.5 bg-transparent border border-white dark:border-[#3A2E28] rounded-[var(--radius-sm)] text-sm text-main placeholder-muted focus:outline-none focus:border-[var(--color-accent-gold)] focus:ring-1 focus:ring-[var(--color-accent-gold)] transition-all">
                    <svg class="w-4 h-4 text-muted absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Grind Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Bentuk Kopi</label>
                <select wire:model.live="grindType" class="w-full px-4 py-2.5 bg-white dark:bg-[var(--color-bg-surface)] border border-white dark:border-[#3A2E28] rounded-[var(--radius-sm)] text-sm text-main focus:outline-none focus:border-[var(--color-accent-gold)] focus:ring-1 focus:ring-[var(--color-accent-gold)] transition-all">
                    <option value="">Semua Bentuk</option>
                    <option value="whole_bean">Biji Utuh (Whole Bean)</option>
                    <option value="fine">Bubuk Halus (Espresso/Tubruk)</option>
                    <option value="medium">Bubuk Sedang (V60/Drip)</option>
                    <option value="coarse">Bubuk Kasar (French Press)</option>
                </select>
            </div>

            <!-- Weight Filter -->
            <div>
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Ukuran Kemasan</label>
                <select wire:model.live="weightG" class="w-full px-4 py-2.5 bg-white dark:bg-[var(--color-bg-surface)] border border-white dark:border-[#3A2E28] rounded-[var(--radius-sm)] text-sm text-main focus:outline-none focus:border-[var(--color-accent-gold)] focus:ring-1 focus:ring-[var(--color-accent-gold)] transition-all">
                    <option value="">Semua Ukuran</option>
                    <option value="200">200 gram</option>
                    <option value="500">500 gram</option>
                    <option value="1000">1000 gram (1 kg)</option>
                </select>
            </div>

            <!-- Sorting -->
            <div>
                <label class="block text-xs font-semibold text-muted uppercase tracking-wider mb-2">Urutkan</label>
                <select wire:model.live="sort" class="w-full px-4 py-2.5 bg-white dark:bg-[var(--color-bg-surface)] border border-white dark:border-[#3A2E28] rounded-[var(--radius-sm)] text-sm text-main focus:outline-none focus:border-[var(--color-accent-gold)] focus:ring-1 focus:ring-[var(--color-accent-gold)] transition-all">
                    <option value="newest">Terbaru</option>
                    <option value="price_asc">Harga Terendah</option>
                    <option value="price_desc">Harga Tertinggi</option>
                </select>
            </div>
        </div>

        @if($search || $grindType || $weightG || $sort !== 'newest')
            <div class="mt-4 pt-4 border-t border-white dark:border-[#3A2E28] flex items-center justify-between">
                <span class="text-xs text-muted">Hasil filter pencarian</span>
                <button wire:click="resetFilters" class="text-xs font-semibold text-[var(--color-accent-gold)] hover:underline flex items-center space-x-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Reset Filter</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Product Grid -->
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
                @php
                    $primaryImage = $product->primaryImage;
                    $minPrice = $product->variants->min('price') ?? $product->base_price;
                    $totalStock = $product->variants->sum('stock');
                    $firstVariant = $product->variants->first();
                @endphp
                <div class="group bg-white dark:bg-[var(--color-bg-surface)] border border-white dark:border-[#3A2E28] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-md)] overflow-hidden flex flex-col transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1">
                    <!-- Image Wrapper -->
                    <div class="relative aspect-square bg-black/5 dark:bg-white/5 overflow-hidden">
                        <img src="{{ $primaryImage->url ?? '/images/products/produk-utama.jpg' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                        <!-- Stock Badge -->
                        <div class="absolute top-3 left-3">
                            @if($totalStock === 0)
                                <span class="px-2.5 py-1 text-xs font-bold uppercase bg-[var(--color-error)] text-white rounded-[var(--radius-sm)] shadow-md">
                                    Stok Habis
                                </span>
                            @elseif($totalStock < 10)
                                <span class="px-2.5 py-1 text-xs font-bold uppercase bg-amber-600 text-white rounded-[var(--radius-sm)] shadow-md">
                                    Stok Terbatas
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold uppercase bg-surface/90 text-[var(--color-accent-gold)] border border-[var(--color-accent-gold)]/30 backdrop-blur-sm rounded-[var(--radius-sm)] shadow-sm">
                                    Tersedia
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs text-muted mb-2 font-mono">
                                <span>{{ $product->origin ?? 'Tanggamus, Lampung' }}</span>
                                <span>{{ $product->roast_profile ?? 'Medium Dark' }}</span>
                            </div>
                            <h3 class="font-display font-bold text-lg text-main group-hover:text-[var(--color-accent-gold)] transition-colors mb-2">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-muted line-clamp-2">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Price & CTA Buttons -->
                        <div class="pt-4 border-t border-white dark:border-[#3A2E28] flex flex-col sm:flex-row items-start sm:items-center justify-between font-mono gap-3 transition-colors">
                            <div>
                                <span class="text-[10px] uppercase text-muted block">Harga</span>
                                <span class="font-display text-lg font-bold text-[var(--color-accent-gold)]">
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-2 w-full sm:w-auto">
                                @if($firstVariant && $firstVariant->stock > 0)
                                    <button wire:click="addToCart({{ $firstVariant->id }})" class="flex-1 sm:flex-none px-3 py-2 text-xs font-bold bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-sm text-center">
                                        + Keranjang
                                    </button>
                                @endif
                                <a href="{{ route('products.show', $product->slug) }}" class="flex-1 sm:flex-none px-3 py-2 text-xs font-semibold border border-white dark:border-[#3A2E28] text-main hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] transition-colors text-center">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white dark:bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-white dark:border-[#3A2E28] shadow-sm transition-colors duration-300">
            <h3 class="font-display text-lg font-bold text-main mb-2">Produk Tidak Ditemukan</h3>
            <p class="text-xs text-muted mb-6 max-w-sm mx-auto">
                Maaf, tidak ada kopi yang sesuai dengan kriteria pencarian atau filter yang kamu pilih.
            </p>
            <button wire:click="resetFilters" class="px-4 py-2 text-xs font-semibold bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-sm">
                Tampilkan Semua Produk
            </button>
        </div>
    @endif

    <!-- ========================================== -->
    <!-- MANUAL: TOMBOL TEMA & WHATSAPP MELAYANG -->
    <!-- ========================================== -->

    <!-- TOMBOL GANTI TEMA (KIRI BAWAH) -->
    <button onclick="toggleGlobalTheme()" 
            class="fixed bottom-6 left-6 z-50 p-3.5 rounded-full bg-[var(--color-bg-surface)] border border-white dark:border-[#3A2E28] text-[var(--color-accent-gold)] shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center group cursor-pointer"
            title="Ubah Mode Gelap / Terang">
        <span id="global-mode-icon" class="text-base">🌙</span>
        <span class="absolute left-full ml-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-white dark:border-[#3A2E28] uppercase tracking-wider pointer-events-none">
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
        <span class="absolute right-full mr-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-white dark:border-[#3A2E28] uppercase tracking-wider pointer-events-none">
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