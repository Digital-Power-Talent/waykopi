<!DOCTYPE html>
<html lang="id" class="dark h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin Panel — Way Kopi' }}</title>

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=2">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body x-data="{ sidebarOpen: false }" class="bg-[var(--color-bg-base)] text-[var(--color-text-primary)] font-body antialiased min-h-full flex flex-col selection:bg-[var(--color-accent-gold)] selection:text-black">
    <div class="flex flex-1 relative min-h-screen">
        
        <!-- Mobile Sidebar Overlay Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             style="display: none;"
             class="fixed inset-0 bg-black/70 backdrop-blur-sm z-40 md:hidden"></div>

        <!-- Admin Sidebar -->
        <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
               class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-[var(--color-bg-surface)] border-r border-[var(--color-coffee-brown)] flex flex-col transform transition-transform duration-300 ease-in-out md:translate-x-0">
            
            <div class="h-16 flex items-center justify-between px-6 border-b border-[var(--color-coffee-brown)]">
                <a href="{{ route('admin.dashboard') }}" class="font-display font-bold text-lg text-[var(--color-accent-gold)] tracking-wider">
                    WAY KOPI <span class="text-xs font-mono text-[var(--color-text-muted)]">[Admin]</span>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-[var(--color-text-muted)] hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="flex-1 px-4 py-6 space-y-2 text-sm font-medium font-mono">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3.5 py-2.5 rounded-[var(--radius-sm)] transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[var(--color-accent-gold)]/10 text-[var(--color-accent-gold)] font-bold border-l-4 border-[var(--color-accent-gold)]' : 'hover:bg-[var(--color-bg-base)] text-[var(--color-text-primary)]' }}">
                    <span class="mr-2.5">📊</span> Dashboard Admin
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center px-3.5 py-2.5 rounded-[var(--radius-sm)] transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-[var(--color-accent-gold)]/10 text-[var(--color-accent-gold)] font-bold border-l-4 border-[var(--color-accent-gold)]' : 'hover:bg-[var(--color-bg-base)] text-[var(--color-text-primary)]' }}">
                    <span class="mr-2.5">🛒</span> Kelola Pesanan
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center px-3.5 py-2.5 rounded-[var(--radius-sm)] transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-[var(--color-accent-gold)]/10 text-[var(--color-accent-gold)] font-bold border-l-4 border-[var(--color-accent-gold)]' : 'hover:bg-[var(--color-bg-base)] text-[var(--color-text-primary)]' }}">
                    <span class="mr-2.5">📦</span> Kelola Produk
                </a>
                <a href="{{ route('admin.posts.index') }}" class="flex items-center px-3.5 py-2.5 rounded-[var(--radius-sm)] transition-colors {{ request()->routeIs('admin.posts.*') ? 'bg-[var(--color-accent-gold)]/10 text-[var(--color-accent-gold)] font-bold border-l-4 border-[var(--color-accent-gold)]' : 'hover:bg-[var(--color-bg-base)] text-[var(--color-text-primary)]' }}">
                    <span class="mr-2.5">📰</span> Kelola Blog
                </a>
                <a href="{{ route('account') }}" class="flex items-center px-3.5 py-2.5 rounded-[var(--radius-sm)] transition-colors {{ request()->routeIs('account') ? 'bg-[var(--color-accent-gold)]/10 text-[var(--color-accent-gold)] font-bold border-l-4 border-[var(--color-accent-gold)]' : 'hover:bg-[var(--color-bg-base)] text-[var(--color-text-primary)]' }}">
                    <span class="mr-2.5">⚙️</span> Pengaturan Akun
                </a>
            </nav>

            <div class="p-4 border-t border-[var(--color-coffee-brown)] text-xs text-[var(--color-text-muted)] font-mono space-y-2">
                <a href="{{ route('home') }}" target="_blank" class="flex items-center text-[var(--color-accent-gold)] hover:underline">
                    <span>Storefront Main Site</span>
                    <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </aside>

        <!-- Admin Main Body -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-16 bg-[var(--color-bg-surface)] border-b border-[var(--color-coffee-brown)] flex items-center justify-between px-4 sm:px-8 sticky top-0 z-30">
                <div class="flex items-center space-x-3">
                    <button @click="sidebarOpen = !sidebarOpen" type="button" class="md:hidden p-2 text-[var(--color-text-primary)] hover:text-[var(--color-accent-gold)] focus:outline-none rounded-[var(--radius-sm)] border border-[var(--color-coffee-brown)]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="font-semibold text-base sm:text-lg text-[var(--color-text-primary)] truncate">
                        {{ $header ?? 'Dashboard Admin' }}
                    </h1>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="hidden sm:inline-block text-xs font-mono text-[var(--color-text-muted)]">
                        Admin: <strong class="text-[var(--color-accent-gold)]">{{ auth()->user()->name ?? 'Admin' }}</strong>
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 text-xs font-mono font-bold text-red-400 border border-red-500/30 rounded-[var(--radius-sm)] hover:bg-red-500/10 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
