<div class="py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full">
    <h1 class="font-display text-3xl font-bold text-[var(--color-text-primary)] mb-8">
        Checkout Pesanan
    </h1>

    @if($errorMessage)
        <div class="p-4 mb-6 rounded-[var(--radius-sm)] bg-[var(--color-error)]/10 border border-[var(--color-error)] text-[var(--color-error)] text-sm">
            {{ $errorMessage }}
        </div>
    @endif

    <form wire:submit="processCheckout">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left: Customer Info & Shipping (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Data Pemesan -->
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                        1. Informasi Pemesan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Nama Lengkap *</label>
                            <input type="text" wire:model="name" placeholder="Eko Prasetyo" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                            @error('name') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Email (Opsional)</label>
                            <input type="email" wire:model="email" placeholder="eko@example.com (opsional)" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                            @error('email') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Nomor HP / WhatsApp *</label>
                        <input type="text" wire:model="phone" placeholder="081234567890" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('phone') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                        2. Alamat Pengiriman
                    </h3>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Cari Kecamatan / Kota *</label>
                        <div class="relative" @click.outside="$wire.clearAreaResults()">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="areaSearch" 
                                   placeholder="Ketik nama kecamatan atau kota (misal: Bogor, Kebayoran)..." 
                                   class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                            
                            <div wire:loading wire:target="areaSearch" class="absolute right-3 top-3 text-xs font-mono text-[var(--color-accent-gold)] animate-pulse">
                                Memuat...
                            </div>

                            @if(!empty($areaResults))
                                <div class="absolute z-30 left-0 right-0 mt-1 bg-[#1C1712] border border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] shadow-2xl max-h-60 overflow-y-auto divide-y divide-[var(--color-coffee-brown)]/40">
                                    @foreach($areaResults as $index => $area)
                                        <button type="button" 
                                                wire:click="selectArea({{ $index }})" 
                                                class="w-full px-4 py-3 text-left text-xs hover:bg-[var(--color-accent-gold)]/20 text-[var(--color-text-primary)] transition-colors flex items-center justify-between">
                                            <span>📍 {{ $area['name'] }}</span>
                                            <span class="text-[10px] text-[var(--color-accent-gold)] uppercase font-mono font-bold">Pilih &rarr;</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('destinationAreaId') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    @if($areaName)
                        <div class="p-3 bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-[var(--radius-sm)] text-xs font-mono text-[var(--color-accent-gold)] flex items-center justify-between">
                            <span>Area Terpilih: {{ $areaName }}</span>
                            <span class="font-bold">✓</span>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Alamat Lengkap (Jalan, RT/RW, No. Rumah) *</label>
                        <textarea wire:model="address" rows="3" placeholder="Jl. Pajajaran No. 45, RT 02/RW 05, Kel. Baranangsiang..." class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]"></textarea>
                        @error('address') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Catatan untuk Penjual (Opsional)</label>
                        <input type="text" wire:model="notes" placeholder="Gilingan ekstra halus untuk espresso..." class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                    </div>
                </div>

                <!-- Kurir Pengiriman -->
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                        3. Opsi Pengiriman (Biteship)
                    </h3>

                    @if(empty($destinationAreaId))
                        <p class="text-xs text-[var(--color-text-muted)] font-mono">
                            Silakan pilih kecamatan/area pengiriman di atas untuk melihat pilihan kurir & estimasi ongkir.
                        </p>
                    @elseif(!empty($shippingRates))
                        <div class="space-y-3">
                            @foreach($shippingRates as $idx => $rate)
                                <div wire:click="selectCourier({{ $idx }})" class="p-4 rounded-[var(--radius-sm)] border cursor-pointer transition-all flex items-center justify-between {{ $selectedCourierIndex === $idx ? 'bg-[var(--color-accent-gold)]/10 border-[var(--color-accent-gold)]' : 'bg-[var(--color-bg-base)] border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)]/50' }}">
                                    <div class="flex items-center space-x-3">
                                        <input type="radio" name="courier_option" {{ $selectedCourierIndex === $idx ? 'checked' : '' }} class="text-[var(--color-accent-gold)] focus:ring-0">
                                        <div>
                                            <span class="font-bold text-sm text-[var(--color-text-primary)] block">
                                                {{ $rate['courier_name'] }} — {{ $rate['courier_service_name'] }}
                                            </span>
                                            <span class="text-xs font-mono text-[var(--color-text-muted)]">
                                                Estimasi: {{ $rate['duration'] }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="text-right font-mono font-bold text-sm text-[var(--color-accent-gold)]">
                                        Rp {{ number_format($rate['price'], 0, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-amber-500 font-mono">
                            Sedang memuat opsi pengiriman dari Biteship...
                        </p>
                    @endif
                </div>

                <!-- Metode Pembayaran -->
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                        4. Metode Pembayaran
                    </h3>

                    <div class="space-y-3">
                        <label class="p-4 rounded-[var(--radius-sm)] border cursor-pointer transition-all flex items-center justify-between {{ $paymentMethod === 'bank_transfer' ? 'bg-[var(--color-accent-gold)]/10 border-[var(--color-accent-gold)]' : 'bg-[var(--color-bg-base)] border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)]/50' }}">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model.live="paymentMethod" value="bank_transfer" class="text-[var(--color-accent-gold)] focus:ring-0">
                                <div>
                                    <span class="font-bold text-sm text-[var(--color-text-primary)] block">
                                        🏦 Transfer Bank Langsung
                                    </span>
                                    <span class="text-xs font-mono text-[var(--color-text-muted)]">
                                        Transfer ke Rekening Resmi Mandiri / BRI & Konfirmasi via WhatsApp
                                    </span>
                                </div>
                            </div>
                        </label>

                        <label class="p-4 rounded-[var(--radius-sm)] border cursor-pointer transition-all flex items-center justify-between {{ $paymentMethod === 'cod' ? 'bg-[var(--color-accent-gold)]/10 border-[var(--color-accent-gold)]' : 'bg-[var(--color-accent-gold)]/20' }}">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model.live="paymentMethod" value="cod" class="text-[var(--color-accent-gold)] focus:ring-0">
                                <div>
                                    <span class="font-bold text-sm text-[var(--color-text-primary)] flex items-center gap-2">
                                        💵 COD (Bayar di Tempat)
                                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold bg-amber-500 text-black rounded">Populer</span>
                                    </span>
                                    <span class="text-xs font-mono text-[var(--color-text-muted)]">
                                        Bayar tunai secara aman langsung ke kurir saat paket biji kopi Anda tiba
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('paymentMethod') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Right: Order Summary Sidebar (5 cols) -->
            <div class="lg:col-span-5">
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-xl space-y-6 sticky top-6">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                        Ringkasan Belanja
                    </h3>

                    <!-- Cart Item Highlights -->
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1 divide-y divide-[var(--color-coffee-brown)]/40">
                        @foreach($cartItems as $item)
                            <div class="pt-2 flex items-center justify-between text-xs font-mono">
                                <div>
                                    <span class="font-bold text-[var(--color-text-primary)] block">{{ $item['variant']->product->name }}</span>
                                    <span class="text-[var(--color-text-muted)]">{{ $item['quantity'] }}x ({{ $item['variant']->grind_type_label }}, {{ $item['variant']->weight_grams }}g)</span>
                                </div>
                                <span class="font-bold text-[var(--color-accent-gold)]">
                                    Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="space-y-3 pt-4 border-t border-[var(--color-coffee-brown)] text-sm font-mono">
                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Subtotal Kopi</span>
                            <span class="text-[var(--color-text-primary)] font-bold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-[var(--color-text-muted)]">
                            <span>Ongkos Kirim</span>
                            <span class="text-[var(--color-accent-gold)] font-bold">
                                @if($selectedShippingFee > 0)
                                    Rp {{ number_format($selectedShippingFee, 0, ',', '.') }}
                                @else
                                    Pilih Kurir
                                @endif
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-base pt-3 border-t border-[var(--color-coffee-brown)]">
                            <span class="font-bold text-[var(--color-text-primary)]">Total Bayar</span>
                            <span class="font-display text-2xl font-bold text-[var(--color-accent-gold)]">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($paymentMethod === 'bank_transfer')
                            <div class="p-3 bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-[var(--radius-sm)] text-[11px] font-mono text-[var(--color-text-muted)] flex items-start gap-2">
                                <span class="text-sm leading-none">💡</span>
                                <span>Kode unik 3 digit verifikasi (Rp 100 - 999) akan ditambahkan otomatis di tagihan pembayaran transfer agar pesanan Anda cepat diverifikasi.</span>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="w-full py-4 px-6 font-bold text-sm bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-lg">
                        Buat Pesanan & Lanjutkan &rarr;
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
