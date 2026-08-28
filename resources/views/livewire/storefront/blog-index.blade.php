<div class="py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full space-y-10 relative">
    <!-- Blog Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-full transition-colors duration-300">
            Jurnal & Storytelling Way Kopi
        </span>
        <h1 class="font-display text-3xl md:text-5xl font-bold text-[var(--color-text-primary)] leading-tight transition-colors duration-300">
            Cerita Kebun & Rahasia Kopi Robusta Lampung
        </h1>
        <p class="text-xs md:text-sm font-mono text-[var(--color-text-muted)] leading-relaxed transition-colors duration-300">
            Menelusuri jejak dedikasi petani kopi Tanggamus, memahami proses pasca-panen, serta panduan menyeduh Robusta petik merah terbaik.
        </p>
    </div>

    <!-- Category Filter & Search Bar -->
    <div class="bg-[var(--color-bg-surface)] p-4 md:p-5 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 flex flex-col md:flex-row gap-4 items-center justify-between font-mono text-xs transition-colors duration-300 shadow-sm">
        <!-- Categories Chips -->
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="setCategory('')" class="px-4 py-2 font-bold rounded-[var(--radius-sm)] transition-colors {{ empty($selectedCategory) ? 'bg-[var(--color-accent-gold)] text-black' : 'bg-[var(--color-bg-base)] text-[var(--color-text-muted)] border border-black/10 dark:border-white/10 hover:text-[var(--color-text-primary)]' }}">
                Semua Artikel
            </button>
            @foreach($categories as $cat)
                <button wire:click="setCategory('{{ $cat }}')" class="px-4 py-2 font-bold rounded-[var(--radius-sm)] transition-colors {{ $selectedCategory === $cat ? 'bg-[var(--color-accent-gold)] text-black' : 'bg-[var(--color-bg-base)] text-[var(--color-text-muted)] border border-black/10 dark:border-white/10 hover:text-[var(--color-text-primary)]' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari artikel / kata kunci..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-black/10 dark:border-white/10 text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)] transition-colors duration-300">
        </div>
    </div>

    <!-- Featured Post Hero Card -->
    @if($featuredPost && empty($selectedCategory) && empty($search) && $posts->currentPage() === 1)
        @php
            $featuredImg = $featuredPost->cover_image_url ?: '/images/lampung_farmer.png';
        @endphp
        <div class="bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 rounded-[var(--radius-lg)] overflow-hidden shadow-sm grid grid-cols-1 md:grid-cols-2 hover:border-[var(--color-accent-gold)] dark:hover:border-[var(--color-accent-gold)] transition-all duration-300 group">
            <div class="h-64 md:h-full bg-[var(--color-bg-base)] overflow-hidden relative">
                <img src="{{ $featuredImg }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                <span class="absolute top-4 left-4 px-3 py-1 text-[10px] uppercase font-bold text-[var(--color-accent-gold)] bg-black/70 border border-[var(--color-accent-gold)]/40 rounded-full font-mono">
                    Utama • {{ $featuredPost->category ?? 'Cerita Kebun' }}
                </span>
            </div>

            <div class="p-6 md:p-10 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-xs font-mono text-[var(--color-text-muted)] block transition-colors duration-300">
                        {{ $featuredPost->published_at ? $featuredPost->published_at->format('d M Y') : 'Baru' }} • Ditulis oleh {{ $featuredPost->author->name ?? 'Tim Way Kopi' }}
                    </span>
                    <h2 class="font-display font-bold text-2xl md:text-3xl text-[var(--color-text-primary)] leading-snug transition-colors duration-300">
                        <a href="{{ route('blog.show', ['slug' => $featuredPost->slug]) }}" class="hover:text-[var(--color-accent-gold)] transition-colors">
                            {{ $featuredPost->title }}
                        </a>
                    </h2>
                    <p class="text-xs md:text-sm font-mono text-[var(--color-text-muted)] line-clamp-3 leading-relaxed transition-colors duration-300">
                        {{ $featuredPost->excerpt ?: Str::limit(strip_tags($featuredPost->content), 160) }}
                    </p>
                </div>

                <div>
                    <a href="{{ route('blog.show', ['slug' => $featuredPost->slug]) }}" class="inline-block px-5 py-2.5 font-bold font-mono text-xs bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-sm">
                        Baca Selengkapnya &rarr;
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Articles Grid -->
    <div>
        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    @php
                        $img = $post->cover_image_url ?: '/images/coffee_roaster.png';
                    @endphp
                    <div class="bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 rounded-[var(--radius-md)] overflow-hidden flex flex-col hover:border-[var(--color-accent-gold)] dark:hover:border-[var(--color-accent-gold)] transition-all shadow-sm hover:shadow-md hover:-translate-y-1">
                        <div class="h-48 bg-[var(--color-bg-base)] overflow-hidden relative">
                            <img src="{{ $img }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            @if($post->category)
                                <span class="absolute top-3 left-3 px-2.5 py-1 text-[9px] uppercase font-bold text-[var(--color-accent-gold)] bg-black/80 border border-[var(--color-accent-gold)]/40 rounded font-mono">
                                    {{ $post->category }}
                                </span>
                            @endif
                        </div>

                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <span class="text-[10px] font-mono text-[var(--color-text-muted)] block transition-colors duration-300">
                                    {{ $post->published_at ? $post->published_at->format('d M Y') : 'Baru' }}
                                </span>
                                <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] line-clamp-2 leading-snug transition-colors duration-300">
                                    <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="hover:text-[var(--color-accent-gold)] transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-xs font-mono text-[var(--color-text-muted)] line-clamp-3 transition-colors duration-300">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between font-mono text-xs transition-colors duration-300">
                                <span class="text-[10px] text-[var(--color-text-muted)]">Oleh {{ $post->author->name ?? 'Tim Way Kopi' }}</span>
                                <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="text-[var(--color-accent-gold)] hover:underline font-bold">
                                    Baca &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $posts->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 rounded-[var(--radius-md)] font-mono text-xs text-[var(--color-text-muted)] space-y-3 transition-colors duration-300 shadow-sm">
                <p>Belum ada artikel yang sesuai dengan pencarian kamu.</p>
                <button wire:click="setCategory('')" class="px-4 py-2 bg-[var(--color-accent-gold)] text-black font-bold rounded">
                    Tampilkan Semua Artikel
                </button>
            </div>
        @endif
    </div>

    <!-- ========================================== -->
    <!-- MANUAL: TOMBOL TEMA & WHATSAPP MELAYANG -->
    <!-- ========================================== -->

    <!-- TOMBOL GANTI TEMA (KIRI BAWAH) -->
    <button onclick="toggleGlobalTheme()" 
            class="fixed bottom-6 left-6 z-50 p-3.5 rounded-full bg-[var(--color-bg-surface)] border border-black/10 dark:border-white/10 text-[var(--color-accent-gold)] shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center group cursor-pointer"
            title="Ubah Mode Gelap / Terang">
        <span id="global-mode-icon" class="text-base">🌙</span>
        <span class="absolute left-full ml-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-black/10 dark:border-white/10 uppercase tracking-wider pointer-events-none">
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
        <span class="absolute right-full mr-3 px-3 py-1.5 bg-[var(--color-bg-surface)] text-[var(--color-text-primary)] text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-black/10 dark:border-white/10 uppercase tracking-wider pointer-events-none">
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
</div>