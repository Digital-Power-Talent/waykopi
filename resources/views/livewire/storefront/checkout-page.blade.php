<div class="py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full relative">
    <h1 class="font-display text-3xl font-bold text-[var(--color-text-primary)] mb-8 transition-colors duration-300">
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
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 transition-colors duration-300">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                        1. Informasi Pemesan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Nama Lengkap *</label>
                            <input type="text" wire:model="name" placeholder="Eko Prasetyo" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300">
                            @error('name') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Email (Opsional)</label>
                            <input type="email" wire:model="email" placeholder="eko@example.com (opsional)" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300">
                            @error('email') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Nomor HP / WhatsApp *</label>
                        <input type="text" wire:model="phone" placeholder="081234567890" class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300">
                        @error('phone') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 transition-colors duration-300">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                        2. Alamat Pengiriman
                    </h3>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Cari Kecamatan / Kota *</label>
                        <div class="relative" @click.outside="$wire.clearAreaResults()">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="areaSearch" 
                                   placeholder="Ketik nama kecamatan atau kota (misal: Bogor, Kebayoran)..." 
                                   class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300">
                            
                            <div wire:loading wire:target="areaSearch" class="absolute right-3 top-3 text-xs font-mono text-[var(--color-accent-gold)] animate-pulse">
                                Memuat...
                            </div>

                            @if(!empty($areaResults))
                                <div class="absolute z-30 left-0 right-0 mt-1 bg-[var(--color-bg-surface)] border border-[var(--color-accent-gold)] rounded-[var(--radius-sm)] shadow-2xl max-h-60 overflow-y-auto divide-y divide-black/10 dark:divide-white/10">
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
                        <textarea wire:model="address" rows="3" placeholder="Jl. Pajajaran No. 45, RT 02/RW 05, Kel. Baranangsiang..." class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300"></textarea>
                        @error('address') <span class="text-xs text-[var(--color-error)] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--color-text-muted)] uppercase tracking-wider mb-2 font-mono">Catatan untuk Penjual (Opsional)</label>
                        <input type="text" wire:model="notes" placeholder="Gilingan ekstra halus untuk espresso..." class="w-full px-4 py-2.5 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-sm text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300">
                    </div>
                </div>

                <!-- Kurir Pengiriman -->
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 transition-colors duration-300">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                        3. Opsi Pengiriman (Biteship)
                    </h3>

                    @if(empty($destinationAreaId))
                        <p class="text-xs text-[var(--color-text-muted)] font-mono">
                            Silakan pilih kecamatan/area pengiriman di atas untuk melihat pilihan kurir & estimasi ongkir.
                        </p>
                    @elseif(!empty($shippingRates))
                        <div class="space-y-3">
                            @foreach($shippingRates as $idx => $rate)
                                <div wire:click="selectCourier({{ $idx }})" class="p-4 rounded-[var(--radius-sm)] border cursor-pointer transition-all flex items-center justify-between {{ $selectedCourierIndex === $idx ? 'bg-[var(--color-accent-gold)]/10 border-[var(--color-accent-gold)]' : 'bg-[var(--color-bg-base)] border-black/10 dark:border-white/10 hover:border-[var(--color-accent-gold)]/50' }}">
                                    <div class="flex items-center space-x-3">
                                        <input type="radio" name="courier_option" {{ $selectedCourierIndex === $idx ? 'checked' : '' }} class="text-[var(--color-accent-gold)] focus:ring-0">
                                        <div>
                                            <span class="font-bold text-sm text-[var(--color-text-primary)] block transition-colors duration-300">
                                                {{ $rate['courier_name'] }} — {{ $rate['courier_service_name'] }}
                                            </span>
                                            <span class="text-xs font-mono text-[var(--color-text-muted)] transition-colors duration-300">
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
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-4 transition-colors duration-300">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                        4. Metode Pembayaran
                    </h3>

                    <div class="space-y-3">
                        <label class="p-4 rounded-[var(--radius-sm)] border cursor-pointer transition-all flex items-center justify-between {{ $paymentMethod === 'bank_transfer' ? 'bg-[var(--color-accent-gold)]/10 border-[var(--color-accent-gold)]' : 'bg-[var(--color-bg-base)] border-black/10 dark:border-white/10 hover:border-[var(--color-accent-gold)]/50' }}">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model.live="paymentMethod" value="bank_transfer" class="text-[var(--color-accent-gold)] focus:ring-0">
                                <div>
                                    <span class="font-bold text-sm text-[var(--color-text-primary)] block transition-colors duration-300">
                                        🏦 Transfer Bank Langsung
                                    </span>
                                    <span class="text-xs font-mono text-[var(--color-text-muted)] transition-colors duration-300">
                                        Transfer ke Rekening Resmi Mandiri / BRI & Konfirmasi via WhatsApp
                                    </span>
                                </div>
                            </div>
                        </label>

                        <label class="p-4 rounded-[var(--radius-sm)] border cursor-pointer transition-all flex items-center justify-between {{ $paymentMethod === 'cod' ? 'bg-[var(--color-accent-gold)]/10 border-[var(--color-accent-gold)]' : 'bg-[var(--color-bg-base)] border-black/10 dark:border-white/10 hover:border-[var(--color-accent-gold)]/50' }}">
                            <div class="flex items-center space-x-3">
                                <input type="radio" wire:model.live="paymentMethod" value="cod" class="text-[var(--color-accent-gold)] focus:ring-0">
                                <div>
                                    <span class="font-bold text-sm text-[var(--color-text-primary)] flex items-center gap-2 transition-colors duration-300">
                                        💵 COD (Bayar di Tempat)
                                        <span class="px-2 py-0.5 text-[10px] uppercase font-bold bg-amber-500 text-black rounded">Populer</span>
                                    </span>
                                    <span class="text-xs font-mono text-[var(--color-text-muted)] transition-colors duration-300">
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
                <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 shadow-sm space-y-6 sticky top-6 transition-colors duration-300">
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-black/10 dark:border-white/10 pb-3 transition-colors duration-300">
                        Ringkasan Belanja
                    </h3>

                    <!-- Cart Item Highlights -->
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1 divide-y divide-black/10 dark:divide-white/10 transition-colors duration-300">
                        @foreach($cartItems as $item)
                            <div class="pt-2 flex items-center justify-between text-xs font-mono">
                                <div>
                                    <span class="font-bold text-[var(--color-text-primary)] block transition-colors duration-300">{{ $item['variant']->product->name }}</span>
                                    <span class="text-[var(--color-text-muted)] transition-colors duration-300">{{ $item['quantity'] }}x ({{ $item['variant']->grind_type_label }}, {{ $item['variant']->weight_grams }}g)</span>
                                </div>
                                <span class="font-bold text-[var(--color-accent-gold)]">
                                    Rp {{ number_format($item['total_price'], 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Automatic Shipping Subsidy Banner -->
                    <div class="p-4 bg-[var(--color-bg-base)] border border-[var(--color-accent-gold)]/30 rounded-[var(--radius-sm)] space-y-2.5 shadow-sm transition-colors duration-300">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-[var(--color-accent-gold)] uppercase tracking-wider font-mono flex items-center gap-1.5">
                                <span>🚚</span>
                                <span>Subsidi & Promo Ongkir</span>
                            </span>
                            <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-amber-300 bg-amber-950/80 border border-amber-600/40 rounded font-mono">
                                {{ $itemCount }} Bungkus
                            </span>
                        </div>

                        @if(!empty($destinationAreaId))
                            @if($discountInfo['is_free_shipping'])
                                <div class="p-3 bg-emerald-950/60 border border-emerald-500/50 rounded flex items-start gap-2.5">
                                    <span class="text-lg leading-none">🎉</span>
                                    <div>
                                        <span class="font-bold text-xs text-emerald-400 font-mono block">
                                            GRATIS ONGKIR OTOMATIS AKTIF
                                        </span>
                                        <span class="text-[10px] text-emerald-300/90 block leading-tight mt-0.5">
                                            {{ $discountInfo['promo_message'] }}
                                        </span>
                                    </div>
                                </div>
                            @elseif($discountInfo['discount_amount'] > 0)
                                <div class="p-3 bg-emerald-950/40 border border-emerald-600/40 rounded flex items-start gap-2.5">
                                    <span class="text-base leading-none">⚡</span>
                                    <div>
                                        <span class="font-bold text-xs text-emerald-400 font-mono block">
                                            {{ $discountInfo['rule_label'] }}
                                        </span>
                                        <span class="text-[10px] text-emerald-300/80 block leading-tight mt-0.5">
                                            {{ $discountInfo['promo_message'] }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="p-2.5 bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 rounded text-[11px] font-mono text-[var(--color-text-muted)] space-y-1 transition-colors duration-300">
                                    <div class="flex items-center justify-between text-[var(--color-text-primary)] font-bold text-[10px]">
                                        <span>Wilayah: {{ $discountInfo['group_label'] }}</span>
                                        <span class="text-amber-400">{{ $discountInfo['rule_label'] }}</span>
                                    </div>
                                    <p class="text-[10px] leading-tight text-amber-200/80">
                                        💡 {{ $discountInfo['promo_message'] }}
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="p-2.5 bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 rounded text-[10px] font-mono text-[var(--color-text-muted)] space-y-1 leading-relaxed transition-colors duration-300">
                                <span class="text-amber-400 font-bold block">✨ Promo Subsidi Otomatis Tersedia:</span>
                                <div>• <strong>Jabar, Jabodetabek & Banten:</strong> 1 bks (-5rb), 2 bks (-10rb), ≥3 bks (<strong>Gratis Ongkir</strong>)</div>
                                <div>• <strong>Jateng, DIY & Jatim:</strong> 2 bks (-5rb), 3 bks (-10rb), ≥4 bks (<strong>Gratis Ongkir</strong>)</div>
                                <span class="text-[9px] text-[var(--color-text-muted)] block mt-1 italic">*Diskon otomatis terpasang saat area pengiriman dipilih.</span>
                            </div>
                        @endif
                    </div>

                    <!-- Voucher Input Section -->
                    <div class="p-4 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] space-y-3 transition-colors duration-300">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-semibold text-[var(--color-text-primary)] uppercase tracking-wider font-mono flex items-center gap-1.5 transition-colors duration-300">
                                <span>🎟️</span>
                                <span>Voucher Diskon Tambahan</span>
                            </label>
                            @if($appliedVoucherCode)
                                <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-emerald-400 bg-emerald-950 border border-emerald-700/50 rounded">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        @if($appliedVoucherCode)
                            <div class="p-3 bg-emerald-950/40 border border-emerald-700/50 rounded flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-xs text-emerald-400 font-mono block">
                                        ✓ {{ $appliedVoucherCode }}
                                    </span>
                                    <span class="text-[10px] text-emerald-300/80 block">
                                        Voucher diskon aktif
                                    </span>
                                </div>
                                <button type="button" wire:click="removeVoucher" class="px-2 py-1 text-[10px] text-red-400 hover:text-red-300 border border-red-500/30 rounded hover:bg-red-500/10 font-mono transition-colors">
                                    ✕ Hapus
                                </button>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       wire:model="voucherInput" 
                                       placeholder="Punya kode voucher? Masukkan di sini..." 
                                       class="w-full px-3 py-2 bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 rounded-[var(--radius-sm)] text-xs text-[var(--color-text-primary)] uppercase font-mono tracking-wider focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300"
                                       @keydown.enter.prevent="$wire.applyVoucher()">
                                <button type="button" 
                                        wire:click="applyVoucher" 
                                        class="px-4 py-2 bg-[var(--color-accent-gold)] text-black font-bold text-xs rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors whitespace-nowrap">
                                    Pakai
                                </button>
                            </div>
                        @endif

                        @if($voucherMessage)
                            <div class="text-xs text-emerald-400 font-mono flex items-center gap-1">
                                <span>✓</span>
                                <span>{{ $voucherMessage }}</span>
                            </div>
                        @endif

                        @if($voucherError)
                            <div class="text-xs text-[var(--color-error)] font-mono flex items-center gap-1">
                                <span>⚠️</span>
                                <span>{{ $voucherError }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Cost Breakdown -->
                    <div class="space-y-3 pt-4 border-t border-black/10 dark:border-white/10 text-sm font-mono transition-colors duration-300">
                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Subtotal Kopi ({{ $itemCount }} bks)</span>
                            <span class="text-[var(--color-text-primary)] font-bold transition-colors duration-300">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-[var(--color-text-muted)] transition-colors duration-300">
                            <span>Ongkos Kirim</span>
                            <span class="text-[var(--color-accent-gold)] font-bold">
                                @if($selectedShippingFee > 0)
                                    Rp {{ number_format($selectedShippingFee, 0, ',', '.') }}
                                @else
                                    Pilih Kurir
                                @endif
                            </span>
                        </div>

                        @if($discountAmount > 0)
                            <div class="flex items-center justify-between text-emerald-400 font-bold">
                                <span>
                                    @if($discountInfo['is_free_shipping'])
                                        Potongan Ongkir (Gratis Ongkir)
                                    @else
                                        Potongan Subsidi Ongkir
                                    @endif
                                    @if($appliedVoucherCode)
                                        + Voucher
                                    @endif
                                </span>
                                <span>- Rp {{ number_format($discountAmount, 0, ',', '.') }}</span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-base pt-3 border-t border-black/10 dark:border-white/10 transition-colors duration-300">
                            <span class="font-bold text-[var(--color-text-primary)] transition-colors duration-300">Total Bayar</span>
                            <span class="font-display text-2xl font-bold text-[var(--color-accent-gold)]">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        @if($paymentMethod === 'bank_transfer')
                            <div class="p-3 bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-[var(--radius-sm)] text-[11px] font-mono text-[var(--color-text-muted)] flex items-start gap-2 transition-colors duration-300">
                                <span class="text-sm leading-none">💡</span>
                                <span>Kode unik 3 digit verifikasi (Rp 100 - 999) akan ditambahkan otomatis di tagihan pembayaran transfer agar pesanan Anda cepat diverifikasi.</span>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="w-full py-4 px-6 font-bold text-sm bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-sm">
                        Buat Pesanan & Lanjutkan &rarr;
                    </button>
                </div>
            </div>
        </div>
    </form>

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