<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Way Kopi — Kopi Robusta Lampung' }}</title>
    
    <!-- SKRIP TEMA INSTAN (Cegah Kedip) -->
    <script>
        if (localStorage.getItem('waykopi_theme') === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--color-bg-base)] text-[var(--color-text-primary)] antialiased min-h-screen flex flex-col justify-between transition-colors duration-300">

    <!-- HEADER / NAVBAR -->
    <header class="sticky top-0 z-40 bg-[var(--color-bg-surface)] border-b border-black/10 dark:border-white/10 text-[var(--color-text-primary)] shadow-xl transition-colors duration-300 h-24 flex items-center justify-between px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center group">
            <img src="/images/waykopi_logo.png" alt="Way Kopi" class="h-16 sm:h-20 w-auto object-contain transition-transform group-hover:scale-105">
        </a>
        <nav class="hidden md:flex space-x-8 font-mono text-xs font-bold uppercase tracking-widest">
            <a href="{{ route('products.index') }}" class="hover:text-[var(--color-accent-gold)]">Katalog</a>
            <a href="{{ route('about.index') }}" class="hover:text-[var(--color-accent-gold)]">Cerita Petani</a>
            <a href="{{ route('blog.index') }}" class="hover:text-[var(--color-accent-gold)]">Jurnal</a>
        </nav>
        <div>
            <a href="{{ route('cart.index') }}" class="hover:text-[var(--color-accent-gold)] p-1.5" title="Keranjang">
                <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
            </a>
        </div>
    </header>

    <!-- AREA KONTEN (HALAMAN LAIN AKAN MASUK KE SINI) -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="bg-[var(--color-bg-surface)] border-t border-black/10 dark:border-white/10 text-[var(--color-text-muted)] font-mono text-xs py-8 mt-auto text-center transition-colors duration-300">
        <p>&copy; 2026 Way Kopi Lampung (waykopi.com).</p>
    </footer>

    <!-- TOMBOL TEMA MELAYANG (OTOMATIS DI SEMUA HALAMAN) -->
    <button onclick="toggleGlobalTheme()" class="fixed bottom-6 left-6 z-50 p-3.5 rounded-full bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 text-[var(--color-accent-gold)] shadow-2xl hover:scale-110 transition-all flex items-center justify-center">
        <span id="global-mode-icon" class="text-base">🌙</span>
    </button>

    <!-- TOMBOL WA MELAYANG (OTOMATIS DI SEMUA HALAMAN) -->
    <a href="https://wa.me/6281234567890?text=Halo" target="_blank" class="fixed bottom-6 right-6 z-50 bg-green-600 hover:bg-green-700 text-white p-4 rounded-full shadow-2xl hover:scale-110 transition-all flex items-center justify-center">
        <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
    </a>

    <script>
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
        window.addEventListener('DOMContentLoaded', () => {
            const icon = document.getElementById('global-mode-icon');
            if (icon) icon.innerText = document.documentElement.classList.contains('dark') ? '🌙' : '☀️';
        });
    </script>
    @livewireScripts
</body>
</html>