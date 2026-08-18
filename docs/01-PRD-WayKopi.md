# Product Requirements Document (PRD) — Way Kopi E-Commerce

**Versi:** 0.1 (Draft)
**Tanggal:** 30 Juli 2026
**Status:** Untuk direview sebelum kick-off development

---

## 1. Latar Belakang & Problem Statement

Way Kopi menjual kopi robusta yang dipanen langsung dari petani di Lampung. Saat ini belum ada kanal penjualan online yang terstruktur (asumsi: penjualan masih manual via WA/media sosial/offline).

**Masalah yang ingin diselesaikan:**
- Petani/brand tidak punya kanal digital untuk menjangkau pembeli di luar jaringan personal
- Tidak ada sistem katalog, stok, dan transaksi yang otomatis (rawan human error, sulit scale)
- Tidak ada data pelanggan/pembelian untuk keputusan bisnis (repeat order, produk favorit, dll)
- website ini merupaka kanal utama untuk penjualan online

---

## 2. Goals

### Business Goals
- Membangun kanal penjualan online langsung ke konsumen (D2C) untuk margin lebih baik dibanding marketplace
- Membangun brand story "kopi dari petani langsung" sebagai diferensiasi
- Mengumpulkan data pelanggan untuk retensi (email/WA list, repeat purchase)

### User Goals
- Pembeli bisa dengan mudah menemukan produk, memahami origin story, dan checkout tanpa friksi
- Pembeli bisa melacak status pesanan tanpa harus tanya manual via chat

### Non-Goals (Out of Scope untuk MVP)
- Marketplace multi-vendor / multi-brand
- B2B/wholesale ordering system
- Subscription box (kopi bulanan) — kandidat fase v2
- Multi-bahasa/multi-currency (ekspor) — kandidat fase v2
- pengiriman dari Bogor, Jawa Barat

---

## 3. Target User & Persona

| Persona | Deskripsi | Kebutuhan Utama |
|---|---|---|
| Coffee enthusiast | Paham origin, roast level, tertarik cerita petani | Info produk detail, transparansi asal kopi |
| Pembeli kasual/gift | Beli sebagai hadiah atau coba-coba | UX simpel, checkout cepat, opsi packaging |
| Repeat customer | Sudah pernah beli, ingin reorder cepat | Riwayat order, reorder 1-klik, member benefit (opsional) |

---

## 4. Scope & MVP

### 4.1 MVP Feature Set (Fase 1 — harus ada untuk launch)

**Customer-facing:**
- [x] Landing page + brand story (asal kopi, proses panen, petani Lampung)
- [x] Katalog produk (list + detail page: deskripsi, roast profile, harga, stok, foto)
- [x] Keranjang belanja (cart)
- [x] Checkout: input alamat, email opsional (tidak wajib), pilihan ongkir, metode pembayaran (Transfer Bank Langsung / COD)
- [x] Opsi pembayaran COD (Bayar di Tempat) & Transfer Bank Langsung (Mandiri/BRI)
- [x] Integrasi ongkir (Biteship)
- [x] Konfirmasi order (WA notifikasi)
- [x] Order tracking sederhana (status: pending_payment/paid/processing/shipped/delivered)
- [x] Login/register (email atau nomor HP)
- [x] Blog/konten edukasi kopi (list artikel + detail artikel, kategori sederhana)

**Admin-facing:**
- [x] Dashboard admin: kelola produk (CRUD), stok, harga
- [x] Kelola pesanan (lihat, update status, input resi, cetak resi pengiriman thermal/A4)
- [x] Cetak / download resi pengiriman siap print untuk pesanan dibayar (paid) & COD (processing)
- [x] Kelola akun pelanggan & pengguna (CRUD di /admin/customers)
- [x] Kelola artikel blog (CRUD, publish/draft)
- [ ] Basic report: total order, revenue harian/bulanan

**Infra/Non-functional:**
- [ ] Deploy di VPS Hostinger via Docker + Traefik (SSL otomatis)
- [ ] Database PostgreSQL dengan backup terjadwal
- [ ] Responsive (mobile-first — mayoritas traffic Indonesia dari mobile)

### 4.2 Fase 2 (Post-MVP, tidak diblok untuk launch)
- Subscription/kopi bulanan
- Loyalty program / member points
- Multi-admin role & permission
- Analytics lanjutan (funnel, cohort)
- ~~Blog/CMS~~ → **dipindah ke MVP** (lihat §4.1)
- Integrasi marketplace (Shopee/Tokopedia sync stok)

---

## 5. Technical Requirements

| Layer | Pilihan | Catatan |
|---|---|---|
| Frontend + Backend | Laravel 13 (Blade + Livewire, full monolith) | 1 stack, server-rendered, cocok untuk solo developer |
| Admin Panel | Laravel Livewire (route group terpisah, sama app) | Tidak perlu app/stack terpisah |
| Database | PostgreSQL | Perlu skema: products, orders, order_items, users, payments |
| Cache/Queue | Redis | Session, cache katalog, queue notifikasi (email/WA) |
| Reverse proxy | Traefik | SSL via Let's Encrypt, routing multi-service |
| Container | Docker + Docker Compose | Sesuai standar environment kamu |
| Payment | Xendit | Sudah diputuskan |
| Ongkir | Biteship | Sudah diputuskan |
| Notifikasi | WhatsApp via WAHA (self-hosted) | WA-first untuk MVP, email menyusul di fase 2 |
| Storage gambar | Cloudflare R2 (S3-compatible) | Sudah diputuskan — jangan simpan file di VPS langsung, masalah backup & scaling |

### Security & Reliability (wajib, bukan opsional)
- Semua secret via `.env`, tidak hardcoded
- Validasi input di server-side (bukan cuma client-side) — cegah price manipulation di cart
- Rate limiting di endpoint checkout & login
- HTTPS enforced via Traefik
- Backup database otomatis (cron + retention policy)
- Idempotency key untuk payment webhook (cegah double-charge/double-order)

---

## 6. Success Metrics

### Launch metrics (30 hari pertama)
- Uptime ≥ 99% (monitoring via Traefik/Grafana atau sejenis)
- Checkout completion rate ≥ target (baseline dulu, tetapkan setelah data 2 minggu)
- Page load (LCP) < 2.5s di mobile

### Business metrics (90 hari)
- Jumlah order/bulan
- Average Order Value (AOV)
- Repeat purchase rate
- Cart abandonment rate < 70% (industry avg e-commerce ~70%, target di bawah itu)

> ⚠️ **Perlu konfirmasi:** target angka konkret (misal jumlah order/bulan) sebaiknya dari kamu berdasarkan kapasitas produksi/stok kopi yang tersedia — jangan set target marketing tanpa sinkron ke supply.

---

## 7. Open Decisions (harus diputuskan sebelum development mulai)

1. ~~Payment gateway~~ ✅ Xendit
2. ~~Ongkir provider~~ ✅ Biteship
3. ~~Backend architecture~~ ✅ Laravel 13 monolith (Blade + Livewire, storefront & admin dalam 1 aplikasi)
4. ~~Object storage~~ ✅ Cloudflare R2
5. ~~Prioritas notifikasi~~ ✅ WA-first
6. ~~Brand asset~~ ✅ Sudah ada (logo, foto produk)

---

## 8. Next Steps
Setelah PRD ini disetujui, lanjut ke 4 dokumen berikutnya:
1. ~~PRD~~ ✅
2. Tech Stack & Architecture Document
3. Database Schema Design
4. UI/UX Wireframe & Design System spec
5. Development Roadmap & Timeline
