<div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 relative">
    <!-- Back to Blog Button -->
    <a href="{{ route('blog.index') }}" class="inline-flex items-center space-x-2 text-xs font-mono text-[var(--color-accent-gold)] hover:underline">
        <span>&larr; Kembali ke Jurnal Kopi</span>
    </a>

    <!-- Article Header -->
    <div class="space-y-4">
        @if($post->category)
            <span class="px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-full transition-colors duration-300">
                {{ $post->category }}
            </span>
        @endif

        <h1 class="font-display text-3xl md:text-5xl font-bold text-[var(--color-text-primary)] leading-tight transition-colors duration-300">
            {{ $post->title }}
        </h1>

        <div class="flex items-center space-x-4 pt-2 border-b border-black/10 dark:border-white/10 pb-4 font-mono text-xs text-[var(--color-text-muted)] transition-colors duration-300">
            <div class="w-10 h-10 rounded-full bg-[var(--color-accent-gold)]/20 border border-[var(--color-accent-gold)]/40 flex items-center justify-center font-bold text-[var(--color-accent-gold)]">
                {{ strtoupper(substr($post->author->name ?? 'W', 0, 1)) }}
            </div>
            <div>
                <strong class="text-[var(--color-text-primary)] block transition-colors duration-300">{{ $post->author->name ?? 'Tim Way Kopi' }}</strong>
                <span>Diterbitkan {{ $post->published_at ? $post->published_at->format('d MMMM Y') : 'Baru' }}</span>
            </div>
        </div>
    </div>

    <!-- Article Cover Image -->
    @php
        $cover = $post->cover_image_url ?: '/images/lampung_farmer.png';
    @endphp
    <div class="rounded-[var(--radius-lg)] overflow-hidden border border-black/10 dark:border-white/10 shadow-sm bg-[var(--color-bg-base)] max-h-[450px] transition-colors duration-300">
        <img src="{{ $cover }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
    </div>

    <!-- Excerpt / Highlight Quote -->
    @if($post->excerpt)
        <blockquote class="p-6 bg-[var(--color-bg-surface)] border-l-4 border-[var(--color-accent-gold)] rounded-r-[var(--radius-md)] text-sm md:text-base font-mono italic text-[var(--color-accent-gold)] leading-relaxed shadow-sm transition-colors duration-300">
            "{{ $post->excerpt }}"
        </blockquote>
    @endif

    <!-- Main Article Body -->
    <div class="prose prose-invert max-w-none font-mono text-xs md:text-sm text-[var(--color-text-primary)] leading-relaxed space-y-6 transition-colors duration-300">
        {!! nl2br(e($post->content)) !!}
    </div>

    <!-- CTA Banner to Storefront Catalog -->
    <div class="bg-[var(--color-bg-surface)] p-8 rounded-[var(--radius-lg)] border border-[var(--color-accent-gold)]/50 shadow-sm text-center space-y-4 transition-colors duration-300">
        <h3 class="font-display text-2xl font-bold text-[var(--color-text-primary)] transition-colors duration-300">
            Ingin Merasakan Sensasi Robusta Asli Tanggamus?
        </h3>
        <p class="text-xs font-mono text-[var(--color-text-muted)] max-w-xl mx-auto transition-colors duration-300">
            Biji kopi petik merah sangrai segar yang dikirim langsung dari perkebunan Lampung ke rumah kamu.
        </p>
        <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 font-bold font-mono text-xs bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-sm">
            Belanja Kopi Sangrai Sekarang &rarr;
        </a>
    </div>

    <!-- Related Articles Section -->
    @if($relatedPosts->count() > 0)
        <div class="pt-8 border-t border-black/10 dark:border-white/10 space-y-6 transition-colors duration-300">
            <h3 class="font-display font-bold text-xl text-[var(--color-text-primary)] transition-colors duration-300">
                Artikel Terkait Lainnya
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-mono text-xs">
                @foreach($relatedPosts as $rel)
                    <div class="bg-[var(--color-bg-surface)] p-4 rounded-[var(--radius-md)] border border-black/10 dark:border-white/10 space-y-2 hover:border-[var(--color-accent-gold)] transition-colors duration-300 shadow-sm">
                        <span class="text-[10px] text-[var(--color-accent-gold)]">{{ $rel->category ?? 'Artikel' }}</span>
                        <h4 class="font-bold text-[var(--color-text-primary)] text-sm line-clamp-2 transition-colors duration-300">
                            <a href="{{ route('blog.show', ['slug' => $rel->slug]) }}" class="hover:underline">
                                {{ $rel->title }}
                            </a>
                        </h4>
                        <p class="text-[var(--color-text-muted)] line-clamp-2 text-[11px] transition-colors duration-300">
                            {{ $rel->excerpt }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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