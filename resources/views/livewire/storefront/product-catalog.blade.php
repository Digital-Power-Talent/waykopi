<div class="py-4 sm:py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 space-y-8">
    <!-- Success Alert -->
    @if(session()->has('success'))
        <div class="p-4 bg-emerald-950/80 border border-emerald-500/50 rounded-[var(--radius-md)] text-xs text-emerald-400 font-mono flex flex-col sm:flex-row items-center justify-between gap-2 shadow-lg">
            <span>✓ {{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="font-bold underline text-emerald-300 hover:text-white">Lihat Keranjang Belanja &rarr;</a>
        </div>
    @endif

    <!-- Hero Banner -->
    @if($showHero)
    <div class="relative overflow-hidden rounded-[var(--radius-lg)] bg-gradient-to-r from-[var(--color-bg-surface)] to-[#281E16] border border-[var(--color-coffee-brown)] p-6 sm:p-8 md:p-12 shadow-xl">
        <div class="max-w-2xl">
            <span class="inline-block px-3 py-1 text-xs font-semibold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/20 rounded-full mb-4">
                Direct-to-Consumer Lampung
            </span>
            <h1 class="font-display text-2xl sm:text-4xl md:text-5xl font-bold text-[var(--color-text-primary)] leading-tight mb-4">
                Kopi Robusta Petani Tanggamus
            </h1>
            <p class="text-xs sm:text-sm md:text-base text-[var(--color-text-muted)] leading-relaxed mb-6">
                Dipetik merah dari kebun lereng Gunung Tanggamus, Lampung pada ketinggian 800–1000 mdpl. Diproses secara etis dan disangrai presisi untuk memberikan karakter rasa <strong class="text-[var(--color-accent-gold)] font-medium">Dark Chocolate, Nutty, & Heavy Body</strong> yang mantap.
            </p>
            <div class="flex flex-wrap gap-3 sm:gap-4 text-xs font-mono text-[var(--color-text-muted)]">
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
    <div class="bg-[var(--color-bg-surface)] p-4 sm:p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2">Cari Produk</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari rasa, daerah..." class="w-full pl-9 pr-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors">
                    <svg class="w-4 h-4 text-[var(--color-text-muted)] absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Grind Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2">Bentuk Kopi</label>
                <select wire:model.live="grindType" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors">
                    <option value="">Semua Bentuk</option>
                    <option value="whole_bean">Biji Utuh (Whole Bean)</option>
                    <option value="fine">Bubuk Halus (Espresso/Tubruk)</option>
                    <option value="medium">Bubuk Sedang (V60/Drip)</option>
                    <option value="coarse">Bubuk Kasar (French Press)</option>
                </select>
            </div>

            <!-- Weight Filter -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2">Ukuran Kemasan</label>
                <select wire:model.live="weightG" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors">
                    <option value="">Semua Ukuran</option>
                    <option value="200">200 gram</option>
                    <option value="500">500 gram</option>
                    <option value="1000">1000 gram (1 kg)</option>
                </select>
            </div>

            <!-- Sorting -->
            <div>
                <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2">Urutkan</label>
                <select wire:model.live="sort" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors">
                    <option value="newest">Terbaru</option>
                    <option value="price_asc">Harga Terendah</option>
                    <option value="price_desc">Harga Tertinggi</option>
                </select>
            </div>
        </div>

        @if($search || $grindType || $weightG || $sort !== 'newest')
            <div class="mt-4 pt-4 border-t border-[var(--color-coffee-brown)]/50 flex items-center justify-between">
                <span class="text-xs text-[var(--color-text-muted)]">Hasil filter pencarian</span>
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
                <div class="group bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-md)] overflow-hidden flex flex-col hover:border-[var(--color-accent-gold)] transition-all duration-200 shadow-lg">
                    <!-- Image Wrapper -->
                    <div class="relative aspect-square bg-[var(--color-bg-base)] overflow-hidden">
                        <img src="{{ $primaryImage->url ?? '/images/products/produk-utama.jpg' }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                        <!-- Stock Badge -->
                        <div class="absolute top-3 left-3">
                            @if($totalStock === 0)
                                <span class="px-2.5 py-1 text-xs font-bold uppercase bg-[var(--color-error)] text-white rounded-[var(--radius-sm)]">
                                    Stok Habis
                                </span>
                            @elseif($totalStock < 10)
                                <span class="px-2.5 py-1 text-xs font-bold uppercase bg-amber-600 text-white rounded-[var(--radius-sm)]">
                                    Stok Terbatas
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs font-semibold uppercase bg-[var(--color-bg-base)]/80 text-[var(--color-accent-gold)] border border-[var(--color-accent-gold)]/30 backdrop-blur-sm rounded-[var(--radius-sm)]">
                                    Tersedia
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Content -->
                    <div class="p-5 sm:p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <div class="flex items-center justify-between text-xs text-[var(--color-text-muted)] mb-2 font-mono">
                                <span>{{ $product->origin ?? 'Tanggamus, Lampung' }}</span>
                                <span>{{ $product->roast_profile ?? 'Medium Dark' }}</span>
                            </div>
                            <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] group-hover:text-[var(--color-accent-gold)] transition-colors mb-2">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <p class="text-xs text-[var(--color-text-muted)] line-clamp-2">
                                {{ $product->description }}
                            </p>
                        </div>

                        <!-- Price & CTA Buttons -->
                        <div class="pt-4 border-t border-[var(--color-coffee-brown)]/50 flex flex-col sm:flex-row items-start sm:items-center justify-between font-mono gap-3">
                            <div>
                                <span class="text-[10px] uppercase text-[var(--color-text-muted)] block">Harga</span>
                                <span class="font-display text-lg font-bold text-[var(--color-accent-gold)]">
                                    Rp {{ number_format($minPrice, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-2 w-full sm:w-auto">
                                @if($firstVariant && $firstVariant->stock > 0)
                                    <button wire:click="addToCart({{ $firstVariant->id }})" class="flex-1 sm:flex-none px-3 py-2 text-xs font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow text-center">
                                        + Keranjang
                                    </button>
                                @endif
                                <a href="{{ route('products.show', $product->slug) }}" class="flex-1 sm:flex-none px-3 py-2 text-xs font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] transition-colors text-center">
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
        <div class="text-center py-16 bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
            <h3 class="font-display text-lg font-bold text-[var(--color-text-primary)] mb-2">Produk Tidak Ditemukan</h3>
            <p class="text-xs text-[var(--color-text-muted)] mb-6 max-w-sm mx-auto">
                Maaf, tidak ada kopi yang sesuai dengan kriteria pencarian atau filter yang kamu pilih.
            </p>
            <button wire:click="resetFilters" class="px-4 py-2 text-xs font-semibold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                Tampilkan Semua Produk
            </button>
        </div>
    @endif
</div>
