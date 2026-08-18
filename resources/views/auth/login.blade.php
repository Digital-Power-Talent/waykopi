<x-layouts.storefront>
    <x-slot name="title">Masuk — Way Kopi</x-slot>

    <div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <!-- Logo Centered -->
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center group">
                <img src="{{ asset('images/logo.jpg') }}" alt="Way Kopi — Kopi Robusta Lampung" class="h-20 w-auto object-contain mx-auto">
            </a>
            <h2 class="mt-4 text-center text-xl font-bold tracking-tight text-[var(--color-text-primary)]">
                Masuk ke Akun Kamu
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[400px]">
            <div class="bg-[var(--color-bg-surface)] py-8 px-4 shadow-xl border border-[var(--color-coffee-brown)] sm:rounded-[var(--radius-md)] sm:px-10">
                <!-- Session Status / Flash Messages -->
                @if (session('status'))
                    <div class="mb-4 text-xs font-medium text-[var(--color-success)] bg-[var(--color-bg-base)] p-3 rounded border border-[var(--color-success)]/30">
                        {{ session('status') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 text-xs font-medium text-[var(--color-error)] bg-[var(--color-bg-base)] p-3 rounded border border-[var(--color-error)]/30">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-medium text-[var(--color-text-primary)]">
                            Email
                        </label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] focus:border-[var(--color-accent-gold)] focus:outline-none focus:ring-1 focus:ring-[var(--color-accent-gold)]">
                        </div>
                        @error('email')
                            <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-medium text-[var(--color-text-primary)]">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs text-[var(--color-accent-gold)] hover:underline">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] placeholder-[var(--color-text-muted)] focus:border-[var(--color-accent-gold)] focus:outline-none focus:ring-1 focus:ring-[var(--color-accent-gold)]">
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox"
                            class="h-4 w-4 rounded border-[var(--color-coffee-brown)] bg-[var(--color-bg-base)] text-[var(--color-accent-gold)] focus:ring-[var(--color-accent-gold)]">
                        <label for="remember_me" class="ml-2 block text-xs text-[var(--color-text-muted)]">
                            Ingat saya
                        </label>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-[var(--radius-sm)] bg-[var(--color-accent-gold)] px-4 py-2.5 text-sm font-semibold text-[var(--color-bg-base)] shadow-sm hover:bg-[var(--color-accent-gold-bright)] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[var(--color-accent-gold)] transition-colors">
                            Masuk
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs">
                    <span class="text-[var(--color-text-muted)]">Belum punya akun?</span>
                    <a href="{{ route('register') }}" class="font-semibold text-[var(--color-accent-gold)] hover:underline ml-1">
                        Buat akun baru
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Newsletter Banner below Login Form (as per wireframe §6.6 & referensi pattern) -->
    <section class="bg-[var(--color-accent-gold)] py-10 px-4 mt-12 text-[var(--color-bg-base)]">
        <div class="max-w-[1280px] mx-auto flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            <div>
                <h3 class="font-display text-xl font-bold">Dapatkan Penawaran Batch Panen Perdana</h3>
                <p class="text-xs opacity-90 mt-1">Daftarkan email kamu untuk promo gratis ongkir & rilisan edisi terbatas dari petani Lampung.</p>
            </div>
            <form class="flex w-full md:w-auto gap-2" onsubmit="event.preventDefault(); text-xs;">
                <input type="email" placeholder="Alamat email kamu..." class="px-4 py-2.5 text-xs bg-[var(--color-bg-base)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none w-full md:w-64">
                <button type="submit" class="px-5 py-2.5 text-xs font-bold bg-[var(--color-bg-surface)] text-[var(--color-accent-gold)] rounded-[var(--radius-sm)] hover:bg-[var(--color-bg-base)] transition-colors whitespace-nowrap">
                    Daftar Sekarang
                </button>
            </form>
        </div>
    </section>
</x-layouts.storefront>
