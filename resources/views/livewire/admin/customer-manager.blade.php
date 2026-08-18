<div class="py-8 max-w-7xl mx-auto space-y-8">
    <!-- Header Navigation -->
    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-lg)] border border-[var(--color-coffee-brown)] shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-1 text-[10px] uppercase font-bold text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded">
                Admin Panel
            </span>
            <h1 class="font-display text-2xl font-bold text-[var(--color-text-primary)] mt-1">
                Kelola Akun Pelanggan & Pengguna
            </h1>
        </div>

        <div class="flex items-center space-x-3 font-mono text-xs">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                &larr; Kembali ke Dashboard
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] rounded-[var(--radius-sm)]">
                📦 Kelola Pesanan
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
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="w-full md:w-80">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, email, no HP pelanggan..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
            </div>

            <select wire:model.live="roleFilter" class="px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                <option value="">Semua Peran (Role)</option>
                <option value="customer">Pelanggan (Customer)</option>
                <option value="admin">Administrator</option>
            </select>
        </div>

        <button wire:click="openCustomerModal" class="w-full md:w-auto px-5 py-2.5 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
            + Tambah Pelanggan Baru
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-lg">
        @if($users->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left font-mono text-xs divide-y divide-[var(--color-coffee-brown)]/40">
                    <thead>
                        <tr class="text-[var(--color-text-muted)] uppercase text-[10px]">
                            <th class="py-3 px-4">Nama Pelanggan</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">No. HP / WhatsApp</th>
                            <th class="py-3 px-4">Peran (Role)</th>
                            <th class="py-3 px-4">Terdaftar Sejak</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-coffee-brown)]/30">
                        @foreach($users as $user)
                            <tr class="hover:bg-[var(--color-bg-base)]/50 transition-colors">
                                <td class="py-3 px-4 font-bold text-[var(--color-text-primary)]">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-[var(--color-accent-gold)]/20 border border-[var(--color-accent-gold)] text-[var(--color-accent-gold)] font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-[var(--color-text-muted)]">
                                    {{ $user->email }}
                                </td>
                                <td class="py-3 px-4 text-[var(--color-text-muted)]">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="py-3 px-4">
                                    @if($user->role === 'admin')
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-amber-400 bg-amber-950 border border-amber-700/50 rounded">Admin</span>
                                    @else
                                        <span class="px-2.5 py-1 text-[9px] uppercase font-bold text-emerald-400 bg-emerald-950 border border-emerald-700/50 rounded">Pelanggan</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-[var(--color-text-muted)]">
                                    {{ $user->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-right space-x-2">
                                    <button wire:click="openCustomerModal({{ $user->id }})" class="px-3 py-1.5 font-bold border border-[var(--color-accent-gold)] text-[var(--color-accent-gold)] rounded hover:bg-[var(--color-accent-gold)] hover:text-[var(--color-bg-base)] transition-colors">
                                        Edit
                                    </button>
                                    @if($user->id !== auth()->id())
                                        <button wire:click="deleteCustomer({{ $user->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus akun pelanggan ini?')" class="px-3 py-1.5 font-bold bg-rose-950/60 border border-rose-800/50 text-rose-300 hover:bg-rose-900 rounded transition-colors">
                                            Hapus
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @else
            <p class="text-xs text-[var(--color-text-muted)] font-mono text-center py-12">
                Tidak ada data pelanggan yang ditemukan.
            </p>
        @endif
    </div>

    <!-- Customer Modal -->
    @if($showCustomerModal)
        <div class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] shadow-2xl max-w-lg w-full space-y-4 font-mono text-xs">
                <div class="flex items-center justify-between border-b border-[var(--color-coffee-brown)] pb-3">
                    <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] font-sans">
                        {{ $editingUserId ? 'Edit Akun Pelanggan' : 'Tambah Akun Pelanggan Baru' }}
                    </h3>
                    <button wire:click="closeCustomerModal" class="text-[var(--color-text-muted)] hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="saveCustomer" class="space-y-3">
                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Nama Lengkap</label>
                        <input type="text" wire:model="name" placeholder="Eko Prasetyo" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        @error('name') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Email</label>
                        <input type="email" wire:model="email" placeholder="eko@example.com" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        @error('email') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Nomor HP / WhatsApp</label>
                        <input type="text" wire:model="phone" placeholder="081234567890" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        @error('phone') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">Peran (Role)</label>
                        <select wire:model="role" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                            <option value="customer">Pelanggan (Customer)</option>
                            <option value="admin">Administrator</option>
                        </select>
                        @error('role') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-[var(--color-text-muted)] uppercase mb-1">
                            Kata Sandi {{ $editingUserId ? '(Kosongkan jika tidak diubah)' : '*' }}
                        </label>
                        <input type="password" wire:model="password" placeholder="••••••••" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded">
                        @error('password') <span class="text-rose-400 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-end space-x-3">
                        <button type="button" wire:click="closeCustomerModal" class="px-4 py-2 font-semibold border border-[var(--color-coffee-brown)] text-[var(--color-text-muted)] rounded">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded">
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
