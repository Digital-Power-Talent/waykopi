<div class="py-8 max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full space-y-10">
    <!-- Blog Header Banner -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="px-3 py-1 text-xs font-mono font-bold uppercase tracking-wider text-[var(--color-accent-gold)] bg-[var(--color-accent-gold)]/10 border border-[var(--color-accent-gold)]/30 rounded-full">
            Jurnal & Storytelling Way Kopi
        </span>
        <h1 class="font-display text-3xl md:text-5xl font-bold text-[var(--color-text-primary)] leading-tight">
            Cerita Kebun & Rahasia Kopi Robusta Lampung
        </h1>
        <p class="text-xs md:text-sm font-mono text-[var(--color-text-muted)] leading-relaxed">
            Menelusuri jejak dedikasi petani kopi Tanggamus, memahami proses pasca-panen, serta panduan menyeduh Robusta petik merah terbaik.
        </p>
    </div>

    <!-- Category Filter & Search Bar -->
    <div class="bg-[var(--color-bg-surface)] p-4 md:p-5 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] flex flex-col md:flex-row gap-4 items-center justify-between font-mono text-xs">
        <!-- Categories Chips -->
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="setCategory('')" class="px-4 py-2 font-bold rounded-[var(--radius-sm)] transition-colors {{ empty($selectedCategory) ? 'bg-[var(--color-accent-gold)] text-[var(--color-bg-base)]' : 'bg-[var(--color-bg-base)] text-[var(--color-text-muted)] border border-[var(--color-coffee-brown)] hover:text-[var(--color-text-primary)]' }}">
                Semua Artikel
            </button>
            @foreach($categories as $cat)
                <button wire:click="setCategory('{{ $cat }}')" class="px-4 py-2 font-bold rounded-[var(--radius-sm)] transition-colors {{ $selectedCategory === $cat ? 'bg-[var(--color-accent-gold)] text-[var(--color-bg-base)]' : 'bg-[var(--color-bg-base)] text-[var(--color-text-muted)] border border-[var(--color-coffee-brown)] hover:text-[var(--color-text-primary)]' }}">
                    {{ $cat }}
                </button>
            @endforeach
        </div>

        <!-- Search Input -->
        <div class="w-full md:w-72">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari artikel / kata kunci..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] rounded-[var(--radius-sm)] focus:outline-none focus:border-[var(--color-accent-gold)]">
        </div>
    </div>

    <!-- Featured Post Hero Card (If exists) -->
    @if($featuredPost && empty($selectedCategory) && empty($search) && $posts->currentPage() === 1)
        @php
            $featuredImg = $featuredPost->cover_image_url ?: '/images/lampung_farmer.png';
        @endphp
        <div class="bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-lg)] overflow-hidden shadow-2xl grid grid-cols-1 md:grid-cols-2 hover:border-[var(--color-accent-gold)] transition-all">
            <div class="h-64 md:h-full bg-[var(--color-bg-base)] overflow-hidden relative">
                <img src="{{ $featuredImg }}" alt="{{ $featuredPost->title }}" class="w-full h-full object-cover">
                <span class="absolute top-4 left-4 px-3 py-1 text-[10px] uppercase font-bold text-[var(--color-accent-gold)] bg-black/70 border border-[var(--color-accent-gold)]/40 rounded-full font-mono">
                    Utama • {{ $featuredPost->category ?? 'Cerita Kebun' }}
                </span>
            </div>

            <div class="p-6 md:p-10 flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <span class="text-xs font-mono text-[var(--color-text-muted)] block">
                        {{ $featuredPost->published_at ? $featuredPost->published_at->format('d M Y') : 'Baru' }} • Ditulis oleh {{ $featuredPost->author->name ?? 'Tim Way Kopi' }}
                    </span>
                    <h2 class="font-display font-bold text-2xl md:text-3xl text-[var(--color-text-primary)] leading-snug">
                        <a href="{{ route('blog.show', ['slug' => $featuredPost->slug]) }}" class="hover:text-[var(--color-accent-gold)] transition-colors">
                            {{ $featuredPost->title }}
                        </a>
                    </h2>
                    <p class="text-xs md:text-sm font-mono text-[var(--color-text-muted)] line-clamp-3 leading-relaxed">
                        {{ $featuredPost->excerpt ?: Str::limit(strip_tags($featuredPost->content), 160) }}
                    </p>
                </div>

                <div>
                    <a href="{{ route('blog.show', ['slug' => $featuredPost->slug]) }}" class="inline-block px-5 py-2.5 font-bold font-mono text-xs bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-colors">
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
                    <div class="bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-md)] overflow-hidden flex flex-col hover:border-[var(--color-accent-gold)] transition-all shadow-lg">
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
                                <span class="text-[10px] font-mono text-[var(--color-text-muted)] block">
                                    {{ $post->published_at ? $post->published_at->format('d M Y') : 'Baru' }}
                                </span>
                                <h3 class="font-display font-bold text-base text-[var(--color-text-primary)] line-clamp-2 leading-snug">
                                    <a href="{{ route('blog.show', ['slug' => $post->slug]) }}" class="hover:text-[var(--color-accent-gold)] transition-colors">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                <p class="text-xs font-mono text-[var(--color-text-muted)] line-clamp-3">
                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 120) }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-[var(--color-coffee-brown)]/40 flex items-center justify-between font-mono text-xs">
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
            <div class="text-center py-16 bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-md)] font-mono text-xs text-[var(--color-text-muted)] space-y-3">
                <p>Belum ada artikel yang sesuai dengan pencarian kamu.</p>
                <button wire:click="setCategory('')" class="px-4 py-2 bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] font-bold rounded">
                    Tampilkan Semua Artikel
                </button>
            </div>
        @endif
    </div>
</div>
