<x-layouts.storefront title="Way Kopi — Kopi Robusta Lampung Langsung dari Kebun Petani">
    
    <!-- 1. HERO BANNER UTAMA -->
    <section class="relative w-full bg-surface border-b border-custom overflow-hidden group transition-colors duration-300">
        <!-- PERBAIKAN: Menggunakan aspect-video untuk HP agar gambar tidak terpotong -->
        <div class="relative w-full aspect-video sm:aspect-auto sm:h-[380px] overflow-hidden">
            <!-- PERBAIKAN: Menggunakan object-left untuk HP agar teks Way Kopi di kiri tidak terpotong -->
            <img src="/images/banner_waykopi.png" alt="Way Kopi Robusta Lampung" class="w-full h-full object-cover object-left sm:object-center transition-transform duration-1000 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        </div>
        
        <!-- Floating Quick Action CTA -->
        <div class="absolute bottom-4 right-4 sm:bottom-6 sm:right-8 z-20">
            <!-- PERBAIKAN: Teks dipersingkat menjadi "Belanja Koleksi" agar lebih pas di layar HP -->
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 sm:px-6 sm:py-3 text-[10px] sm:text-xs font-bold font-mono bg-[var(--color-accent-gold)] text-black rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-xl uppercase tracking-widest flex items-center gap-2 border border-[var(--color-accent-gold)]">
                <span>Belanja Koleksi</span>
                <span>&rarr;</span>
            </a>
        </div>
    </section>

    <!-- 2. TRUST BADGES BAR -->
    <section class="bg-surface border-b border-custom py-4 sm:py-3 px-4 sm:px-8 shadow-inner transition-colors duration-300">
        <!-- PERBAIKAN: Menjadi grid-cols-1 di HP agar menumpuk rapi ke bawah -->
        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-y-3 sm:gap-4 text-center font-mono text-[11px] sm:text-xs uppercase tracking-wider text-main">
            <!-- PERBAIKAN: Border disesuaikan agar rapi di HP (border-b) dan kembali jadi (border-r) di laptop -->
            <div class="flex items-center justify-center space-x-2 pb-3 sm:pb-0 sm:py-1 border-b sm:border-b-0 sm:border-r border-custom">
                <span class="text-[var(--color-accent-gold)] font-bold text-sm sm:text-base">✓</span>
                <span>100% Fine Robusta Lampung</span>
            </div>
            <div class="flex items-center justify-center space-x-2 pb-3 sm:pb-0 sm:py-1 border-b md:border-b-0 md:border-r border-custom">
                <span class="text-[var(--color-accent-gold)] font-bold text-sm sm:text-base">🌱</span>
                <span>Direct Trade dari Petani</span>
            </div>
            <div class="flex items-center justify-center space-x-2 pb-3 sm:pb-0 sm:py-1 border-b sm:border-b-0 sm:border-r border-custom">
                <span class="text-[var(--color-accent-gold)] font-bold text-sm sm:text-base">🔥</span>
                <span>Freshly Artisan Roasted</span>
            </div>
            <div class="flex items-center justify-center space-x-2 sm:py-1">
                <span class="text-[var(--color-accent-gold)] font-bold text-sm sm:text-base">🛡️</span>
                <span>Garansi Kualitas Terjamin</span>
            </div>
        </div>
    </section>

    <!-- 3. KONTEN UTAMA -->
    <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 w-full pt-10 sm:pt-12 pb-16 space-y-12 sm:space-y-16">
        
        <!-- Katalog Produk Kopi -->
        <div>
            <div class="mb-6 sm:mb-8 flex flex-col sm:flex-row sm:items-end justify-between border-b border-custom pb-4 transition-colors duration-300">
                <div>
                    <span class="text-[10px] sm:text-xs font-mono uppercase tracking-widest text-[var(--color-accent-gold)] font-bold block mb-1 sm:mb-2">Pilihan Terbaik Roastery</span>
                    <h2 class="font-display text-2xl sm:text-4xl font-bold text-main">Katalog Kopi Robusta</h2>
                </div>
                <a href="{{ route('products.index') }}" class="mt-4 sm:mt-0 font-mono text-[11px] sm:text-xs uppercase tracking-wider text-[var(--color-accent-gold)] hover:underline font-bold flex items-center space-x-1">
                    <span>Lihat Semua Katalog</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- KOMPONEN FILTER ADA DI SINI -->
            @livewire('storefront.product-catalog', ['showHero' => false])
        </div>

        <!-- 4. SECTION: MENGAPA MEMILIH WAY KOPI -->
        <section class="py-8 border-t border-b border-custom my-10 transition-colors duration-300">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12 space-y-3 px-2 sm:px-0">
                <span class="text-[10px] sm:text-xs font-mono uppercase tracking-widest font-bold text-[var(--color-accent-gold)] bg-surface px-4 py-1.5 rounded-full border border-custom inline-block shadow-sm">
                    Standar Kualitas Tertinggi
                </span>
                <h2 class="font-display text-2xl sm:text-4xl font-bold text-main leading-tight">
                    Mengapa Way Kopi Begitu Istimewa?
                </h2>
                <p class="text-[11px] sm:text-sm text-muted font-mono leading-relaxed mt-2">
                    Didedikasikan untuk mengangkat derajat biji kopi Robusta Tanggamus langsung ke cangkir seduhan Anda dengan standar kurasi tanpa kompromi.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <!-- Card 1 -->
                <div class="bg-surface p-5 sm:p-8 rounded-[var(--radius-md)] border border-custom hover:border-[var(--color-accent-gold)] transition-all duration-300 group shadow-sm hover:-translate-y-1">
                    <div class="w-full h-48 sm:h-44 mb-5 sm:mb-6 rounded-[var(--radius-sm)] overflow-hidden border border-custom relative shadow-inner">
                        <img src="/images/lampung_farmer.png" alt="Petani Kopi Tanggamus" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-3 left-3 bg-surface/90 px-3 py-1 rounded text-[10px] font-mono font-bold text-[var(--color-accent-gold)] uppercase tracking-wider border border-custom">
                            Petik Merah
                        </div>
                    </div>
                    <h3 class="font-display font-bold text-lg sm:text-xl text-main mb-2">100% Seleksi Petik Merah</h3>
                    <p class="text-[11px] sm:text-xs font-mono text-muted leading-relaxed">
                        Hanya buah kopi matang sempurna dari lereng Gunung Tanggamus yang dipetik manual untuk menjamin manis alami & crema tebal yang pekat.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="bg-surface p-5 sm:p-8 rounded-[var(--radius-md)] border border-custom hover:border-[var(--color-accent-gold)] transition-all duration-300 group shadow-sm hover:-translate-y-1">
                    <div class="w-full h-48 sm:h-44 mb-5 sm:mb-6 rounded-[var(--radius-sm)] overflow-hidden border border-custom relative shadow-inner">
                        <img src="/images/products/produk-utama.jpg" alt="Perdagangan Etis" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-3 left-3 bg-surface/90 px-3 py-1 rounded text-[10px] font-mono font-bold text-[var(--color-accent-gold)] uppercase tracking-wider border border-custom">
                            Fair Trade
                        </div>
                    </div>
                    <h3 class="font-display font-bold text-lg sm:text-xl text-main mb-2">Perdagangan Etis & Transparan</h3>
                    <p class="text-[11px] sm:text-xs font-mono text-muted leading-relaxed">
                        Kami membeli hasil panen langsung dari kelompok tani mitra di Lampung dengan harga yang adil guna meningkatkan kesejahteraan petani lokal.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="bg-surface p-5 sm:p-8 rounded-[var(--radius-md)] border border-custom hover:border-[var(--color-accent-gold)] transition-all duration-300 group shadow-sm hover:-translate-y-1">
                    <div class="w-full h-48 sm:h-44 mb-5 sm:mb-6 rounded-[var(--radius-sm)] overflow-hidden border border-custom relative shadow-inner">
                        <img src="/images/coffee_roaster.png" alt="Artisan Roasting" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-3 left-3 bg-surface/90 px-3 py-1 rounded text-[10px] font-mono font-bold text-[var(--color-accent-gold)] uppercase tracking-wider border border-custom">
                            Artisan Roasting
                        </div>
                    </div>
                    <h3 class="font-display font-bold text-lg sm:text-xl text-main mb-2">Freshly Artisan Roasted</h3>
                    <p class="text-[11px] sm:text-xs font-mono text-muted leading-relaxed">
                        Kopi disangrai secara berkala dalam batch kecil dengan presisi tinggi untuk menjaga kesegaran dan cita rasa autentik khas Robusta.
                    </p>
                </div>
            </div>
        </section>

        <!-- 5. TESTIMONIAL SECTION -->
        <section class="py-8 sm:py-10 bg-surface border border-custom rounded-[var(--radius-lg)] p-6 sm:p-12 text-center space-y-4 sm:space-y-6 shadow-sm relative overflow-hidden transition-colors duration-300">
            <div class="absolute inset-0 z-0 opacity-10 mix-blend-luminosity pointer-events-none">
                <img src="/images/hero_coffee_cup.png" alt="Crema Coffee Cup" class="w-full h-full object-cover">
            </div>
            <div class="relative z-10 space-y-4 sm:space-y-6 max-w-3xl mx-auto">
                <div class="inline-flex items-center space-x-1 text-[var(--color-accent-gold)] text-base sm:text-lg">
                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                </div>
                <span class="text-[10px] sm:text-xs font-mono uppercase font-bold text-[var(--color-accent-gold)] tracking-widest block">
                    Ulasan Pelanggan Setia
                </span>
                <blockquote class="font-display font-bold text-lg sm:text-3xl text-main leading-relaxed italic px-2">
                    "Kopi Robusta terbaik yang pernah saya seduh! Mantap, tidak asam, aromanya gurih karamel dan crema-nya tebal banget."
                </blockquote>
                <div class="pt-2">
                    <p class="text-[11px] sm:text-xs font-mono font-bold text-main uppercase tracking-wider">
                        Hendra Kurniawan
                    </p>
                    <p class="text-[10px] sm:text-[11px] font-mono text-muted mt-1">
                        Penikmat & Praktisi Espresso — Bogor
                    </p>
                </div>
            </div>
        </section>

    </div>

    <!-- ========================================== -->
    <!-- MANUAL: TOMBOL TEMA & WHATSAPP MELAYANG -->
    <!-- ========================================== -->

    <!-- TOMBOL GANTI TEMA (KIRI BAWAH) -->
    <button onclick="toggleDarkMode()" 
            class="fixed bottom-4 left-4 sm:bottom-6 sm:left-6 z-50 p-3 sm:p-3.5 rounded-full bg-surface border border-custom text-[var(--color-accent-gold)] shadow-2xl hover:scale-110 transition-all duration-300 flex items-center justify-center group cursor-pointer"
            title="Ubah Mode Gelap / Terang">
        <span id="mode-icon" class="text-sm sm:text-base">🌙</span>
        <span class="absolute left-full ml-3 px-3 py-1.5 bg-surface text-main text-[10px] sm:text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-custom uppercase tracking-wider pointer-events-none">
            Ubah Tema
        </span>
    </button>

    <!-- TOMBOL WHATSAPP (KANAN BAWAH) -->
    <a href="https://wa.me/6281234567890?text=Halo%20Way%20Kopi,%20saya%20tertarik%20untuk%20memesan%20kopi." 
       target="_blank" 
       class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-50 bg-green-600 hover:bg-green-700 text-white p-3.5 sm:p-4 rounded-full shadow-2xl flex items-center justify-center transition-all duration-300 hover:scale-110 group cursor-pointer"
       title="Chat WhatsApp CS Way Kopi">
        <svg class="w-6 h-6 sm:w-7 sm:h-7 fill-current" viewBox="0 0 24 24">
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
        </svg>
        <span class="absolute right-full mr-3 px-3 py-1.5 bg-surface text-main text-[10px] sm:text-xs font-mono rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap border border-custom uppercase tracking-wider pointer-events-none">
            Chat WhatsApp CS
        </span>
    </a>

    <!-- SISTEM PENGATUR WARNA DINAMIS (CSS VARIABLES) -->
    <style>
        :root {
            --bg-base: #120F0D;
            --bg-surface: #18120F;
            --text-main: #EADBC8;
            --text-muted: #A69C8D;
            --border-custom: #3A2E28;
        }

        body.light-mode {
            --bg-base: #F9F6F0;
            --bg-surface: #FFFFFF;
            --text-main: #2C221E;
            --text-muted: #6B5B53;
            --border-custom: #E2D4CC;
        }

        body {
            background-color: var(--bg-base) !important;
            color: var(--text-main) !important;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .bg-surface {
            background-color: var(--bg-surface) !important;
        }

        .border-custom {
            border-color: var(--border-custom) !important;
        }

        .text-main {
            color: var(--text-main) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }
    </style>

    <!-- SCRIPT GANTI TEMA SINKRON & AMAN -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('waykopi_theme');
            const icons = [document.getElementById('global-mode-icon'), document.getElementById('mode-icon')];
            if (savedTheme === 'light') {
                document.body.classList.add('light-mode');
                document.documentElement.classList.remove('dark');
                icons.forEach(icon => { if (icon) icon.innerText = '☀️'; });
            } else {
                document.body.classList.remove('light-mode');
                document.documentElement.classList.add('dark');
                icons.forEach(icon => { if (icon) icon.innerText = '🌙'; });
            }
        })();

        function toggleGlobalTheme() {
            toggleDarkMode();
        }

        function toggleDarkMode() {
            const body = document.body;
            const icons = [document.getElementById('global-mode-icon'), document.getElementById('mode-icon')];
            
            if (body.classList.contains('light-mode')) {
                body.classList.remove('light-mode');
                document.documentElement.classList.add('dark');
                localStorage.setItem('waykopi_theme', 'dark');
                icons.forEach(icon => { if (icon) icon.innerText = '🌙'; });
            } else {
                body.classList.add('light-mode');
                document.documentElement.classList.remove('dark');
                localStorage.setItem('waykopi_theme', 'light');
                icons.forEach(icon => { if (icon) icon.innerText = '☀️'; });
            }
        }
    </script>
</x-layouts.storefront>