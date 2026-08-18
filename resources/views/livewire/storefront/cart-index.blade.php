<div class="py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full">
    <h1 class="font-display text-3xl font-bold text-[var(--color-text-primary)] mb-8">
        Keranjang Belanja Kamu
    </h1>

    @if($cartItems->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Items Table (8 cols) -->
            <div class="lg:col-span-8 space-y-4">
                <div class="bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] overflow-hidden shadow-lg">
                    <div class="p-4 border-b border-[var(--color-coffee-brown)] flex items-center justify-between font-mono text-xs text-[var(--color-text-muted)]">
                        <span>Detail Item ({{ $itemCount }} Kopi)</span>
                        <button wire:click="clearCart" class="text-[var(--color-error)] hover:underline">
                            Kosongkan Keranjang
                        </button>
                    </div>

                    <div class="divide-y divide-[var(--color-coffee-brown)]/40">
                        @foreach($cartItems as $item)
                            @php
                                $variant = $item['variant'];
                                $product = $variant->product;
                                $image = $product->primaryImage ?? $product->images->first();
                            @endphp
                            <div class="p-4 md:p-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] overflow-hidden flex-shrink-0">
                                        @if($image)
                                            <img src="{{ $image->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs font-mono text-[var(--color-text-muted)]">
                                                Way Kopi
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <h3 class="font-display font-bold text-base text-[var(--color-text-primary)]">
                                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-[var(--color-accent-gold)] transition-colors">
                                                {{ $product->name }}
                                            </a>
                                        </h3>
                                        <div class="flex items-center space-x-2 text-xs font-mono text-[var(--color-text-muted)] mt-1">
                                            <span>{{ $variant->grind_type_label }}</span>
                                            <span>•</span>
                                            <span>{{ $variant->weight_grams }}g</span>
                                        </div>
                                        <div class="text-xs font-mono text-[var(--color-accent-gold)] mt-1">
                                            Rp {{ number_format($item['item_price'], 0, ',', '.') }} / pack
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between md:justify-end space-x-6 pt-2 md:pt-0 border-t md:border-t-0 border-[var(--color-coffee-brown)]/30">
                                    <!-- Quantity Controller -->
                                    <div class="flex items-center border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] bg-[var(--color-bg-base)]">
                                        <button wire:click="updateQuantity({{ $variant->id }}, {{ $item['quantity'] - 1 }})" class="px-2.5 py-1 text-sm font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)]">-</button>
                                        <span class="px-3 py-1 text-xs font-mono font-bold text-[var(--color-text-primary)]">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $variant->id }}, {{ $item['quantity'] + 1 }})" class="px-2.5 py-1 text-sm font-bold text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)]">+</button>
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
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-xl space-y-6">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                        Ringkasan Pesanan
                    </h3>

                    <div class="space-y-3 text-sm font-mono">
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Total Item</span>
                            <span class="text-[var(--color-text-primary)] font-bold">{{ $itemCount }} Kopi</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Total Berat</span>
                            <span class="text-[var(--color-text-primary)] font-bold">{{ number_format($totalWeightGrams / 1000, 2) }} kg ({{ $totalWeightGrams }}g)</span>
                        </div>
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Subtotal Kopi</span>
                            <span class="text-[var(--color-text-primary)] font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-xs text-[var(--color-text-muted)] pt-2 border-t border-[var(--color-coffee-brown)]/40">
                            <span>Ongkos Kirim</span>
                            <span>Dihitung saat Checkout</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[var(--color-coffee-brown)]">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-xs uppercase text-[var(--color-text-muted)] font-mono">Total Sementara</span>
                            <span class="font-display text-2xl font-bold text-[var(--color-accent-gold)]">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>

                        <a href="{{ route('checkout.index') }}" class="block w-full py-4 px-6 text-center font-bold text-sm bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-lg">
                            Lanjut ke Checkout &rarr;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-16 bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
            <svg class="w-16 h-16 text-[var(--color-text-muted)] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <h3 class="font-display text-xl font-bold text-[var(--color-text-primary)] mb-2">Keranjang Belanja Masih Kosong</h3>
            <p class="text-xs text-[var(--color-text-muted)] max-w-md mx-auto mb-6">
                Kamu belum menambahkan varian kopi Robusta Lampung ke dalam keranjang. Yuk jelajahi katalog panen terbaru kami!
            </p>
            <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 text-xs font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                Lihat Katalog Kopi &rarr;
            </a>
        </div>
    @endif
</div>
