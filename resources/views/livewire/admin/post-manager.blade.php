<div class="space-y-8">
    <!-- Top Action Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-display text-2xl font-bold text-[var(--color-text-primary)]">
                Manajemen Blog & Artikel
            </h1>
            <p class="text-xs font-mono text-[var(--color-text-muted)] mt-1">
                Kelola postingan cerita petani, edukasi seduh, dan kabar terbaru Way Kopi.
            </p>
        </div>

        <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2.5 bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] rounded-[var(--radius-sm)] font-mono font-bold text-xs hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-md uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            + Tambah Postingan Baru
        </button>
    </div>

    <!-- Flash Notification -->
    @if(session()->has('success'))
        <div class="p-4 rounded-[var(--radius-sm)] bg-emerald-950/60 border border-emerald-700/50 text-emerald-300 text-xs font-mono flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="font-bold text-emerald-400 hover:underline">&times;</button>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="bg-[var(--color-bg-surface)] p-4 rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] flex flex-col md:flex-row items-center justify-between gap-4 font-mono text-xs">
        <div class="w-full md:w-80 relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari judul atau isi postingan..." class="w-full pl-9 pr-4 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
            <svg class="w-4 h-4 text-[var(--color-text-muted)] absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <select wire:model.live="categoryFilter" class="px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                <option value="">Semua Kategori</option>
                <option value="Cerita Petani">Cerita Petani</option>
                <option value="Profil Sangrai">Profil Sangrai</option>
                <option value="Edukasi Kopi">Edukasi Kopi</option>
                <option value="Promo & Berita">Promo & Berita</option>
            </select>

            <select wire:model.live="statusFilter" class="px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                <option value="">Semua Status</option>
                <option value="published">Diterbitkan (Published)</option>
                <option value="draft">Draf (Draft)</option>
            </select>
        </div>
    </div>

    <!-- Blog Posts Table -->
    <div class="bg-[var(--color-bg-surface)] rounded-[var(--radius-md)] border border-[var(--color-coffee-brown)] overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left font-mono text-xs">
                <thead class="bg-[#1C1713] text-[var(--color-accent-gold)] uppercase tracking-wider border-b border-[var(--color-coffee-brown)]">
                    <tr>
                        <th class="py-3.5 px-4">Sampul</th>
                        <th class="py-3.5 px-4">Judul Artikel</th>
                        <th class="py-3.5 px-4">Kategori</th>
                        <th class="py-3.5 px-4">Penulis</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tanggal Terbit</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-coffee-brown)]/40">
                    @forelse($posts as $post)
                        <tr wire:key="post-row-{{ $post->id }}" class="hover:bg-[var(--color-bg-base)]/50 transition-colors">
                            <td class="py-3 px-4">
                                <div class="w-14 h-10 rounded overflow-hidden border border-[var(--color-coffee-brown)] bg-[var(--color-bg-base)]">
                                    <img src="{{ $post->cover_image_url ?? '/images/lampung_farmer.png' }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-[var(--color-text-primary)] hover:text-[var(--color-accent-gold)] transition-colors">
                                    {{ $post->title }}
                                </p>
                                <p class="text-[10px] text-[var(--color-text-muted)] truncate max-w-xs">
                                    {{ $post->excerpt }}
                                </p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold bg-[var(--color-accent-gold)]/10 text-[var(--color-accent-gold)] border border-[var(--color-accent-gold)]/30 rounded-full">
                                    {{ $post->category }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-[var(--color-text-muted)]">
                                {{ $post->author->name ?? 'Admin' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($post->status === 'published')
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-emerald-950/60 text-emerald-400 border border-emerald-700/50 rounded-full">
                                        Published
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase bg-amber-950/60 text-amber-400 border border-amber-700/50 rounded-full">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-[var(--color-text-muted)]">
                                {{ $post->published_at ? $post->published_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button wire:click="openEditModal({{ $post->id }})" class="px-2.5 py-1 text-[11px] font-bold bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] text-[var(--color-text-primary)] hover:border-[var(--color-accent-gold)] hover:text-[var(--color-accent-gold)] rounded transition-colors">
                                    Edit
                                </button>
                                <button wire:click="deletePost({{ $post->id }})" onclick="return confirm('Apakah Anda yakin ingin menghapus postingan ini?')" class="px-2.5 py-1 text-[11px] font-bold bg-rose-950/40 border border-rose-800/50 text-rose-300 hover:bg-rose-900/60 rounded transition-colors">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[var(--color-text-muted)]">
                                Tidak ada postingan yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-[var(--color-coffee-brown)]">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-[var(--color-bg-surface)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-md)] w-full max-w-3xl overflow-hidden shadow-2xl space-y-6">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-[var(--color-coffee-brown)] flex items-center justify-between bg-[#1C1713]">
                    <h3 class="font-display font-bold text-lg text-[var(--color-accent-gold)]">
                        {{ $editingPostId ? 'Edit Postingan Artikel' : 'Tambah Postingan Artikel Baru' }}
                    </h3>
                    <button wire:click="closeModal" class="text-[var(--color-text-muted)] hover:text-white font-bold text-lg">&times;</button>
                </div>

                <!-- Modal Body Form -->
                <form wire:submit.prevent="savePost" class="p-6 space-y-5 font-mono text-xs">
                    <!-- Title -->
                    <div>
                        <label class="block uppercase font-bold text-[var(--color-text-muted)] mb-1">Judul Artikel <span class="text-rose-400">*</span></label>
                        <input wire:model="title" type="text" placeholder="Contoh: Rahasia Seduh Robusta Tanggamus Murni" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        @error('title') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Category & Status Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block uppercase font-bold text-[var(--color-text-muted)] mb-1">Kategori Artikel <span class="text-rose-400">*</span></label>
                            <select wire:model="category" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                                <option value="Cerita Petani">Cerita Petani</option>
                                <option value="Profil Sangrai">Profil Sangrai</option>
                                <option value="Edukasi Kopi">Edukasi Kopi</option>
                                <option value="Promo & Berita">Promo & Berita</option>
                            </select>
                            @error('category') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block uppercase font-bold text-[var(--color-text-muted)] mb-1">Status Publikasi <span class="text-rose-400">*</span></label>
                            <select wire:model="status" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                                <option value="published">Diterbitkan (Published)</option>
                                <option value="draft">Draf (Draft)</option>
                            </select>
                            @error('status') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Cover Image URL & Presets -->
                    <div>
                        <label class="block uppercase font-bold text-[var(--color-text-muted)] mb-1">URL Gambar Sampul (Cover Image)</label>
                        <input wire:model="coverImageUrl" type="text" placeholder="/images/lampung_farmer.png" class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]">
                        
                        <!-- Presets -->
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-[10px]">
                            <span class="text-[var(--color-text-muted)]">Pilih Preset Gambar:</span>
                            <button type="button" wire:click="$set('coverImageUrl', '/images/lampung_farmer.png')" class="px-2 py-0.5 bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] rounded hover:border-[var(--color-accent-gold)] text-[var(--color-text-primary)]">🌾 Petani Tanggamus</button>
                            <button type="button" wire:click="$set('coverImageUrl', '/images/coffee_roaster.png')" class="px-2 py-0.5 bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] rounded hover:border-[var(--color-accent-gold)] text-[var(--color-text-primary)]">🔥 Mesin Sangrai</button>
                            <button type="button" wire:click="$set('coverImageUrl', '/images/hero_coffee_cup.png')" class="px-2 py-0.5 bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] rounded hover:border-[var(--color-accent-gold)] text-[var(--color-text-primary)]">☕ Cangkir Espresso</button>
                            <button type="button" wire:click="$set('coverImageUrl', '/images/coffee_beans.png')" class="px-2 py-0.5 bg-[var(--color-coffee-brown)]/40 border border-[var(--color-coffee-brown)] rounded hover:border-[var(--color-accent-gold)] text-[var(--color-text-primary)]">🫘 Biji Sangrai</button>
                        </div>
                        @error('coverImageUrl') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Excerpt -->
                    <div>
                        <label class="block uppercase font-bold text-[var(--color-text-muted)] mb-1">Kutipan Singkat (Excerpt) <span class="text-rose-400">*</span></label>
                        <textarea wire:model="excerpt" rows="2" placeholder="Ringkasan 1-2 kalimat untuk ditampilkan di kartu artikel..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)]"></textarea>
                        @error('excerpt') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Content -->
                    <div>
                        <label class="block uppercase font-bold text-[var(--color-text-muted)] mb-1">Isi Artikel Lengkap (Content) <span class="text-rose-400">*</span></label>
                        <textarea wire:model="content" rows="8" placeholder="Tuliskan isi cerita atau artikel lengkap di sini..." class="w-full px-3 py-2 bg-[var(--color-bg-base)] border border-[var(--color-coffee-brown)] rounded-[var(--radius-sm)] text-[var(--color-text-primary)] focus:outline-none focus:border-[var(--color-accent-gold)] leading-relaxed"></textarea>
                        @error('content') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-[var(--color-coffee-brown)]">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border border-[var(--color-coffee-brown)] text-[var(--color-text-muted)] hover:text-white rounded-[var(--radius-sm)]">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-[var(--color-accent-gold)] text-[var(--color-bg-base)] font-bold rounded-[var(--radius-sm)] hover:bg-[var(--color-accent-gold-bright)] transition-all shadow-md">
                            Simpan Artikel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
