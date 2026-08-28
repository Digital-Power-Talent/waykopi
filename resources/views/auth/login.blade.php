<x-layouts.storefront>
    <x-slot name="title">Masuk — Way Kopi</x-slot>

    <div class="min-h-[75vh] flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <!-- Logo Jelas & Proporsional -->
            <a href="{{ url('/') }}" class="inline-flex justify-center items-center group mb-4">
                <img src="/images/logo.jpg" alt="Way Kopi — Kopi Robusta Lampung" class="h-20 sm:h-24 w-auto object-contain transition-transform group-hover:scale-105">
            </a>
            <h2 class="text-2xl sm:text-3xl font-display font-bold tracking-tight text-[var(--color-text-primary)] transition-colors duration-300">
                Selamat Datang Kembali
            </h2>
            <p class="mt-2 text-xs font-mono uppercase tracking-widest text-[var(--color-text-muted)] transition-colors duration-300">
                Masuk untuk melacak pesanan & menikmati kopi pilihan
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[420px]">
            <!-- Kartu Login dengan Tema Dinamis Terang & Gelap -->
            <div class="bg-white dark:bg-[#18120F] py-8 px-6 sm:px-10 shadow-sm border border-black/10 dark:border-[#3A2E28] rounded-[var(--radius-lg)] text-[var(--color-text-primary)] transition-colors duration-300">
                
                <!-- Session Status / Flash Messages -->
                @if (session('status'))
                    <div class="mb-6 text-xs font-medium text-emerald-300 bg-emerald-950/50 p-3.5 rounded-[var(--radius-sm)] border border-emerald-800 flex items-center space-x-2">
                        <span>✓</span>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-6 text-xs font-medium text-red-300 bg-red-950/50 p-3.5 rounded-[var(--radius-sm)] border border-red-800 flex items-center space-x-2">
                        <span>⚠️</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-mono uppercase tracking-wider font-semibold text-[var(--color-text-primary)] mb-1.5 transition-colors duration-300">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                placeholder="nama@email.com"
                                class="block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-4 py-3 text-sm text-[var(--color-text-primary)] placeholder-gray-400 focus:border-[var(--color-accent-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-gold)]/30 shadow-inner transition-all duration-300">
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-mono uppercase tracking-wider font-semibold text-[var(--color-text-primary)] transition-colors duration-300">
                                Kata Sandi
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-[var(--color-accent-gold)] hover:underline font-mono">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                placeholder="••••••••"
                                class="block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-4 py-3 text-sm text-[var(--color-text-primary)] placeholder-gray-400 focus:border-[var(--color-accent-gold)] focus:outline-none focus:ring-2 focus:ring-[var(--color-accent-gold)]/30 shadow-inner transition-all duration-300">
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-xs text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember" type="checkbox"
                                class="h-4 w-4 rounded border-black/10 dark:border-[#3A2E28] bg-white dark:bg-[var(--color-bg-base)] text-[var(--color-accent-gold)] focus:ring-[var(--color-accent-gold)]">
                            <label for="remember_me" class="ml-2 block text-xs font-mono text-[var(--color-text-muted)] transition-colors duration-300">
                                Ingat saya
                            </label>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="flex w-full justify-center items-center rounded-[var(--radius-sm)] bg-[var(--color-accent-gold)] px-4 py-3 text-xs font-mono uppercase tracking-widest font-bold text-black hover:bg-[var(--color-accent-gold-bright)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-accent-gold)] transition-all shadow-sm">
                            Masuk ke Akun
                        </button>
                    </div>
                </form>

                <div class="mt-8 pt-6 border-t border-black/10 dark:border-[#3A2E28] text-center text-xs font-mono transition-colors duration-300">
                    <span class="text-[var(--color-text-muted)]">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="font-bold text-[var(--color-accent-gold)] hover:underline ml-1 uppercase tracking-wider">
                        Buat akun baru &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Section -->
    <section class="bg-[var(--color-accent-gold)] py-10 px-4 mt-16 text-black shadow-inner">
        <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            <div>
                <h3 class="font-display text-xl font-bold">Dapatkan Penawaran Batch Panen Perdana</h3>
                <p class="text-xs opacity-90 mt-1 font-mono">Daftarkan email kamu untuk promo gratis ongkir & rilisan edisi terbatas dari petani Lampung.</p>
            </div>
            <form class="flex w-full md:w-auto gap-2" onsubmit="event.preventDefault();">
                <input type="email" placeholder="Alamat email kamu..." class="px-4 py-3 text-xs bg-white text-black rounded-[var(--radius-sm)] focus:outline-none w-full md:w-64 placeholder-gray-400 shadow-sm border border-black/10">
                <button type="submit" class="px-5 py-3 text-xs font-mono uppercase tracking-wider font-bold bg-black text-[var(--color-accent-gold)] rounded-[var(--radius-sm)] hover:bg-neutral-800 transition-colors whitespace-nowrap shadow-sm">
                    Daftar
                </button>
            </form>
        </div>
    </section>

    <!-- ========================================== -->
    <!-- MANUAL: TOMBOL TEMA & WHATSAPP MELAYANG -->
    <!-- ========================================== -->

    <!-- TOMBOL GANTI TEMA (KIRI BAWAH) -->
    <button onclick="toggleGlobalTheme()" 
            class="fixed bottom-6 left-6 z-50 p-3.5 rounded-full bg-[var(--color-bg-surface)] border border-black/10 dark:border-[#3A2E28] text-[var(--color-accent-gold)] shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center group cursor-pointer"
            title="Ubah Mode Gelap / Terang">
        <span id="global-mode-icon" class="text-base">🌙</span>
        <span class="absolute left-full ml-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-black/10 dark:border-[#3A2E28] uppercase tracking-wider pointer-events-none">
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
        <span class="absolute right-full mr-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-black/10 dark:border-[#3A2E28] uppercase tracking-wider pointer-events-none">
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
</x-layouts.storefront>