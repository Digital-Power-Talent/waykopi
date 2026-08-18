<div class="py-8 max-w-5xl mx-auto space-y-8">
    <!-- Account Header Banner -->
    <div class="bg-[var(--color-bg-surface)] p-6 md:p-8 rounded-[var(--radius-lg)] border border-[var(--color-coffee-brown)] shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-full bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 flex items-center justify-center text-2xl font-bold text-[var(--color-accent-gold)] font-mono">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h1 class="font-display text-2xl font-bold text-[var(--color-text-primary)]">
                    {{ Auth::user()->name }}
                </h1>
                <p class="text-xs font-mono text-[var(--color-text-muted)]">
                    {{ Auth::user()->email }} • Terdaftar {{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : 'Member' }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="px-4 py-2 text-xs font-bold border border-red-500/40 text-red-400 rounded-[var(--radius-sm)] hover:bg-red-500/10 transition-colors">
                Keluar (Logout)
            </button>
        </form>
    </div>

    <!-- Global Alert Messages -->
    @if($statusMessage)
        <div class="p-4 bg-emerald-950/60 border border-emerald-500/40 rounded-[var(--radius-sm)] text-xs text-emerald-400 font-mono">
            ✓ {{ $statusMessage }}
        </div>
    @endif

    <!-- Tabs Bar -->
    <div class="flex border-b border-[var(--color-coffee-brown)] space-x-2">
        <button wire:click="switchTab('orders')" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'orders' ? 'border-[var(--color-accent-gold)] text-[var(--color-accent-gold)]' : 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)]' }}">
            📦 Riwayat Pesanan
        </button>
        <button wire:click="switchTab('addresses')" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'addresses' ? 'border-[var(--color-accent-gold)] text-[var(--color-accent-gold)]' : 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)]' }}">
            📍 Alamat Pengiriman
        </button>
        <button wire:click="switchTab('profile')" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'profile' ? 'border-[var(--color-accent-gold)] text-[var(--color-accent-gold)]' : 'border-transparent text-[var(--color-text-muted)] hover:text-[var(--color-text-primary)]' }}">
            ⚙️ Pengaturan Akun
        </button>
    </div>

    <!-- TAB 1: Riwayat Pesanan -->
    @if($activeTab === 'orders')
        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-6">
            <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-3">
                Daftar Pesanan Kopi Kamu
            </h3>

            @if($orders->count() > 0)
                <div class="space-y-4 font-mono text-xs">
                    @foreach($orders as $order)
                        <div class="p-5 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)]/60 rounded-[var(--radius-sm)] space-y-4">
                            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--color-coffee-brown)]/30 pb-3">
                                <div>
                                    <span class="text-[var(--color-text-muted)] text-[10px] uppercase block">Nomor Pesanan</span>
                                    <strong class="text-[var(--color-accent-gold)] text-sm">{{ $order->order_number }}</strong>
                                    <span class="text-[var(--color-text-muted)] text-[10px] ml-2">({{ $order->created_at->format('d M Y, H:i') }})</span>
                                </div>

                                <div>
                                    @if($order->status === 'paid')
                                        <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-emerald-400 bg-emerald-950 border border-emerald-700/50 rounded-full">
                                            Lunas
                                        </span>
                                    @elseif($order->status === 'pending_payment')
                                        <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-amber-400 bg-amber-950 border border-amber-700/50 rounded-full">
                                            Menunggu Pembayaran
                                        </span>
                                    @elseif($order->status === 'shipped')
                                        <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-blue-400 bg-blue-950 border border-blue-700/50 rounded-full">
                                            Dikirim
                                        </span>
                                    @elseif($order->status === 'expired' || $order->status === 'cancelled')
                                        <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-red-400 bg-red-950 border border-red-700/50 rounded-full">
                                            Batal / Expired
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-gray-300 bg-gray-900 border border-gray-700 rounded-full">
                                            {{ strtoupper($order->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Items List -->
                            <div class="space-y-2">
                                @foreach($order->items as $item)
                                    <div class="flex items-center justify-between">
                                        <span class="text-[var(--color-text-primary)]">{{ $item->quantity }}x {{ $item->product_name }} ({{ $item->variant_label }})</span>
                                        <span class="text-[var(--color-text-muted)]">Rp {{ number_format($item->price_at_purchase * $item->quantity, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pt-3 border-t border-[var(--color-coffee-brown)]/30 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-[var(--color-text-muted)]">Total Tagihan: </span>
                                    <strong class="text-[var(--color-accent-gold)] text-sm">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                </div>

                                <div>
                                    @if($order->status === 'pending_payment')
                                        <a href="{{ route('checkout.payment', ['orderNumber' => $order->order_number]) }}" class="px-3 py-1.5 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                                            Bayar Sekarang &rarr;
                                        </a>
                                    @else
                                        <a href="{{ route('checkout.success', ['orderNumber' => $order->order_number]) }}" class="px-3 py-1.5 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] hover:border-[var(--color-accent-gold)] transition-colors">
                                            Lihat Detail &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-12 text-[var(--color-text-muted)] font-mono text-xs space-y-3">
                    <p>Kamu belum pernah membuat pesanan kopi.</p>
                    <a href="{{ route('products.index') }}" class="inline-block px-4 py-2 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)]">
                        Belanja Kopi Sekarang &rarr;
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- TAB 2: Daftar Alamat -->
    @if($activeTab === 'addresses')
        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-6">
            <div class="flex items-center justify-between border-b border-[var(--color-coffee-brown)] pb-3">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)]">
                    Alamat Pengiriman Saya
                </h3>
                <button wire:click="openAddressModal" class="px-4 py-2 text-xs font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                    + Tambah Alamat Baru
                </button>
            </div>

            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 font-mono text-xs">
                    @foreach($addresses as $addr)
                        <div class="p-4 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] space-y-2 relative">
                            @if($addr->is_default)
                                <span class="px-2 py-0.5 text-[9px] uppercase font-bold text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded">
                                    Alamat Utama ({{ $addr->label }})
                                </span>
                            @else
                                <span class="px-2 py-0.5 text-[9px] uppercase text-[var(--color-text-muted)] border border-[var(--color-coffee-brown)] rounded">
                                    {{ $addr->label }}
                                </span>
                            @endif

                            <h4 class="font-bold text-[var(--color-text-primary)] text-sm">{{ $addr->recipient_name }}</h4>
                            <p class="text-[var(--color-text-muted)]">{{ $addr->phone }}</p>
                            <p class="text-[var(--color-text-primary)]">{{ $addr->full_address }}</p>
                            <p class="text-[var(--color-text-muted)]">{{ $addr->district }}, {{ $addr->city }}, {{ $addr->province }} ({{ $addr->postal_code }})</p>

                            <div class="pt-2 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-end space-x-3 text-[11px]">
                                <button wire:click="openAddressModal({{ $addr->id }})" class="text-[var(--color-accent-gold)] hover:underline">Edit</button>
                                <button wire:click="deleteAddress({{ $addr->id }})" class="text-red-400 hover:underline" onclick="return confirm('Hapus alamat ini?')">Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-[var(--color-text-muted)] font-mono text-center py-8">
                    Belum ada alamat pengiriman yang tersimpan. Klik tombol di atas untuk menambahkan.
                </p>
            @endif
        </div>
    @endif

    <!-- TAB 3: Pengaturan Akun -->
    @if($activeTab === 'profile')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Profil Form -->
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-2">
                    Ubah Data Diri
                </h3>
                <form wire:submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-xs font-mono text-[var(--color-text-muted)] uppercase mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('name') <span class="text-[10px] text-red-400 font-mono">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-[var(--color-text-muted)] uppercase mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('email') <span class="text-[10px] text-red-400 font-mono">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-[var(--color-text-muted)] uppercase mb-1">Nomor HP / WhatsApp</label>
                        <input type="text" wire:model="userPhone" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('userPhone') <span class="text-[10px] text-red-400 font-mono">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-2.5 text-xs font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                        Simpan Perubahan Profil
                    </button>
                </form>
            </div>

            <!-- Password Form -->
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg space-y-4">
                <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-2">
                    Ubah Kata Sandi
                </h3>
                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-xs font-mono text-[var(--color-text-muted)] uppercase mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" wire:model="current_password" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('current_password') <span class="text-[10px] text-red-400 font-mono">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-[var(--color-text-muted)] uppercase mb-1">Kata Sandi Baru</label>
                        <input type="password" wire:model="new_password" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('new_password') <span class="text-[10px] text-red-400 font-mono">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-mono text-[var(--color-text-muted)] uppercase mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" wire:model="new_password_confirmation" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                    </div>

                    <button type="submit" class="w-full py-2.5 text-xs font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                        Perbarui Kata Sandi
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Form Alamat Pengiriman -->
    @if($showAddressModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-2xl max-w-lg w-full space-y-4">
                <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)] pb-2">
                    {{ $editingAddressId ? 'Edit Alamat Pengiriman' : 'Tambah Alamat Pengiriman Baru' }}
                </h3>

                <form wire:submit.prevent="saveAddress" class="space-y-3 font-mono text-xs">
                    <div>
                        <label class="block text-[var(--color-text-muted)] mb-1">Label Alamat (misal: Rumah, Kantor)</label>
                        <input type="text" wire:model="label" placeholder="Rumah" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                        @error('label') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] mb-1">Nama Penerima</label>
                        <input type="text" wire:model="recipient_name" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                        @error('recipient_name') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] mb-1">Nomor Telepon / WA</label>
                        <input type="text" wire:model="phone" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                        @error('phone') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] mb-1">Alamat Lengkap (Jalan, RT/RW, No. Rumah)</label>
                        <textarea wire:model="full_address" rows="2" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]"></textarea>
                        @error('full_address') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[var(--color-text-muted)] mb-1">Kecamatan</label>
                            <input type="text" wire:model="district" placeholder="misal: Bogor Barat" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                            @error('district') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[var(--color-text-muted)] mb-1">Kota / Kabupaten</label>
                            <input type="text" wire:model="city" placeholder="misal: Kota Bogor" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                            @error('city') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[var(--color-text-muted)] mb-1">Provinsi</label>
                            <input type="text" wire:model="province" placeholder="misal: Jawa Barat" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                            @error('province') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-[var(--color-text-muted)] mb-1">Kode Pos</label>
                            <input type="text" wire:model="postal_code" placeholder="16115" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)]">
                            @error('postal_code') <span class="text-red-400 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 pt-2">
                        <input type="checkbox" id="is_default" wire:model="is_default" class="rounded border-[var(--color-coffee-brown)] bg-[var(--color-bg-base)] text-[var(--color-accent-gold)]">
                        <label for="is_default" class="text-[var(--color-text-muted)]">Jadikan alamat utama</label>
                    </div>

                    <div class="pt-4 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-end space-x-3">
                        <button type="button" wire:click="closeAddressModal" class="px-4 py-2 text-xs font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-muted)] rounded-[var(--radius-sm)]">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-xs font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)]">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
