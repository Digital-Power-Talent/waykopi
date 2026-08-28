<x-layouts.storefront>
    <x-slot name="title">Daftar Akun Baru — Way Kopi</x-slot>

    <div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center group">
                <img src="{{ asset('images/logo.jpg') }}" alt="Way Kopi — Kopi Robusta Lampung" class="h-20 w-auto object-contain mx-auto">
            </a>
            <h2 class="mt-4 text-center text-xl font-bold tracking-tight text-[var(--color-text-primary)] transition-colors duration-300">
                Buat Akun Baru
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[400px]">
            <div class="bg-white dark:bg-[var(--color-bg-surface)] py-8 px-4 shadow-sm border border-black/10 dark:border-[#3A2E28] sm:rounded-[var(--radius-md)] sm:px-10 transition-colors duration-300">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-medium text-[var(--color-text-primary)] transition-colors duration-300">Nama Lengkap</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none transition-colors duration-300">
                        @error('name') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-[var(--color-text-primary)] transition-colors duration-300">Email</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none transition-colors duration-300">
                        @error('email') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium text-[var(--color-text-primary)] transition-colors duration-300">Nomor WhatsApp / HP</label>
                        <input id="phone" name="phone" type="text" placeholder="081234567890" value="{{ old('phone') }}"
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none transition-colors duration-300">
                        @error('phone') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-medium text-[var(--color-text-primary)] transition-colors duration-300">Password</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none transition-colors duration-300">
                        @error('password') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-[var(--color-text-primary)] transition-colors duration-300">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-white dark:bg-[var(--color-bg-base)] border border-black/10 dark:border-[#3A2E28] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none transition-colors duration-300">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full rounded-[var(--radius-sm)] bg-[var(--color-accent-gold)] px-4 py-2.5 text-sm font-semibold text-black hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-sm">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs transition-colors duration-300">
                    <span class="text-[var(--color-text-muted)]">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="font-semibold text-[var(--color-accent-gold)] hover:underline ml-1">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>

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