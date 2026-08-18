<x-layouts.storefront>
    <x-slot name="title">Daftar Akun Baru — Way Kopi</x-slot>

    <div class="min-h-[70vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <a href="{{ url('/') }}" class="inline-flex flex-col items-center group">
                <img src="{{ asset('images/logo.jpg') }}" alt="Way Kopi — Kopi Robusta Lampung" class="h-20 w-auto object-contain mx-auto">
            </a>
            <h2 class="mt-4 text-center text-xl font-bold tracking-tight text-[var(--color-text-primary)]">
                Buat Akun Baru
            </h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-[400px]">
            <div class="bg-[var(--color-bg-surface)] py-8 px-4 shadow-xl border border-[var(--color-coffee-brown)] sm:rounded-[var(--radius-md)] sm:px-10">
                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-medium text-[var(--color-text-primary)]">Nama Lengkap</label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none">
                        @error('name') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-medium text-[var(--color-text-primary)]">Email</label>
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none">
                        @error('email') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-medium text-[var(--color-text-primary)]">Nomor WhatsApp / HP</label>
                        <input id="phone" name="phone" type="text" placeholder="081234567890" value="{{ old('phone') }}"
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none">
                        @error('phone') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-medium text-[var(--color-text-primary)]">Password</label>
                        <input id="password" name="password" type="password" required
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none">
                        @error('password') <p class="mt-1 text-xs text-[var(--color-error)]">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-[var(--color-text-primary)]">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="mt-1 block w-full rounded-[var(--radius-sm)] bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] px-3 py-2 text-sm text-[var(--color-text-primary)] focus:border-[var(--color-accent-gold)] focus:outline-none">
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full rounded-[var(--radius-sm)] bg-[var(--color-accent-gold)] px-4 py-2.5 text-sm font-semibold text-[var(--color-bg-base)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs">
                    <span class="text-[var(--color-text-muted)]">Sudah punya akun?</span>
                    <a href="{{ route('login') }}" class="font-semibold text-[var(--color-accent-gold)] hover:underline ml-1">
                        Masuk
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.storefront>
