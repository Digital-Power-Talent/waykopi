<!DOCTYPE html>
<html lang="id" class="dark h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Way Kopi — Kopi Robusta Lampung Petik Merah Direct from Farmers' }}</title>
    <meta name="description" content="Way Kopi menjual kopi robusta yang dipanen langsung oleh petani di Tanggamus, Lampung, Indonesia. Nikmati cita rasa bold, autentik, direct-to-consumer.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;600;700;900&family=IBM+Plex+Mono:wght@400;500;600;700&family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--color-bg-base)] text-[var(--color-text-primary)] font-body antialiased flex flex-col min-h-full selection:bg-[var(--color-accent-gold)] selection:text-black">
    <!-- Top Announcement Bar -->
    <div class="bg-red-700 text-white font-mono text-center py-2 px-3 text-[10px] sm:text-xs font-bold uppercase tracking-wider z-50">
        <span>Promo Launching: <strong class="text-amber-300 underline">Gratis Ongkir Seluruh Indonesia</strong> untuk Pembelian Pertama!</span>
    </div>

    <!-- Header Navigation Bar -->
    <x-storefront-header />

    <!-- Page Content Container with Responsive Padding -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full pt-6 sm:pt-10 pb-16">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-[var(--color-bg-surface)] border-t border-[var(--color-coffee-brown)] mt-16 sm:mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 py-10 sm:py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-10 font-mono text-xs">
                <!-- Col 1: Logo & About -->
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <img src="/images/waykopi_logo.png" alt="Way Kopi Lampung" class="h-10 w-auto object-contain drop-shadow-[0_2px_6px_rgba(200,160,80,0.3)]">
                    </div>
                    <p class="text-[var(--color-text-muted)] leading-relaxed">
                        Kopi Robusta kualitas petik merah dipanen langsung dari kebun petani lereng Gunung Tanggamus, Lampung.
                    </p>
                </div>

                <!-- Col 2: Navigation Links -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)]/60 pb-2">Navigasi Utama</h4>
                    <ul class="space-y-2 text-[var(--color-text-muted)]">
                        <li><a href="{{ route('products.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Katalog Produk Kopi</a></li>
                        <li><a href="{{ route('about.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Cerita Petani Lampung</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Jurnal & Panduan Seduh</a></li>
                        <li><a href="{{ route('cart.index') }}" class="hover:text-[var(--color-accent-gold)] transition-colors">Keranjang Belanja</a></li>
                    </ul>
                </div>

                <!-- Col 3: Contact & Logistics -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)]/60 pb-2">Kontak & Logistik</h4>
                    <ul class="space-y-2 text-[var(--color-text-muted)]">
                        <li>WhatsApp CS: +62 812-3456-7890</li>
                        <li>Email: halo@waykopi.com</li>
                        <li>Gudang: Tanggamus, Lampung & Bogor Barat</li>
                        <li>Mitra Ekspedisi: Biteship (JNE, Sicepat, J&T, Pos)</li>
                    </ul>
                </div>

                <!-- Col 4: Newsletter -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold uppercase text-[var(--color-text-primary)] border-b border-[var(--color-coffee-brown)]/60 pb-2">Langganan Diskon 10%</h4>
                    <p class="text-[var(--color-text-muted)]">Dapatkan promo eksklusif & rilis batch sangrai kopi segar mingguan.</p>
                    <form class="flex flex-col space-y-2" onsubmit="event.preventDefault();">
                        <input type="email" placeholder="Email kamu..." class="px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        <button type="submit" class="px-3 py-2 font-bold bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors uppercase tracking-wider">
                            Dapatkan Promo
                        </button>
                    </form>
                </div>
            </div>

            <div class="border-t border-[var(--color-coffee-brown)]/50 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-[var(--color-text-muted)] font-mono gap-4 text-center sm:text-left">
                <p>&copy; {{ date('Y') }} Way Kopi Lampung (waykopi.com). Direct-to-Consumer Coffee Brand.</p>
                <div class="flex items-center space-x-4">
                    <span>💳 Payment by Xendit</span>
                    <span>🚚 Shipping by Biteship</span>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
