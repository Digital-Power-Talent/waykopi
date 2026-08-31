<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Way Kopi — Kopi Robusta Lampung' }}</title>
    
    <!-- Vite CSS & JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- SISTEM PENGATUR WARNA DINAMIS GLOBAL & PEMBUNGKUS BORDER OTOMATIS -->
    <style>
        :root {
            --bg-base: #120F0D;
            --bg-surface: #18120F;
            --text-main: #EADBC8;
            --text-muted: #A69C8D;
            --border-custom: #3A2E28;
            --color-accent-gold: #C8A050;
            --color-accent-gold-bright: #E4C374;
            --color-error: #B85C50;
        }

        body.light-mode {
            --bg-base: #F9F6F0;
            --bg-surface: #FFFFFF;
            --text-main: #2C221E;
            --text-muted: #6B5B53;
            --border-custom: #E2D4CC;
        }

        body {
            background-color: var(--bg-base) !important;
            color: var(--text-main) !important;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* PERBAIKAN MUTLAK: Memaksa semua garis kotak putih/terang agar berubah jadi cokelat gelap saat mode gelap */
        :not(body.light-mode) [class*="border-white"], 
        :not(body.light-mode) [class*="border-gray-"] {
            border-color: var(--border-custom) !important;
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-between selection:bg-[var(--color-accent-gold-bright)] selection:text-[#2C221E]">

    @php
        $cartCount = app(\App\Services\CartService::class)->getItemCount();
    @endphp

    <!-- ========================================== -->
    <!-- HEADER / NAVBAR UTAMA (STICKY & PREMIUM) -->
    <!-- ========================================== -->
    <header x-data="{ userDropdownOpen: false, mobileMenuOpen: false }" class="sticky top-0 bg-[#0a0a0a] border-b border-[#3A2E28] z-[100] shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 relative">
                
                <!-- KIRI: Link Navigasi Desktop -->
                <div class="hidden md:flex items-center space-x-6 lg:space-x-10 w-5/12 justify-start">
                    <a href="{{ route('products.index') }}" class="whitespace-nowrap font-mono text-xs font-bold tracking-widest uppercase text-red-600 hover:text-red-500 transition-colors">
                        Katalog Kopi
                    </a>
                    <a href="{{ route('about.index') }}" class="whitespace-nowrap font-mono text-xs font-bold tracking-widest uppercase text-white hover:text-gray-300 transition-colors">
                        Cerita Petani
                    </a>
                </div>

                <!-- TENGAH: Logo Melayang dengan Efek Cincin Merah -->
                <div class="absolute left-1/2 transform -translate-x-1/2 top-2 sm:top-2.5 z-[110]">
                    <a href="{{ route('home') }}" class="block rounded-full bg-[#120F0D] border-[3px] border-red-600 shadow-xl shadow-red-600/50 p-1 sm:p-1.5 hover:scale-105 transition-all duration-300">
                        <img src="/images/waykopi_logo.png" alt="Way Kopi" class="h-12 w-12 sm:h-16 sm:w-16 object-contain rounded-full">
                    </a>
                </div>

                <!-- KANAN: Link Navigasi & Ikon Desktop -->
                <div class="hidden md:flex items-center space-x-6 lg:space-x-10 w-5/12 justify-end">
                    <a href="{{ route('blog.index') }}" class="whitespace-nowrap font-mono text-xs font-bold tracking-widest uppercase text-white hover:text-gray-300 transition-colors">
                        Jurnal & Seduh
                    </a>
                    <a href="{{ route('products.index') }}" class="whitespace-nowrap font-mono text-xs font-bold tracking-widest uppercase text-[var(--color-accent-gold)] hover:text-yellow-400 transition-colors">
                        Promo Launching
                    </a>
                    
                    <!-- Garis Pemisah Vertikal & Ikon -->
                    <div class="flex items-center space-x-4 lg:space-x-5 border-l border-gray-700 pl-4 lg:pl-6 ml-2">
                        
                        <!-- Dropdown Akun User -->
                        <div class="relative flex items-center" @click.away="userDropdownOpen = false">
                            @auth
                                <button @click="userDropdownOpen = !userDropdownOpen" class="text-white hover:text-[var(--color-accent-gold)] p-1.5 transition-colors focus:outline-none">
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

                <!-- TAMPILAN MOBILE (HP) -->
                <div class="md:hidden flex items-center justify-between w-full">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-white hover:text-[var(--color-accent-gold)] focus:outline-none z-40 relative p-1.5">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <div class="flex items-center space-x-4 z-40 relative">
                        <a href="{{ route('cart.index') }}" class="relative text-white hover:text-[var(--color-accent-gold)] p-1.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            @if($cartCount > 0)
                                <span class="absolute 0 -right-1 px-1 text-[9px] font-bold bg-[var(--color-error)] text-white rounded-full min-w-[16px] text-center shadow-sm">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Menu Dropdown Mobile -->
        <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden bg-[#0a0a0a] border-b border-[#3A2E28] px-6 py-6 font-mono text-xs uppercase tracking-wider space-y-3 shadow-xl absolute w-full left-0 top-full text-white z-40">
            <div class="grid grid-cols-1 gap-2 font-bold">
                <a href="{{ route('products.index') }}" class="py-2.5 px-3 hover:bg-[#1a1a1a] rounded flex justify-between text-red-500"><span>☕ Katalog Kopi</span><span>&rarr;</span></a>
                <a href="{{ route('about.index') }}" class="py-2.5 px-3 hover:bg-[#1a1a1a] rounded flex justify-between"><span>🌱 Cerita Petani</span><span>&rarr;</span></a>
                <a href="{{ route('blog.index') }}" class="py-2.5 px-3 hover:bg-[#1a1a1a] rounded flex justify-between"><span>📖 Jurnal & Seduh</span><span>&rarr;</span></a>
                <a href="{{ route('cart.index') }}" class="py-2.5 px-3 bg-[#1a1a1a] rounded text-[var(--color-accent-gold)] flex justify-between border border-[#3A2E28]"><span>🛒 Keranjang ({{ $cartCount }})</span><span>&rarr;</span></a>
                
                @auth
                    <a href="{{ route('account') }}" class="py-2.5 px-3 hover:bg-[#1a1a1a] rounded flex justify-between text-gray-300 border-t border-[#3A2E28] mt-2 pt-4"><span>👤 Akun Saya</span><span>&rarr;</span></a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full text-left py-2.5 px-3 hover:bg-red-900/20 text-red-500 rounded flex justify-between"><span>Keluar</span><span>&rarr;</span></button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="py-2.5 px-3 hover:bg-[#1a1a1a] rounded flex justify-between text-gray-300 border-t border-[#3A2E28] mt-2 pt-4"><span>👤 Masuk / Daftar</span><span>&rarr;</span></a>
                @endauth
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 relative z-10">
        {{ $slot }}
    </main>

    <!-- FOOTER PROFESIONAL -->
    <footer class="bg-[#18120F] border-t border-[#3A2E28] text-[#D1C7BD] font-mono text-xs pt-16 pb-8 mt-auto relative z-10">
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

    <!-- ========================================== -->
    <!-- TOMBOL TEMA & WHATSAPP MELAYANG GLOBAL -->
    <!-- ========================================== -->

    <!-- TOMBOL GANTI TEMA (KIRI BAWAH) -->
    <button onclick="toggleGlobalTheme()" 
            class="fixed bottom-4 left-4 sm:bottom-6 sm:left-6 z-[120] p-3 sm:p-3.5 rounded-full bg-white dark:bg-[#18120F] border border-gray-300 dark:border-[#3A2E28] shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center group cursor-pointer"
            title="Ubah Mode Gelap / Terang">
        
        <!-- Ikon Matahari (Hanya Tampil di Mode Terang / Light Mode) -->
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>

        <!-- Ikon Bulan Emas (Hanya Tampil di Mode Gelap / Dark Mode) -->
        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[var(--color-accent-gold)] hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
        </svg>

        <!-- Tooltip Label -->
        <span class="absolute left-full ml-3 px-3 py-1.5 bg-white dark:bg-[#18120F] text-[#2C221E] dark:text-white text-[10px] sm:text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-gray-300 dark:border-[#3A2E28] uppercase tracking-wider pointer-events-none">
            Ubah Tema
        </span>
    </button>

    <!-- TOMBOL WHATSAPP (KANAN BAWAH) -->
    <a href="https://wa.me/6281234567890?text=Halo%20Way%20Kopi,%20saya%20tertarik%20untuk%20memesan%20kopi." 
       target="_blank" 
       class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[120] bg-green-600 hover:bg-green-700 text-white p-3.5 sm:p-4 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 group cursor-pointer"
       title="Chat WhatsApp CS Way Kopi">
        <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-current" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
        <span class="absolute right-full mr-3 px-3 py-1.5 bg-[#18120F] text-white text-[10px] sm:text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-[#3A2E28] uppercase tracking-wider pointer-events-none">
            Chat WhatsApp CS
        </span>
    </a>

    <!-- SCRIPT GANTI TEMA -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('waykopi_theme');
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
                document.documentElement.classList.remove('dark');
            } else {
                document.body.classList.remove('light-mode');
                document.documentElement.classList.add('dark');
            }
        })();

        function toggleGlobalTheme() {
            const body = document.body;
            
            if (body.classList.contains('light-mode')) {
                body.classList.remove('light-mode');
                document.documentElement.classList.add('dark');
                localStorage.setItem('waykopi_theme', 'dark');
            } else {
                body.classList.add('light-mode');
                document.documentElement.classList.remove('dark');
                localStorage.setItem('waykopi_theme', 'light');
            }
        }
    </script>

    @livewireScripts
</body>
</html>