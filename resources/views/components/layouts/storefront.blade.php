<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Way Kopi — Kopi Robusta Lampung' }}</title>
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--color-bg-base)] text-[var(--color-text-primary)] antialiased min-h-screen flex flex-col justify-between selection:bg-[var(--color-accent-gold-bright)] selection:text-[var(--color-text-primary)]">

    <!-- HEADER / NAVBAR UTAMA (Gelap Elegan & Logo Besar Pas) -->
    @php
        $cartCount = app(\App\Services\CartService::class)->getItemCount();
    @endphp

    <header x-data="{ userDropdownOpen: false, mobileMenuOpen: false }" class="sticky top-0 z-50 bg-[#18120F] border-b border-[#3A2E28] text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-24 flex items-center justify-between">
            
            <!-- SISI KIRI: Logo Ukuran Besar Pas & Menu Navigasi -->
            <div class="flex items-center space-x-6 lg:space-x-10">
                <!-- Tombol Mobile -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden text-white hover:text-[var(--color-accent-gold)] p-1.5 transition-colors">
                    <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <!-- Logo Diperbesar Ukuran Pas & Proporsional -->
                <a href="{{ route('home') }}" class="flex items-center group focus:outline-none py-2" title="Way Kopi — Beranda">
                    <img src="/images/waykopi_logo.png" alt="Way Kopi Kopi Robusta" class="h-24 sm:h-40 w-auto object-contain transition-transform group-hover:scale-105">
                </a>

                <!-- Menu Navigasi Desktop -->
                <nav class="hidden md:flex items-center space-x-8 font-mono text-xs uppercase tracking-widest font-bold text-[#EADBC8]">
                    <a href="{{ route('products.index') }}" class="py-2 hover:text-[var(--color-accent-gold)] transition-colors {{ request()->routeIs('products.*') ? 'text-[var(--color-accent-gold)]' : '' }}">Katalog</a>
                    <a href="{{ route('about.index') }}" class="py-2 hover:text-[var(--color-accent-gold)] transition-colors {{ request()->routeIs('about.*') ? 'text-[var(--color-accent-gold)]' : '' }}">Cerita Petani</a>
                    <a href="{{ route('blog.index') }}" class="py-2 hover:text-[var(--color-accent-gold)] transition-colors {{ request()->routeIs('blog.*') ? 'text-[var(--color-accent-gold)]' : '' }}">Jurnal</a>
                </nav>
            </div>

            <!-- SISI KANAN: Promo, Akun, & Keranjang Belanja -->
            <div class="flex items-center space-x-4 sm:space-x-6 font-mono text-xs uppercase tracking-widest font-bold text-[#EADBC8]">
                <a href="{{ route('products.index') }}" class="hidden lg:inline-flex py-2 text-[var(--color-accent-gold)] hover:text-[var(--color-accent-gold-bright)] transition-colors">
                    Promo Launching
                </a>

                <div class="flex items-center space-x-3 pl-2 sm:pl-4 border-l border-[#3A2E28]">
                    <!-- Dropdown Akun User -->
                    <div class="relative flex items-center" @click.away="userDropdownOpen = false">
                        @auth
                            <button @click="userDropdownOpen = !userDropdownOpen" class="text-white hover:text-[var(--color-accent-gold)] p-1.5 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </button>
                            <div x-show="userDropdownOpen" style="display: none;" class="absolute right-0 top-10 w-56 bg-[#241C18] border border-[#3A2E28] rounded-md shadow-xl py-2 z-50 text-xs font-mono normal-case text-white">
                                <div class="px-4 py-2 border-b border-[#3A2E28]">
                                    <p class="text-[10px] text-[#A69C8D]">Masuk sebagai</p>
                                    <p class="font-bold text-[var(--color-accent-gold)] truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('account') }}" class="block px-4 py-2 hover:bg-[#322722] text-white transition-colors">Dashboard Akun</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-[#322722] text-[var(--color-accent-gold-bright)] font-bold transition-colors">Panel Admin</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-[var(--color-error)] hover:bg-[var(--color-error)]/10 transition-colors">Logout</button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-white hover:text-[var(--color-accent-gold)] p-1.5 transition-colors" title="Masuk">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </a>
                        @endauth
                    </div>

                    <!-- Ikon Keranjang -->
                    <a href="{{ route('cart.index') }}" class="relative text-white hover:text-[var(--color-accent-gold)] p-1.5 transition-colors" title="Keranjang Belanja">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 px-1.5 py-0.5 text-[9px] font-bold bg-[var(--color-error)] text-white rounded-full min-w-[18px] text-center shadow-sm">{{ $cartCount }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu Dropdown untuk HP (Mobile) -->
        <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden bg-[#18120F] border-b border-[#3A2E28] px-6 py-6 font-mono text-xs uppercase tracking-wider space-y-3 shadow-xl absolute w-full left-0 text-white">
            <div class="grid grid-cols-1 gap-2 font-bold">
                <a href="{{ route('products.index') }}" class="py-2.5 px-3 hover:bg-[#241C18] rounded flex justify-between"><span>☕ Katalog Kopi</span><span>&rarr;</span></a>
                <a href="{{ route('about.index') }}" class="py-2.5 px-3 hover:bg-[#241C18] rounded flex justify-between"><span>🌱 Cerita Petani</span><span>&rarr;</span></a>
                <a href="{{ route('blog.index') }}" class="py-2.5 px-3 hover:bg-[#241C18] rounded flex justify-between"><span>📖 Jurnal & Seduh</span><span>&rarr;</span></a>
                <a href="{{ route('cart.index') }}" class="py-2.5 px-3 bg-[#241C18] rounded text-[var(--color-accent-gold)] flex justify-between border border-[#3A2E28]"><span>🛒 Keranjang ({{ $cartCount }})</span><span>&rarr;</span></a>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- FOOTER PROFESIONAL -->
    <footer class="bg-[#18120F] border-t border-[#3A2E28] text-[#D1C7BD] font-mono text-xs pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 pb-12 border-b border-[#3A2E28]">
                
                <!-- Kolom 1: Logo & Deskripsi -->
                <div class="space-y-4">
                    <img src="/images/waykopi_logo.png" alt="Way Kopi" class="h-10 w-auto object-contain">
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Kopi Robusta kualitas petik merah dipanen langsung dari kebun petani lereng Gunung Tanggamus, Lampung.
                    </p>
                </div>

                <!-- Kolom 2: Navigasi Utama -->
                <div class="space-y-4">
                    <h4 class="font-bold uppercase tracking-wider text-white pb-2 border-b border-[#3A2E28]">Navigasi Utama</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('products.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Katalog Produk Kopi</a></li>
                        <li><a href="{{ route('about.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Cerita Petani Lampung</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Jurnal & Panduan Seduh</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Keranjang Belanja</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak & Logistik -->
                <div class="space-y-4">
                    <h4 class="font-bold uppercase tracking-wider text-white pb-2 border-b border-[#3A2E28]">Kontak & Logistik</h4>
                    <div class="space-y-2 text-[var(--color-text-muted)] leading-relaxed">
                        <p>WhatsApp CS: +62 812-3456-7890</p>
                        <p>Email: halo@waykopi.com</p>
                        <p>Gudang Pengiriman: Tajurhalang, Kab. Bogor, Jawa Barat</p>
                        <p class="pt-1">Mitra Ekspedisi: Biteship (JNE, Sicepat, J&T, Lion, Paxel)</p>
                    </div>
                </div>

                <!-- Kolom 4: Langganan Diskon 10% -->
                <div class="space-y-4">
                    <h4 class="font-bold uppercase tracking-wider text-white pb-2 border-b border-[#3A2E28]">Langganan Diskon 10%</h4>
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Dapatkan promo eksklusif & rilis batch sangrai kopi segar mingguan.
                    </p>
                    <form onsubmit="event.preventDefault();" class="space-y-3">
                        <input type="email" placeholder="Email kamu..." class="w-full px-3 py-2.5 bg-[#100E0C] border border-[#3A2E28] rounded-[var(--radius-sm)] text-white placeholder-gray-500 focus:outline-none focus:border-[var(--color-accent-gold)] text-xs">
                        <button type="submit" class="w-full py-2.5 px-4 bg-[var(--color-accent-gold)] hover:bg-[var(--color-accent-gold-bright)] text-[#18120F] font-bold uppercase tracking-wider rounded-[var(--radius-sm)] transition-colors shadow-md">
                            Dapatkan Promo
                        </button>
                    </form>
                </div>

            </div>

            <!-- Copyright Bar -->
            <div class="pt-8 text-center text-[var(--color-text-muted)] text-[11px]">
                <p>&copy; 2026 Way Kopi Lampung (waykopi.com). Direct-to-Consumer Coffee Brand.</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>