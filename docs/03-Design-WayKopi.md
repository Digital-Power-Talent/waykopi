# Design Document — Way Kopi E-Commerce

**Versi:** 0.1 (Draft)
**Tanggal:** 30 Juli 2026
**Referensi:** 01-PRD-WayKopi.md, 02-Architecture-WayKopi.md

---

## 1. Design Direction & Brand Grounding

Diturunkan langsung dari brand asset yang sudah ada (logo + kemasan produk):
- **Latar gelap bertekstur batu** (near-black, bukan flat black) — kesan premium, "malam di kebun kopi"
- **Display serif elegan dengan swash** untuk wordmark ("Way Kopi") — karakter classic/heritage
- **Script emas/tan** untuk sub-label ("Kopi Robusta") — sentuhan hand-crafted, bukan korporat
- **Fotografi produk gelap & moody** dengan highlight hangat (biji kopi, daun, cherry merah kopi)

**Satu kalimat arah desain:** situs terasa seperti membuka kemasan kopi premium di malam hari — gelap, hangat, taktil, dengan cerita petani Lampung sebagai jantung narasi, bukan sekadar katalog produk.

**Signature element:** garis kontur peta Lampung yang di-trace tipis (hairline, warna emas pudar) sebagai elemen background berulang di section "asal kopi" — bukan dekorasi generik, tapi literal menunjuk lokasi panen. Dipakai satu kali dengan disiplin, tidak diulang di setiap section.

> **Referensi struktur (bukan visual):** beberapa pola struktur halaman (promo bar, filter katalog, carousel New Arrivals/Best Sellers, testimonial carousel, listing blog, layout login page + newsletter banner) diadopsi dari analisis referensi UX e-commerce kopi yang diberikan klien — pola-pola ini umum dipakai lintas platform Shopify/e-commerce, bukan elemen visual eksklusif milik brand tertentu. **Palet warna, tipografi, ikonografi, dan tone brand tetap 100% mengikuti identitas Way Kopi** (§2), tidak meniru skema warna/branding brand lain.

> ⚠️ Dihindari secara sadar: warna terracotta `#D97757` (default umum AI-generated design) dan pola numbered marker (01/02/03) generik yang tidak actually merepresentasikan sequence nyata.

---

## 2. Design Tokens

### 2.1 Color Palette

| Token | Hex | Penggunaan |
|---|---|---|
| `--color-bg-base` | `#100E0C` | Background utama (near-black, warm undertone, bukan pure black) |
| `--color-bg-surface` | `#1C1712` | Card, section alternatif, elevasi ringan |
| `--color-text-primary` | `#F5F1E8` | Teks utama (cream, bukan pure white — konsisten dgn logo) |
| `--color-text-muted` | `#A69C8D` | Teks sekunder, caption, meta info |
| `--color-accent-gold` | `#C8A050` | Aksen utama (harga, CTA border, highlight) — diturunkan dari warna script logo |
| `--color-accent-gold-bright` | `#E4C374` | Hover state, highlight aktif |
| `--color-coffee-brown` | `#3D2B1F` | Aksen sekunder (badge, divider tebal) |
| `--color-success` | `#7A9B76` | Status paid/delivered (muted green, tidak neon) |
| `--color-error` | `#B85C50` | Error/gagal (muted red-brown, konsisten dgn palet, bukan merah stok generik) |

### 2.2 Typography

| Role | Font | Fallback | Catatan |
|---|---|---|---|
| Display (H1, wordmark) | **Fraunces** (serif, opsional variable font utk swash) | `Georgia, serif` | Dipakai terbatas: hero headline, nama produk besar |
| Body | **Inter** | `system-ui, sans-serif` | Readability tinggi, netral, tidak bersaing dgn display |
| Accent/Script (sparingly) | **Playfair Display Italic** atau webfont custom mendekati logo | `Georgia, serif italic` | Hanya untuk sub-label pendek, bukan body text |
| Utility/Data (harga, order ID, angka) | **IBM Plex Mono** | `ui-monospace, monospace` | Angka harga & nomor order — alignment rapi di tabel |

**Type scale (rem, base 16px):**
```
--text-xs:   0.75rem   (12px)  caption, label
--text-sm:   0.875rem  (14px)  body kecil, meta
--text-base: 1rem      (16px)  body default
--text-lg:   1.25rem   (20px)  subheading
--text-xl:   1.75rem   (28px)  section title
--text-2xl:  2.5rem    (40px)  page hero (mobile)
--text-3xl:  4rem      (64px)  page hero (desktop)
```

### 2.3 Spacing & Layout

- Grid: 12-column, max-width container `1280px`, gutter `24px`
- Spacing scale: `4, 8, 12, 16, 24, 32, 48, 64, 96` (px) — konsisten dengan Tailwind default scale, tidak custom-invent
- Border radius: `--radius-sm: 4px`, `--radius-md: 8px` — sudut tidak terlalu rounded, selaras dengan karakter "heritage/craft" bukan playful/startup generic

### 2.4 Motion

- Page-load: fade-in halus untuk hero (300ms, ease-out) — satu kali saja, bukan tiap section
- Scroll-reveal: section "asal kopi" (peta Lampung) muncul dengan garis kontur "digambar" (stroke-dashoffset animation) — ini signature moment, dieksekusi dengan baik satu kali
- Hover: product card scale `1.02` + shadow halus, transisi `150ms`
- **Respect `prefers-reduced-motion`** — semua animasi non-esensial di-disable kalau user set preference ini

---

## 3. Component Library Decision

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Styling | Tailwind CSS | Konsisten dgn Laravel stack (Vite + Tailwind default), utility-first cocok untuk design token di atas |
| Component primitives | Blade Components + Livewire Components | Reusable UI (`<x-button>`, `<x-product-card>`) via Blade component, interaktivitas (cart, form realtime) via Livewire component |
| Interactivity tanpa full page reload | Alpine.js (built-in dengan Livewire) | Dropdown, modal, toggle sederhana di client tanpa butuh JS framework besar |
| Icon | Blade Icons (`blade-ui-kit/blade-icons`) dengan set Lucide | Konsisten line-weight, dipakai sebagai Blade component (`<x-icon-cart />`) |
| Image | `spatie/laravel-medialibrary` atau Livewire `WithFileUploads` + Cloudflare R2 | Auto-resize/optimize di server sebelum simpan ke R2 — penting untuk foto produk yang berat |

**Keputusan kunci:** Blade Component dipakai untuk elemen statis/reusable (button, card, badge), Livewire Component dipakai kalau butuh state & interaktivitas server-driven (cart, form checkout, admin table dengan filter). Alpine.js dipakai hanya untuk interaksi client-side murni yang tidak butuh round-trip server (misal toggle dropdown navigasi) — supaya tidak semua interaksi kecil memicu request Livewire yang tidak perlu.

---

## 4. Information Architecture / Site Map

```
/                          Landing (hero carousel, brand story, New Arrivals, Best Sellers,
                             Shop by Collection, testimonial, produk unggulan)
/products                  Katalog (grid produk + filter: Roast/Flavor + sort + pagination)
/products/[slug]           Detail produk (deskripsi, roast profile, harga, add to cart)
/about                     Cerita Way Kopi & petani Lampung — hero statement, values (3 poin
                             icon), highlight proses produksi, tim/behind-the-scenes, timeline
                             milestone brand
/blog                      Listing artikel, filter kategori (Coffee/Recipes/Lifestyle)
/blog/[slug]               Detail artikel
/cart                      Keranjang belanja
/checkout                  Form alamat + pilih ongkir + pilih metode bayar
/checkout/success          Konfirmasi setelah redirect dari Xendit
/orders/[id]               Order tracking (status, resi, timeline)
/account                   Riwayat order, data akun (kalau sudah login)
/account/login             Login — form email+password, "forgot password", "create account",
                             newsletter signup banner di bawah form (pola dari referensi)
/account/register          Registrasi akun

--- Admin (route group terpisah, auth-gated) ---
/admin                     Dashboard (ringkasan order/revenue)
/admin/products            Kelola produk (list, create, edit)
/admin/orders              Kelola pesanan (list, detail, update status, input resi)
/admin/posts               Kelola artikel blog (list, create/edit, publish/draft)
```

**Elemen berulang lintas halaman (pola dari referensi, tokenized ke Way Kopi):**
- **Promo bar** di paling atas (misal info ongkir gratis/promo aktif) — dark bg dengan aksen gold, bukan merah
- **Newsletter signup banner** muncul di footer semua halaman + khusus setelah form login/register
- **Sticky nav** dengan logo di tengah (mengikuti pola referensi, cocok dengan logo Way Kopi yang sudah simetris)

---

## 5. Key User Flows

### 5.1 Flow: Pembeli baru → checkout

```
Landing → lihat brand story → klik "Belanja Sekarang"
   → Katalog → klik produk → Detail Produk (baca roast profile, origin)
   → "Tambah ke Keranjang" → toast konfirmasi (tanpa pindah halaman)
   → ikon cart → Cart page → review qty/harga → "Checkout"
   → Checkout: isi alamat → pilih kurir (Biteship rates muncul realtime)
   → pilih metode bayar → redirect ke Xendit payment page
   → bayar → redirect balik ke /checkout/success
   → notifikasi WA (via WAHA) masuk otomatis
   → bisa cek /orders/[id] kapan saja untuk tracking
```

**Prinsip UX:** minimal 2 halaman antara "lihat produk" dan "checkout" — jangan force login sebelum checkout (guest checkout diizinkan, akun dibuat opsional setelah order untuk tracking lebih mudah).

### 5.2 Flow: Admin proses pesanan baru

```
Notifikasi order baru (dashboard atau nanti bisa WA to admin)
   → /admin/orders → filter status "Paid, belum dikirim"
   → klik order → lihat detail (alamat, produk, catatan pembeli)
   → buat shipment (trigger Biteship API dari sini, sesuai flow di Architecture.md §4.2)
   → input/generate resi → update status "Shipped"
   → sistem otomatis kirim notifikasi WA ke pembeli
```

### 5.3 Empty & Error States (ditulis sebagai konten, bukan generik)

| Kondisi | Pesan (nada: hangat, jelas, tidak generik) |
|---|---|
| Cart kosong | "Keranjang masih kosong. Belum menemukan kopi yang pas?" + CTA ke katalog |
| Stok habis | "Batch ini sedang habis. Batch berikutnya biasanya tersedia dalam [X] minggu." (bukan sekadar "Out of Stock") |
| Checkout gagal (payment) | "Pembayaran belum berhasil diproses. Coba lagi, atau pilih metode pembayaran lain." |
| Order tidak ditemukan | "Nomor pesanan ini tidak ditemukan. Periksa kembali link dari WhatsApp konfirmasi kamu." |

---

## 6. Wireframe — Halaman Kunci (ASCII, low-fidelity)

### 6.1 Landing Page

```
┌──────────────────────────────────────────────┐
│ [Promo bar: info ongkir/promo, dark+gold text]│
├──────────────────────────────────────────────┤
│  [Logo Way Kopi]        Produk  Cerita  Blog  │  ← nav, logo di tengah, transparan di atas hero
│                          Cart                  │
├──────────────────────────────────────────────┤
│     [Foto kemasan/kebun, dark moody]          │
│     "Kopi Robusta, dipanen langsung           │  ← hero, fade-in on load
│      dari petani Lampung"                     │
│     [Belanja Sekarang →]                      │
├──────────────────────────────────────────────┤
│  Produk Terbaru (New Arrivals)                │
│  [Card] [Card] [Card] [Card →scroll]          │  ← carousel horizontal, konsisten dgn pola referensi
├──────────────────────────────────────────────┤
│  [Signature section: peta Lampung, garis      │
│   kontur ter-animate saat scroll]             │
│  "Dari kebun di [nama daerah], langsung ke    │
│   cangkir kamu"                               │
├──────────────────────────────────────────────┤
│  Kata Mereka (testimonial carousel)           │
│  "Kopi paling..." — [Nama pembeli]            │
├──────────────────────────────────────────────┤
│  Best Sellers                                 │
│  [Card] [Card] [Card] [Card →scroll]          │
├──────────────────────────────────────────────┤
│  Newsletter signup banner (gold bg, dark text)│
├──────────────────────────────────────────────┤
│  Footer: kontak WA, alamat, sosial media      │
└──────────────────────────────────────────────┘
```

**Catatan:** section "New Arrivals"/"Best Sellers"/testimonial carousel diadopsi dari pola referensi (struktur, bukan visual) — relevan karena Way Kopi diproyeksikan nambah varian produk di fase depan (Schema.md §1). Untuk MVP dengan 1 varian produk, section ini bisa tampil lebih sederhana (misal cuma 1-2 card), tapi struktur komponen tetap disiapkan untuk scale.

### 6.2 Product Detail Page (PDP)

```
┌──────────────────────────────────────────────┐
│  [Foto produk, zoomable]  │  Way Kopi Robusta │
│                            │  Rp XX.XXX        │
│                            │  Roast: Medium    │
│                            │  Berat: 200g       │
│                            │  ─────────────    │
│                            │  Qty [- 1 +]      │
│                            │  [Tambah ke Cart] │
├──────────────────────────────────────────────┤
│  Deskripsi & cerita origin produk ini         │
├──────────────────────────────────────────────┤
│  Produk lainnya (related)                     │
└──────────────────────────────────────────────┘
```

### 6.3 Checkout Page

```
┌──────────────────────────────────────────────┐
│  1. Alamat Pengiriman        │  Ringkasan     │
│     [form input]              │  Produk x qty  │
│  2. Pilih Kurir               │  Subtotal      │
│     [radio: opsi + estimasi   │  Ongkir        │
│      dari Biteship]           │  ─────────     │
│  3. Metode Pembayaran         │  Total         │
│     [radio: via Xendit]       │  [Bayar →]     │
└──────────────────────────────────────────────┘
```
Layout 2 kolom desktop (form kiri, ringkasan sticky kanan), stack vertikal di mobile dengan ringkasan collapsible di atas form.

### 6.4 Admin Order List

```
┌──────────────────────────────────────────────┐
│  Pesanan          [Filter: Semua|Paid|Shipped]│
├──────────────────────────────────────────────┤
│  #ORD-001  Budi S.      Rp150.000   [Paid]    │
│  #ORD-002  Siti A.      Rp95.000    [Shipped] │
│  #ORD-003  Andi P.      Rp210.000   [Pending] │
└──────────────────────────────────────────────┘
```
Tabel data-dense, pakai font mono untuk angka (harga, ID order) sesuai token typography §2.2.

### 6.5 Katalog Produk (dengan filter — pola dari referensi)

```
┌──────────────────────────────────────────────┐
│  Beranda › Kopi Robusta                       │
│  Filter: [Roast▾] [Flavor▾]     Sort: [▾]     │
├──────────────────────────────────────────────┤
│  [Card]  [Card]  [Card]  [Card]               │  ← grid 4 kolom desktop, 2 kolom tablet,
│  [Card]  [Card]  [Card]  [Card]               │     1 kolom mobile
├──────────────────────────────────────────────┤
│         [1] [2] [3] [Next →]                  │  ← pagination, untuk MVP mungkin belum perlu
└──────────────────────────────────────────────┘  kalau produk masih 1 varian — struktur disiapkan
```

> Catatan MVP: dengan 1 varian produk (Schema.md), halaman ini akan tampak sangat sederhana (1 card, tanpa filter berarti). Struktur filter/sort/pagination tetap dibangun sebagai komponen reusable supaya siap dipakai begitu varian produk bertambah di fase depan — tapi UI filter **disembunyikan otomatis** kalau jumlah produk di bawah threshold tertentu (misal < 4 produk), supaya tidak terlihat kosong/janggal.

### 6.6 Login Page

```
┌──────────────────────────────────────────────┐
│  [Promo bar]                                  │
│  [Logo Way Kopi, tengah]                      │
│              Masuk                            │
│      ┌────────────────────────┐               │
│      │  Email                  │               │
│      ├────────────────────────┤               │
│      │  Password                │               │
│      └────────────────────────┘               │
│      Lupa kata sandi?                          │
│      [   Masuk   ]  (gold fill button)        │
│      Buat akun baru                            │
├──────────────────────────────────────────────┤
│  [Newsletter signup banner, gold bg]          │
├──────────────────────────────────────────────┤
│  Footer                                       │
└──────────────────────────────────────────────┘
```
Form tunggal center-aligned, max-width `400px` — pola diadopsi dari referensi (form sederhana + newsletter banner di bawahnya), warna disesuaikan token Way Kopi.

### 6.7 Blog — Listing & Detail

```
Listing (/blog)                          Detail (/blog/[slug])
┌───────────────────────────┐            ┌───────────────────────────┐
│  Blog                      │            │  ← Kembali ke Blog         │
│  [Semua|Coffee|Recipes|    │            │  [Cover image]             │
│   Lifestyle]                │            │  Judul Artikel             │
├───────────────────────────┤            │  Kategori · Tanggal        │
│  [Card] [Card] [Card]      │            ├───────────────────────────┤
│  [Card] [Card] [Card]      │            │  Konten artikel (rich      │
├───────────────────────────┤            │  text, format sama dgn     │
│  [Load more / pagination]  │            │  body typography §2.2)     │
└───────────────────────────┘            ├───────────────────────────┤
                                          │  Artikel terkait (related) │
                                          └───────────────────────────┘
```
Card blog: cover image + kategori badge (token warna §2.1, bukan warna generik per-kategori) + judul + excerpt 1-2 baris + tanggal.

### 6.8 About Page

```
┌──────────────────────────────────────────────┐
│  Hero statement penuh ("Dari Lampung untuk     │
│  Indonesia" atau sejenis) + foto kebun/produk │
├──────────────────────────────────────────────┤
│  3 poin nilai (icon + teks pendek):           │
│  [Harga adil ke petani] [Proses berkelanjutan]│
│  [Pemberdayaan komunitas Lampung]             │
├──────────────────────────────────────────────┤
│  Section 2 kolom: foto proses produksi +      │
│  narasi "dari kebun ke kemasan"               │
├──────────────────────────────────────────────┤
│  Signature: peta kontur Lampung (sama dgn     │
│  landing §6.1, kalau belum ditampilkan di sana)│
├──────────────────────────────────────────────┤
│  Newsletter banner + Footer                    │
└──────────────────────────────────────────────┘
```
Struktur "3 poin nilai" & "2 kolom foto+narasi" diadopsi dari referensi, tapi konten diisi cerita Way Kopi/petani Lampung yang sesungguhnya — **bukan** placeholder/isi generik. Timeline milestone brand (seperti di referensi) **opsional**, tambahkan kalau Way Kopi punya milestone konkret untuk ditampilkan (tahun berdiri, pencapaian, dll) — jangan dipaksakan kalau belum ada.

---

## 7. Component Inventory

| Komponen | Varian | Catatan teknis |
|---|---|---|
| Button | primary (gold fill), secondary (outline), ghost | Focus ring wajib terlihat (accessibility) |
| ProductCard | default, out-of-stock (dimmed + label) | Hover: scale 1.02 |
| Input/Select/Textarea | default, error state | Error message inline, bukan cuma border merah (untuk color-blind users) |
| Badge | status order (pending/paid/shipped/delivered) | Warna dari token §2.1, bukan merah/hijau generik browser |
| Toast/Notification | success, error | Auto-dismiss 4s, bisa di-dismiss manual |
| Modal/Dialog | konfirmasi aksi admin (misal hapus produk) | Alpine.js `x-show`/`x-trap` untuk focus trap manual, atau Livewire modal package (`livewire-ui/modal`) |
| PriceDisplay | dengan mono font | Format Rupiah konsisten (`Rp150.000`, bukan `Rp 150000`) |
| StepIndicator | checkout 3 langkah | Bukan numbered generik — label eksplisit ("Alamat", "Kurir", "Bayar") |
| PromoBar | dismissible atau permanen | Dark bg + teks gold, tampil di atas nav semua halaman |
| FilterDropdown | Roast, Flavor, Sort | Disembunyikan otomatis kalau jumlah produk < threshold (§6.5) |
| ProductCarousel | New Arrivals, Best Sellers | Scroll horizontal (drag/swipe di mobile), panah navigasi di desktop |
| TestimonialCarousel | quote + nama pembeli | Auto-rotate dengan pause on hover, respect `prefers-reduced-motion` |
| PostCard | blog listing | Cover image + kategori badge + judul + excerpt + tanggal |
| CategoryBadge | blog & produk (kalau perlu tag) | Warna dari token §2.1, konsisten dengan StatusBadge, bukan warna acak per kategori |
| Pagination | katalog & blog listing | Numbered + Next, disembunyikan kalau total item ≤ 1 halaman |
| NewsletterBanner | footer & setelah login/register | Gold bg, input email + CTA, 1 baris di desktop, stack di mobile |

---

## 8. Responsive Breakpoints

```
--bp-sm:  640px   (mobile besar)
--bp-md:  768px   (tablet)
--bp-lg:  1024px  (desktop kecil)
--bp-xl:  1280px  (desktop, max container width)
```

Mobile-first (sesuai PRD — mayoritas traffic dari mobile). Semua wireframe di §6 didesain mobile dulu, desktop adalah ekspansi (bukan sebaliknya).

---

## 9. Technical Design Decisions

| # | Keputusan | Alasan |
|---|---|---|
| 1 | Blade Component untuk elemen statis, Livewire untuk interaktif, Alpine untuk client-only | Pemisahan jelas: hindari over-fetch server-side (Livewire) untuk interaksi trivial, tapi tetap dapat server-driven state untuk logic penting (cart, checkout) |
| 2 | Image resize/optimize di server sebelum simpan ke R2 (via `intervention/image` atau `spatie/laravel-medialibrary`), lazy-load pakai atribut `loading="lazy"` native | Foto produk dari R2 cenderung besar; optimize wajib untuk performance (LCP target di PRD < 2.5s). Laravel tidak punya `next/image` built-in, jadi optimasi dilakukan eksplisit saat upload (generate beberapa ukuran/format WebP), bukan on-the-fly saat render |
| 3 | Font di-self-host (bukan Google Fonts CDN langsung) | Kurangi external request, lebih baik untuk performance & privacy (tidak kirim data ke Google Fonts server) |
| 4 | Dark mode adalah **satu-satunya** mode (bukan light/dark toggle) — **dikonfirmasi final** | Brand identity dark-first sesuai logo; menambah light mode akan melemahkan konsistensi brand tanpa kebutuhan bisnis yang jelas |
| 5 | Skeleton loading state untuk katalog & PDP | ISR/cache Redis bisa stale-while-revalidate; skeleton mencegah layout shift |
| 6 | Semua interactive element wajib visible focus ring | Aksesibilitas keyboard navigation, terutama untuk flow checkout |

> ✅ **Dikonfirmasi:** dark-only untuk seluruh situs. Pengecualian: halaman invoice/struk (kalau perlu print-friendly) di-scope terpisah sebagai halaman khusus dengan style print (`@media print`), bukan toggle mode keseluruhan situs.

---

## 10. Design Tokens as Code (handoff ke developer)

```css
/* globals.css — CSS variables, dikonsumsi oleh Tailwind config */
:root {
  --color-bg-base: #100E0C;
  --color-bg-surface: #1C1712;
  --color-text-primary: #F5F1E8;
  --color-text-muted: #A69C8D;
  --color-accent-gold: #C8A050;
  --color-accent-gold-bright: #E4C374;
  --color-coffee-brown: #3D2B1F;
  --color-success: #7A9B76;
  --color-error: #B85C50;

  --font-display: 'Fraunces', Georgia, serif;
  --font-body: 'Inter', system-ui, sans-serif;
  --font-accent: 'Playfair Display', Georgia, serif;
  --font-mono: 'IBM Plex Mono', ui-monospace, monospace;

  --radius-sm: 4px;
  --radius-md: 8px;
}
```

---

## 11. Verifikasi Dokumen Ini Benar

- [ ] Semua warna di §2.1 sudah dites kontras minimal WCAG AA (4.5:1 untuk teks normal) terhadap `--color-bg-base`
- [ ] Wireframe di §6 sudah divalidasi dengan minimal 1 produk asli (foto yang sudah ada) sebelum dikembangkan jadi hi-fi mockup
- [ ] Font `Fraunces`/`Playfair Display` sudah dicek lisensinya untuk self-hosting (Google Fonts umumnya OFL, aman, tapi perlu dikonfirmasi)
- [ ] **Hi-fi mockup Figma** sudah dibuat untuk halaman kunci (Landing, PDP, Cart, Checkout, Admin Order List) berdasarkan wireframe §6 + token §2, dan direview/disetujui sebelum development frontend dimulai

## 12. Next Step — Figma Hi-Fi Mockup

Sesuai keputusan, development frontend **tidak langsung dari wireframe ASCII** di dokumen ini — perlu tahap hi-fi mockup di Figma dulu sebagai gate sebelum coding:

1. Import token §2 (warna, tipografi, spacing) sebagai Figma Variables/Styles
2. Build mockup untuk 5 halaman prioritas: Landing, Katalog, PDP, Cart+Checkout, Admin Order List
3. Terapkan signature element (§1: garis kontur peta Lampung) di halaman Landing sebagai showcase utama
4. Review mockup terhadap brand asset asli (logo, foto produk) — pastikan konsisten, bukan "mirip tapi beda nuansa"
5. Setelah disetujui, baru masuk fase development frontend (component library §3 + wireframe jadi acuan struktur, mockup jadi acuan visual final)

---

## 13. Open Items

Semua open item sudah diputuskan:

1. ~~Dark-only vs light mode~~ ✅ Dark-only, dikonfirmasi
2. ~~Hi-fi mockup Figma~~ ✅ Diperlukan — jadi gate sebelum development (lihat §12)
3. ~~Foto tambahan~~ ✅ Pakai foto produk yang sudah ada dulu (bubuk kopi kemasan) untuk mockup awal. Section brand story di landing page dirancang fleksibel (§6.1) supaya foto kebun/petani bisa ditambahkan belakangan tanpa restrukturisasi layout.
