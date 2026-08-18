<div class="py-8 max-w-4xl mx-auto space-y-10">
    <!-- Back to Blog Button -->
    <a href="{{ route('blog.index') }}" class="inline-flex items-center space-x-2 text-xs font-mono text-[var(--color-accent-gold)] hover:underline">
        <span>&larr; Kembali ke Jurnal Kopi</span>
    </a>

    <!-- Article Header -->
    <div class="space-y-4">
        @if($post->category)
            <span class="px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-full">
                {{ $post->category }}
            </span>
        @endif

        <h1 class="font-display text-3xl md:text-5xl font-bold text-[var(--color-text-primary)] leading-tight">
            {{ $post->title }}
        </h1>

        <div class="flex items-center space-x-4 pt-2 border-b border-[var(--color-coffee-brown)]/40 pb-4 font-mono text-xs text-[var(--color-text-muted)]">
            <div class="w-10 h-10 rounded-full bg-[var(--color-accent-gold)]/20 border border-[var(--color-accent-gold)]/40 flex items-center justify-center font-bold text-[var(--color-accent-gold)]">
                {{ strtoupper(substr($post->author->name ?? 'W', 0, 1)) }}
            </div>
            <div>
                <strong class="text-[var(--color-text-primary)] block">{{ $post->author->name ?? 'Tim Way Kopi' }}</strong>
                <span>Diterbitkan {{ $post->published_at ? $post->published_at->format('d MMMM Y') : 'Baru' }}</span>
            </div>
        </div>
    </div>

    <!-- Article Cover Image -->
    @php
        $cover = $post->cover_image_url ?: '/images/lampung_farmer.png';
    @endphp
    <div class="rounded-[var(--radius-lg)] overflow-hidden border border-[var(--color-coffee-brown)] shadow-2xl bg-[var(--color-bg-base)] max-h-[450px]">
        <img src="{{ $cover }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
    </div>

    <!-- Excerpt / Highlight Quote -->
    @if($post->excerpt)
        <blockquote class="p-6 bg-[var(--color-bg-surface)] border-l-4 border-[var(--color-accent-gold)] rounded-r-[var(--radius-md)] text-sm md:text-base font-mono italic text-[var(--color-accent-gold)] leading-relaxed shadow-lg">
            "{{ $post->excerpt }}"
        </blockquote>
    @endif

    <!-- Main Article Body -->
    <div class="prose prose-invert max-w-none font-mono text-xs md:text-sm text-[var(--color-text-primary)] leading-relaxed space-y-6">
        {!! nl2br(e($post->content)) !!}
    </div>

    <!-- CTA Banner to Storefront Catalog -->
    <div class="bg-[var(--color-bg-surface)] p-8 rounded-[var(--radius-lg)] border border-[var(--color-accent-gold)]/50 shadow-2xl text-center space-y-4">
        <h3 class="font-display text-2xl font-bold text-[var(--color-text-primary)]">
            Ingin Merasakan Sensasi Robusta Asli Tanggamus?
        </h3>
        <p class="text-xs font-mono text-[var(--color-text-muted)] max-w-xl mx-auto">
            Biji kopi petik merah sangrai segar yang dikirim langsung dari perkebunan Lampung ke rumah kamu.
        </p>
        <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 font-bold font-mono text-xs bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors shadow-lg">
            Belanja Kopi Sangrai Sekarang &rarr;
        </a>
    </div>

    <!-- Related Articles Section -->
    @if($relatedPosts->count() > 0)
        <div class="pt-8 border-t border-[var(--color-coffee-brown)] space-y-6">
            <h3 class="font-display font-bold text-xl text-[var(--color-text-primary)]">
                Artikel Terkait Lainnya
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 font-mono text-xs">
                @foreach($relatedPosts as $rel)
                    <div class="bg-[var(--color-bg-surface)] p-4 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] space-y-2 hover:border-[var(--color-accent-gold)] transition-colors">
                        <span class="text-[10px] text-[var(--color-accent-gold)]">{{ $rel->category ?? 'Artikel' }}</span>
                        <h4 class="font-bold text-[var(--color-text-primary)] text-sm line-clamp-2">
                            <a href="{{ route('blog.show', ['slug' => $rel->slug]) }}" class="hover:underline">
                                {{ $rel->title }}
                            </a>
                        </h4>
                        <p class="text-[var(--color-text-muted)] line-clamp-2 text-[11px]">
                            {{ $rel->excerpt }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
