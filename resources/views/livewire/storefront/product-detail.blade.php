<div class="py-4 sm:py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-xs font-mono text-[var(--color-text-muted)] mb-6 sm:mb-8 overflow-x-auto whitespace-nowrap">
        <a href="{{ url('/') }}" class="hover:text-[var(--color-accent-gold)]">Beranda</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-[var(--color-accent-gold)]">Katalog</a>
        <span>/</span>
        <span class="text-[var(--color-accent-gold)] truncate">{{ $product->name }}</span>
    </nav>

    <!-- Success Flash Message -->
    @if(session()->has('success'))
        <div class="p-4 mb-6 rounded-[var(--radius-sm)] bg-[var(--color-success)]/10 border border-[var(--color-success)] text-[var(--color-success)] text-xs sm:text-sm flex flex-col sm:flex-row items-center justify-between gap-2 shadow-lg">
            <span>{{ session('success') }}</span>
            <a href="{{ route('cart.index') }}" class="font-bold underline">Lihat Keranjang &rarr;</a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
        <!-- Left: Image Gallery (5 cols) -->
        <div class="lg:col-span-5 space-y-4">
            <div class="aspect-square bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-md)] overflow-hidden relative shadow-lg">
                <img src="{{ $selectedImageUrl ?? '/images/products/produk-utama.jpg' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>

            <!-- Thumbnails -->
            @if($product->images->count() > 1)
                <div class="flex items-center space-x-3 overflow-x-auto pb-2">
                    @foreach($product->images as $img)
                        <button wire:click="selectImage('{{ $img->url }}')" class="w-16 h-16 sm:w-20 sm:h-20 rounded-[var(--radius-sm)] overflow-hidden border-2 transition-all flex-shrink-0 {{ $selectedImageUrl === $img->url ? 'border-[var(--color-accent-gold)] scale-105' : 'border-[var(--color-coffee-brown)] opacity-70 hover:opacity-100' }}">
                            <img src="{{ $img->url }}" alt="Thumbnail" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Right: Product Info & Purchase Options (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <div>
                <!-- Origin & Altitude Chips -->
                <div class="flex flex-wrap items-center gap-2 mb-3 font-mono text-xs text-[var(--color-accent-gold)]">
                    <span class="px-2.5 py-1 bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] rounded">
                        📍 {{ $product->origin ?? 'Tanggamus, Lampung' }}
                    </span>
                    <span class="px-2.5 py-1 bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] rounded">
                        🔥 {{ $product->roast_profile ?? 'Medium Dark' }}
                    </span>
                </div>

                <h1 class="font-display text-2xl sm:text-3xl font-bold text-[var(--color-text-primary)] mb-2">
                    {{ $product->name }}
                </h1>

                <p class="text-xs sm:text-sm text-[var(--color-text-muted)] leading-relaxed">
                    {{ $product->description }}
                </p>
            </div>

            <!-- Price & Stock Section -->
            <div class="bg-[var(--color-bg-surface)] p-5 sm:p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase text-[var(--color-text-muted)] block font-mono">Harga Varian Ini</span>
                    <span class="font-display text-2xl sm:text-3xl font-bold text-[var(--color-accent-gold)]">
                        Rp {{ number_format($selectedVariant ? $selectedVariant->price : $product->base_price, 0, ',', '.') }}
                    </span>
                </div>

                <div>
                    @if($selectedVariant && $selectedVariant->stock > 0)
                        <span class="px-3 py-1.5 text-xs font-semibold uppercase bg-emerald-950/60 border border-emerald-700/50 text-emerald-400 rounded-full flex items-center space-x-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Stok Ready: {{ $selectedVariant->stock }} pcs</span>
                        </span>
                    @else
                        <span class="px-3 py-1.5 text-xs font-semibold uppercase bg-rose-950/60 border border-rose-700/50 text-rose-400 rounded-full">
                            Stok Varian Habis
                        </span>
                    @endif
                </div>
            </div>

            <!-- Variant Selector -->
            <div class="space-y-4">
                <!-- Grind Type Selection -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-2 font-mono">
                        Pilih Bentuk Kopi
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                        @foreach(['whole_bean' => 'Biji Utuh', 'fine' => 'Bubuk Halus', 'medium' => 'Bubuk Sedang', 'coarse' => 'Bubuk Kasar'] as $key => $label)
                            <button wire:click="selectGrindType('{{ $key }}')" type="button" class="px-3 py-2.5 sm:px-4 sm:py-3 text-xs font-medium rounded-[var(--radius-sm)] border text-center transition-all {{ $selectedGrindType === $key ? 'bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] border-[var(--color-accent-gold)] font-bold shadow-md' : 'bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)]/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Weight Selection -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] mb-2 font-mono">
                        Pilih Ukuran Kemasan
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3">
                        @foreach([200 => '200 gram', 500 => '500 gram', 1000 => '1000 gram (1 kg)'] as $weight => $label)
                            <button wire:click="selectWeight({{ $weight }})" type="button" class="px-3 py-2.5 sm:px-4 sm:py-3 text-xs font-medium rounded-[var(--radius-sm)] border text-center transition-all {{ $selectedWeightG === $weight ? 'bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] border-[var(--color-accent-gold)] font-bold shadow-md' : 'bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)]/50' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quantity & Actions -->
            <div class="space-y-4 pt-4 border-t border-[var(--color-coffee-brown)]">
                <div class="flex items-center space-x-4">
                    <label class="text-xs font-semibold uppercase tracking-wider text-[var(--color-text-muted)] font-mono">Jumlah:</label>
                    <div class="flex items-center border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] bg-[var(--color-bg-surface)]">
                        <button wire:click="decrementQuantity" type="button" class="px-3 py-2 text-sm font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors">-</button>
                        <span class="px-4 py-2 text-sm font-mono font-bold text-[var(--color-text-primary)]">{{ $quantity }}</span>
                        <button wire:click="incrementQuantity" type="button" class="px-3 py-2 text-sm font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)] transition-colors">+</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <button wire:click="addToCart" type="button" @if(!$selectedVariant || $selectedVariant->stock === 0) disabled @endif class="w-full py-3.5 px-6 font-semibold text-xs sm:text-sm rounded-[var(--radius-sm)] border border-[var(--color-accent-gold)] text-[var(--color-accent-gold)] hover:bg-[var(--color-accent-gold)] hover:text-[var(--color-bg-base)] transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        + Tambah ke Keranjang
                    </button>
                    <button wire:click="buyNow" type="button" @if(!$selectedVariant || $selectedVariant->stock === 0) disabled @endif class="w-full py-3.5 px-6 font-bold text-xs sm:text-sm rounded-[var(--radius-sm)] bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                        Beli Sekarang
                    </button>
                </div>
            </div>

            <!-- Accordion Details -->
            <div class="border-t border-[var(--color-coffee-brown)] pt-6 space-y-4 text-sm text-[var(--color-text-muted)]">
                <details class="group bg-[var(--color-bg-surface)] p-4 rounded-[var(--radius-sm)] border border-[var(--color-coffee-brown)]">
                    <summary class="font-semibold text-[var(--color-text-primary)] cursor-pointer flex items-center justify-between">
                        <span>☕ Panduan Seduh Rekomendasi</span>
                        <span class="group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="mt-3 text-xs leading-relaxed space-y-2 pt-2 border-t border-[var(--color-coffee-brown)]/40 font-mono">
                        <p><strong>Bubuk Sedang (Medium Grind):</strong> 15g kopi + 225ml air panas (90°C–92°C). Metode V60, Drip, atau Tubruk Saring.</p>
                        <p><strong>Tubruk Tradisional:</strong> 12g kopi + 200ml air panas 93°C, aduk dan tunggu 4 menit.</p>
                    </div>
                </details>

                <details class="group bg-[var(--color-bg-surface)] p-4 rounded-[var(--radius-sm)] border border-[var(--color-coffee-brown)]">
                    <summary class="font-semibold text-[var(--color-text-primary)] cursor-pointer flex items-center justify-between">
                        <span>📦 Pengiriman & Jaminan Kesegaran</span>
                        <span class="group-open:rotate-180 transition-transform">▼</span>
                    </summary>
                    <div class="mt-3 text-xs leading-relaxed space-y-2 pt-2 border-t border-[var(--color-coffee-brown)]/40 font-mono">
                        <p>Kopi disangrai maksimal 7 hari sebelum pengiriman. Dikemas dalam kemasan ziplock berbahan foil tebal dengan one-way valve untuk menjaga aroma & kesegaran kopi.</p>
                    </div>
                </details>
            </div>
        </div>
    </div>
</div>
