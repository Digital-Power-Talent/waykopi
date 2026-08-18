<x-layouts.storefront title="Way Kopi — Kopi Robusta Lampung Langsung dari Kebun Petani">
    <!-- Full Width Main Website Banner (Edge-to-Edge 100% Width) -->
    <section class="relative w-full overflow-hidden bg-[#0d0b09] border-b border-[var(--color-coffee-brown)]/80 shadow-2xl group">
        <div class="w-full overflow-hidden">
            <img src="/images/banner_waykopi.png" alt="Way Kopi Robusta Lampung — Dari Lampung, Untuk Indonesia" class="w-full h-auto object-cover block transition-transform duration-700 group-hover:scale-[1.005]">
        </div>
        
        <!-- Floating Quick Action CTA Button -->
        <div class="absolute bottom-4 right-4 sm:bottom-8 sm:right-10 z-10 flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 sm:px-7 sm:py-3.5 text-xs sm:text-sm font-bold font-mono bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-2xl uppercase tracking-wider flex items-center gap-2 border border-[var(--color-accent-gold)]">
                <span>Belanja Sekarang</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

    <!-- Centered Container for Catalogue & Lower Sections -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full pt-10 pb-16 space-y-16">
        <!-- Product Catalog Section Component -->
        @livewire('storefront.product-catalog', ['showHero' => false])

        <!-- Why Way Kopi Section -->
        <section class="py-6 border-t border-[var(--color-coffee-brown)]/40">
            <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
                <span class="text-xs font-mono uppercase font-bold text-[var(--color-accent-gold)]">Keunggulan Kopi Robusta Lampung</span>
                <h2 class="font-display text-3xl font-bold text-[var(--color-text-primary)]">
                    Mengapa Memilih Way Kopi?
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div class="bg-[var(--color-bg-surface)] p-8 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)] transition-all group shadow-xl">
                    <div class="w-full h-44 mb-6 rounded-[var(--radius-sm)] overflow-hidden border border-[var(--color-coffee-brown)] relative">
                        <img src="/images/lampung_farmer.png" alt="Petani Kopi Tanggamus Lampung" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] mb-2">100% Petik Merah</h3>
                    <p class="text-xs font-mono text-[var(--color-text-muted)] leading-relaxed">
                        Hanya biji kopi matang sempurna dari Tanggamus yang dipetik manual oleh petani lokal untuk menjamin manis alami & crema tebal.
                    </p>
                </div>

                <div class="bg-[var(--color-bg-surface)] p-8 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)] transition-all group shadow-xl">
                    <div class="w-full h-44 mb-6 rounded-[var(--radius-sm)] overflow-hidden border border-[var(--color-coffee-brown)] relative">
                        <img src="/images/coffee_beans.png" alt="Biji Kopi Robusta Sangrai" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] mb-2">Perdagangan Etis & Fair Trade</h3>
                    <p class="text-xs font-mono text-[var(--color-text-muted)] leading-relaxed">
                        Kami membeli hasil panen langsung dari kelompok tani mitra di Lampung dengan harga yang adil dan transparan.
                    </p>
                </div>

                <div class="bg-[var(--color-bg-surface)] p-8 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] hover:border-[var(--color-accent-gold)] transition-all group shadow-xl">
                    <div class="w-full h-44 mb-6 rounded-[var(--radius-sm)] overflow-hidden border border-[var(--color-coffee-brown)] relative">
                        <img src="/images/coffee_roaster.png" alt="Mesin Sangrai Kopi Way Kopi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h3 class="font-display font-bold text-lg text-[var(--color-text-primary)] mb-2">Freshly Artisan Roasted</h3>
                    <p class="text-xs font-mono text-[var(--color-text-muted)] leading-relaxed">
                        Kopi disangrai secara berkala dalam batch kecil presisi tinggi untuk menjaga aroma segar khas Robusta sampai di meja kamu.
                    </p>
                </div>
            </div>
        </section>

        <!-- Testimonial Section (What People Are Saying - Death Wish Style) -->
        <section class="py-10 bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-lg)] p-8 md:p-12 text-center space-y-6 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 z-0 opacity-10 mix-blend-luminosity">
                <img src="/images/hero_coffee_cup.png" alt="Crema Coffee Cup" class="w-full h-full object-cover">
            </div>
            <div class="relative z-10 space-y-6">
                <span class="text-xs font-mono uppercase font-bold text-[var(--color-accent-gold)] tracking-widest">
                    WHAT PEOPLE ARE SAYING
                </span>
                <blockquote class="font-display font-bold text-2xl md:text-4xl text-[var(--color-text-primary)] max-w-4xl mx-auto leading-tight">
                    "Kopi Robusta terbaik yang pernah saya seduh! Mantap, tidak asam, aromanya gurih karamel dan crema-nya tebal banget."
                </blockquote>
                <p class="text-xs font-mono text-[var(--color-text-muted)] uppercase tracking-wider">
                    — Hendra K., Penikmat Kopi Espresso Bogor ⭐⭐⭐⭐⭐
                </p>
            </div>
        </section>
    </div>
</x-layouts.storefront>
