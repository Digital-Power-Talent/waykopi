<x-layouts.admin>
    <x-slot name="header">Dashboard Admin</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
            <span class="text-xs font-mono text-[var(--color-text-muted)]">Total Pesanan</span>
            <div class="text-2xl font-bold font-mono text-[var(--color-accent-gold)] mt-2">10 Order</div>
        </div>
        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
            <span class="text-xs font-mono text-[var(--color-text-muted)]">Pendapatan Total</span>
            <div class="text-2xl font-bold font-mono text-[var(--color-accent-gold)] mt-2">Rp 1.500.000</div>
        </div>
        <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
            <span class="text-xs font-mono text-[var(--color-text-muted)]">Status Sistem</span>
            <div class="text-2xl font-bold font-mono text-[var(--color-success)] mt-2">Aktif</div>
        </div>
    </div>

    <div class="bg-[var(--color-bg-surface)] p-6 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)]">
        <h2 class="text-sm font-semibold text-[var(--color-text-primary)] mb-4">Selamat datang di Admin Panel Way Kopi</h2>
        <p class="text-xs text-[var(--color-text-muted)] leading-relaxed">
            Gunakan menu di sidebar untuk mengelola katalog produk, pesanan pembeli, dan artikel blog.
        </p>
    </div>
</x-layouts.admin>
