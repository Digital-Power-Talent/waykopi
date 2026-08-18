@php
    $cartCount = app(\App\Services\CartService::class)->getItemCount();
@endphp

<header x-data="{ userDropdownOpen: false, mobileMenuOpen: false }" class="sticky top-0 z-50 bg-[#060606] border-b border-neutral-900 shadow-2xl">
    <!-- Navbar Header Container -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between font-mono text-xs uppercase tracking-widest font-black text-white">
        
        <!-- Left Navigation Menu (KATALOG | TENTANG PETANI) -->
        <div class="flex items-center space-x-3 sm:space-x-6 lg:space-x-8 justify-start flex-1">
            <!-- Mobile Hamburger Toggle Button (Shown on Mobile < md) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="md:hidden text-white hover:text-[#e31818] p-1.5 focus:outline-none flex items-center gap-1.5" title="Menu Navigasi">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="text-[10px] uppercase tracking-wider text-neutral-400 font-bold">MENU</span>
            </button>

            <!-- Desktop Links (Left) -->
            <a href="{{ route('products.index') }}" class="hidden md:inline-flex items-center py-2 transition-colors hover:text-[#e31818] whitespace-nowrap {{ request()->routeIs('products.*') ? 'text-[#e31818]' : 'text-white' }}">
                KATALOG KOPI
            </a>
            <a href="{{ route('about.index') }}" class="hidden md:inline-flex items-center py-2 transition-colors text-white hover:text-[#e31818] whitespace-nowrap {{ request()->routeIs('about.*') ? 'text-[#e31818]' : 'text-white' }}">
                CERITA PETANI
            </a>
        </div>

        <!-- Center Emblem Logo Badge -->
        <div class="flex items-center justify-center relative z-50 px-2 flex-shrink-0">
            <a href="{{ route('home') }}" title="Way Kopi Lampung — Direct from Farmers" class="transform translate-y-2 sm:translate-y-3 transition-transform hover:scale-105 block">
                <div class="w-14 h-14 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-full bg-black border-2 border-[#e31818] p-1 shadow-[0_0_25px_rgba(227,24,24,0.45)] flex items-center justify-center">
                    <div class="w-full h-full rounded-full border border-neutral-800 flex items-center justify-center p-1 text-center bg-gradient-to-b from-neutral-900 to-black overflow-hidden relative">
                        <img src="/images/waykopi_logo.png" 
                             alt="Way Kopi — Fine Robusta Lampung" 
                             class="w-full h-full object-contain filter drop-shadow-[0_2px_4px_rgba(0,0,0,0.9)]">
                    </div>
                </div>
            </a>
        </div>

        <!-- Right Navigation Menu (JURNAL | PROMO | User & Cart Icons) -->
        <div class="flex items-center space-x-3 sm:space-x-6 lg:space-x-8 justify-end flex-1">
            <a href="{{ route('blog.index') }}" class="hidden md:inline-flex items-center py-2 transition-colors hover:text-[#e31818] whitespace-nowrap {{ request()->routeIs('blog.*') ? 'text-[#e31818]' : 'text-white' }}">
                JURNAL & SEDUH
            </a>
            <a href="{{ route('products.index') }}" class="hidden lg:inline-flex items-center py-2 transition-colors text-amber-400 hover:text-amber-300 whitespace-nowrap font-bold">
                PROMO LAUNCHING
            </a>

            <!-- Action Icons (User Account Profile & Cart Bag) -->
            <div class="flex items-center space-x-3 lg:space-x-4 pl-2 border-l border-neutral-800/80">
                <!-- User Account Profile Icon -->
                <div class="relative flex items-center" @click.away="userDropdownOpen = false">
                    @auth
                        <button @click="userDropdownOpen = !userDropdownOpen" type="button" class="flex items-center text-white hover:text-[#e31818] transition-colors p-1.5 focus:outline-none" title="Akun Saya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </button>
                        <!-- User Dropdown Menu -->
                        <div x-show="userDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             style="display: none;"
                             class="absolute right-0 top-10 w-56 bg-neutral-950 border border-neutral-800 rounded-none shadow-2xl py-2 z-50 text-xs font-mono normal-case">
                            
                            <div class="px-4 py-2 border-b border-neutral-800">
                                <p class="text-[10px] uppercase text-neutral-400">Masuk sebagai</p>
                                <p class="font-bold text-[#e31818] truncate">{{ auth()->user()->email }}</p>
                            </div>

                            <a href="{{ route('account') }}" class="flex items-center px-4 py-2.5 hover:bg-neutral-900 text-white hover:text-[#e31818] transition-colors">
                                Dashboard Akun
                            </a>

                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2.5 hover:bg-neutral-900 text-amber-400 font-bold transition-colors">
                                    Panel Admin
                                </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center px-4 py-2.5 text-left text-red-400 hover:bg-red-950/40 transition-colors">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-[#e31818] transition-colors p-1.5" title="Masuk">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </a>
                    @endauth
                </div>

                <!-- Cart Bag Icon with Counter Badge -->
                <a href="{{ route('cart.index') }}" class="relative text-white hover:text-[#e31818] transition-colors p-1.5 flex items-center" title="Keranjang Belanja">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    @if($cartCount > 0)
                        <span class="absolute -top-1 -right-1 px-1.5 py-0.5 text-[9px] font-bold bg-[#e31818] text-white rounded-full min-w-[18px] text-center leading-none shadow-md">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer Overlay Menu (< md Screens) -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         style="display: none;"
         class="md:hidden bg-neutral-950 border-b border-neutral-800 px-6 py-6 font-mono text-xs uppercase tracking-wider space-y-4 shadow-2xl">
        
        <div class="grid grid-cols-1 gap-3 text-white font-bold">
            <a href="{{ route('products.index') }}" class="py-2.5 px-3 hover:bg-neutral-900 rounded hover:text-[#e31818] transition-colors flex items-center justify-between">
                <span>☕ Katalog Kopi Robusta</span>
                <span>&rarr;</span>
            </a>
            <a href="{{ route('about.index') }}" class="py-2.5 px-3 hover:bg-neutral-900 rounded hover:text-[#e31818] transition-colors flex items-center justify-between">
                <span>🌱 Cerita Petani & Tentang Kami</span>
                <span>&rarr;</span>
            </a>
            <a href="{{ route('blog.index') }}" class="py-2.5 px-3 hover:bg-neutral-900 rounded hover:text-[#e31818] transition-colors flex items-center justify-between">
                <span>📖 Jurnal & Panduan Seduh</span>
                <span>&rarr;</span>
            </a>
            <a href="{{ route('cart.index') }}" class="py-2.5 px-3 hover:bg-neutral-900 rounded hover:text-[#e31818] transition-colors flex items-center justify-between text-amber-400">
                <span>🛒 Keranjang Belanja ({{ $cartCount }})</span>
                <span>&rarr;</span>
            </a>

            @auth
                <a href="{{ route('account') }}" class="py-2.5 px-3 bg-neutral-900 rounded text-amber-400 transition-colors flex items-center justify-between">
                    <span>👤 Dashboard Akun</span>
                    <span>&rarr;</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="py-3 px-3 bg-[#e31818] text-white text-center rounded font-black tracking-widest uppercase mt-2 shadow-lg">
                    Masuk / Register
                </a>
            @endauth
        </div>
    </div>
</header>
