<div class="py-8 max-w-7xl mx-auto space-y-8">
    <!-- Header Navigation -->
    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--color-coffee-brown)] shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded">
                Admin Panel
            </span>
            <h1 class="font-display text-2xl font-bold text-[var(--color-text-primary)] mt-1">
                Kelola Produk & Stok Kopi
            </h1>
        </div>

        <div class="flex items-center space-x-3 font-mono text-xs">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                &larr; Kembali ke Dashboard
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                📦 Kelola Pesanan
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
    <div class="bg-[var(--color-bg-surface)] p-5 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] flex flex-col md:flex-row gap-4 items-center justify-between font-mono text-xs">
        <div class="w-full md:w-80">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama produk kopi..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
        </div>

        <button wire:click="openProductModal" class="px-5 py-2.5 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
            + Tambah Produk Kopi Baru
        </button>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($products as $product)
            @php
                $img = $product->primaryImage?->url ?? '/images/products/waykopi_robusta.png';
                $minPrice = $product->variants->min('price') ?? 0;
                $totalStock = $product->variants->sum('stock');
            @endphp
            <div class="bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-md)] overflow-hidden flex flex-col hover:border-[var(--color-accent-gold)] transition-all">
                <div class="h-48 bg-[var(--color-bg-base)] overflow-hidden relative">
                    <img src="{{ $img }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    <div class="absolute top-3 right-3 font-mono">
                        @if($totalStock < 10)
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-amber-600 text-white rounded">Stok Rendah ({{ $totalStock }})</span>
                        @else
                            <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-emerald-950 border border-emerald-500/40 text-emerald-400 rounded">Stok {{ $totalStock }}</span>
                        @endif
                    </div>
                </div>

                <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-[10px] font-mono text-[var(--color-text-muted)] mb-1">
                            <span>{{ $product->origin }}</span>
                            <span>{{ $product->roast_profile }}</span>
                        </div>
                        <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)]">
                            {{ $product->name }}
                        </h3>
                        <p class="text-xs text-[var(--color-text-muted)] line-clamp-2 mt-1">
                            {{ $product->description }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-between font-mono text-xs">
                        <div>
                            <span class="text-[10px] text-[var(--color-text-muted)] block">Harga Mulai</span>
                            <span class="font-bold text-[var(--color-accent-gold)] text-sm">Rp {{ number_format($minPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button wire:click="openProductModal({{ $product->id }})" class="px-3 py-1.5 font-bold border border-[var(--color-accent-gold)] text-[var(--color-accent-gold)] rounded hover:bg-[var(--color-accent-gold)] hover:text-[var(--color-bg-base)] transition-colors">
                                Edit
                            </button>
                            <button wire:click="deleteProduct({{ $product->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="px-3 py-1.5 font-bold bg-rose-950/60 border border-rose-800/50 text-rose-300 hover:bg-rose-900 rounded transition-colors">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Product Modal -->
    @if($showProductModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-2xl max-w-lg w-full space-y-4 font-mono text-xs">
                <div class="flex items-center justify-between border-b border-[var(--color-coffee-brown)] pb-3">
                    <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] font-sans">
                        {{ $editingProductId ? 'Edit Produk Kopi' : 'Tambah Produk Kopi Baru' }}
                    </h3>
                    <button wire:click="closeProductModal" class="text-[var(--color-text-muted)] hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="saveProduct" class="space-y-3">
                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Nama Produk Kopi</label>
                        <input type="text" wire:model.live="name" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        @error('name') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Slug URL</label>
                        <input type="text" wire:model="slug" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        @error('slug') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Deskripsi Produk</label>
                        <textarea wire:model="description" rows="3" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded"></textarea>
                        @error('description') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[var(--color-text-muted)] uppercase mb-1">Profil Sangrai</label>
                            <input type="text" wire:model="roast_profile" placeholder="Medium Dark" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        </div>
                        <div>
                            <label class="block text-[var(--color-text-muted)] uppercase mb-1">Asal Daerah (Origin)</label>
                            <input type="text" wire:model="origin" placeholder="Tanggamus, Lampung" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">URL Gambar Produk</label>
                        <input type="text" wire:model="imageUrl" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                    </div>

                    <!-- Main Variant Config -->
                    <div class="pt-3 border-t border-[var(--color-coffee-brown)]/40 space-y-2">
                        <span class="block font-bold text-[var(--color-accent-gold)]">Konfigurasi Varian Kopi Utama:</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[var(--color-text-muted)] text-[10px]">Berat (gram)</label>
                                <input type="number" wire:model="weight_grams" class="w-full px-2 py-1.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                            </div>
                            <div>
                                <label class="block text-[var(--color-text-muted)] text-[10px]">Harga (Rp)</label>
                                <input type="number" wire:model="price" class="w-full px-2 py-1.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                            </div>
                            <div>
                                <label class="block text-[var(--color-text-muted)] text-[10px]">Stok (pcs)</label>
                                <input type="number" wire:model="stock" class="w-full px-2 py-1.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-end space-x-3">
                        <button type="button" wire:click="closeProductModal" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-muted)] rounded">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
