# Architecture Document — Way Kopi E-Commerce

**Versi:** 0.2 (Revisi — pivot stack ke Laravel)
**Tanggal:** 30 Juli 2026
**Referensi:** 01-PRD-WayKopi.md

> **Catatan revisi:** dokumen ini menggantikan v0.1 yang berbasis Next.js. Semua keputusan bisnis (Xendit, Biteship, WAHA, Cloudflare R2, VPS KVM 4, domain waykopi.com) tetap sama — yang berubah adalah implementasi teknis: dari Next.js/Node.js ke **Laravel 13 (Blade + Livewire) full monolith**.

---

## 1. Tech Stack Overview

| Layer | Pilihan | Versi (rekomendasi) |
|---|---|---|
| Framework | Laravel | 13.x |
| Frontend (storefront + admin) | Blade + Livewire 3 + Alpine.js | — |
| Runtime | PHP | 8.3+ |
| Web server | Nginx + PHP-FPM (Laravel Octane opsional untuk performa lanjutan) | — |
| Database | PostgreSQL | 16.x |
| Cache/Queue/Session | Redis | 7.x |
| ORM | Eloquent (built-in Laravel) | — |
| Reverse proxy | Traefik | 3.x |
| Container | Docker + Docker Compose | — |
| Payment | Xendit API (via `xendit/xendit-php` SDK resmi) | latest |
| Ongkir | Biteship API (HTTP client Laravel, wrapper custom — tidak ada SDK resmi PHP) | v1 |
| Notifikasi | WAHA (WhatsApp HTTP API, self-hosted) | latest stable |
| Object storage | Cloudflare R2 (S3-compatible, via Laravel Filesystem `s3` driver) | — |
| Queue worker | Laravel Queue (driver: Redis) + Supervisor | Horizon opsional untuk monitoring |
| Server OS | Ubuntu 24.04 LTS | — |
| Hosting | Hostinger VPS KVM 4 | — |

> Cek versi terbaru Laravel/PHP/Traefik saat mulai coding — versi di atas adalah baseline per Juli 2026.

**Keputusan kunci:** full monolith Laravel — storefront (Blade+Livewire) dan admin panel (Livewire, route group terpisah) dalam **1 aplikasi**, bukan API + SPA terpisah. Dipilih untuk mengurangi kompleksitas operasional solo developer (1 bahasa, 1 deployment, 1 auth system).

---

## 2. High-Level Architecture

```
                            ┌─────────────────────┐
                            │      Internet         │
                            └──────────┬───────────┘
                                       │ HTTPS (443)
                            ┌──────────▼───────────┐
                            │       Traefik          │  ← SSL termination (Let's Encrypt)
                            └──────────┬───────────┘
                                       │
                            ┌──────────▼───────────┐
                            │   Nginx + PHP-FPM       │
                            │  (container: app)       │
                            │  Laravel: Storefront     │
                            │  (Blade+Livewire) +      │
                            │  Admin (/admin, gated)   │
                            └──────────┬───────────┘
                                       │
                     ┌─────────────────┼─────────────────┐
                     │                 │                   │
           ┌─────────▼────────┐ ┌──────▼──────────┐ ┌──────▼─────────┐
           │   PostgreSQL       │ │      Redis        │ │  Queue Worker    │
           │  (container: db)   │ │  (cache, session,  │ │  (container:     │
           │                    │ │   queue)           │ │  queue:work)     │
           └────────────────────┘ └─────────────────────┘ └──────────────────┘

           External services (dipanggil via HTTP client dari Laravel):
           ┌─────────────┐  ┌─────────────┐  ┌──────────────────┐
           │   Xendit      │  │  Biteship    │  │  Cloudflare R2      │
           └─────────────┘  └─────────────┘  └──────────────────┘

           Internal service (self-hosted, VPS sama):
           ┌────────────────────┐
           │   WAHA (container)   │  ← Laravel panggil via internal network
           └────────────────────┘
```

**Queue worker terpisah dari container `app`** — proses HTTP request dan proses background job (kirim notifikasi WA, dll) dipisah supaya request tidak lambat menunggu job selesai. Job di-dispatch ke Redis queue, worker container yang memprosesnya secara async.

> ⚠️ **Catatan risiko WAHA:** self-hosted, pakai sesi WhatsApp Web (bukan official Business API). Risiko: (1) sesi logout sewaktu-waktu → butuh monitoring/alert, (2) nomor bisa di-flag kalau volume tinggi. Mitigasi: rate limit via queue throttle, nomor dedicated (bukan pribadi), fallback log ke tabel `notification_logs`.

---

## 3. Folder Structure

```
way-kopi/
├── docker-compose.yml
├── docker-compose.prod.yml
├── .env.example
├── traefik/
│   ├── traefik.yml
│   └── dynamic/middlewares.yml
├── app/
│   ├── Console/Commands/
│   │   └── ReleaseExpiredOrderStock.php   # scheduled command, ganti "cron job" versi lama
│   ├── Http/
│   │   ├── Controllers/Webhooks/
│   │   │   ├── XenditWebhookController.php
│   │   │   └── BiteshipWebhookController.php
│   │   ├── Middleware/EnsureIsAdmin.php    # guard route /admin
│   │   └── Requests/                        # Form Request classes (validasi server-side)
│   │       ├── StoreProductRequest.php
│   │       └── CheckoutRequest.php
│   ├── Livewire/
│   │   ├── Storefront/
│   │   │   ├── ProductCatalog.php
│   │   │   ├── ProductDetail.php
│   │   │   ├── Cart.php
│   │   │   ├── Checkout.php
│   │   │   ├── OrderTracking.php
│   │   │   ├── PostList.php                  # Blog listing (MVP)
│   │   │   └── PostDetail.php                 # Blog detail (MVP)
│   │   └── Admin/
│   │       ├── ProductManager.php
│   │       ├── OrderList.php
│   │       ├── OrderDetail.php
│   │       ├── PostManager.php                # CRUD artikel blog (MVP)
│   │       └── Dashboard.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── ProductVariant.php
│   │   ├── ProductImage.php
│   │   ├── Address.php
│   │   ├── CartItem.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── OrderStatusHistory.php
│   │   ├── Payment.php
│   │   ├── Shipment.php
│   │   ├── NotificationLog.php
│   │   ├── Post.php
│   │   └── WebhookEvent.php
│   ├── Services/                             # business logic layer
│   │   ├── ProductService.php
│   │   ├── OrderService.php
│   │   ├── StockReservationService.php
│   │   ├── PaymentService.php                # wrapper Xendit
│   │   ├── ShippingService.php                # wrapper Biteship
│   │   └── PostService.php                    # publish/draft logic, slug generation
│   ├── Notifications/
│   │   └── SendWhatsAppNotification.php       # panggil WAHA, dispatch via queue
│   └── Providers/
├── database/
│   ├── migrations/
│   ├── factories/
│   └── seeders/DatabaseSeeder.php
├── resources/
│   ├── views/
│   │   ├── livewire/{storefront,admin}/
│   │   ├── layouts/{storefront,admin}.blade.php
│   │   └── components/                        # Blade components reusable
│   ├── css/app.css                              # Tailwind entry
│   └── js/app.js                                # Alpine.js entry
├── routes/
│   ├── web.php
│   └── console.php                              # scheduled command registration
├── docker/
│   ├── app/Dockerfile
│   └── nginx/default.conf
└── scripts/
    ├── backup-db.sh
    └── deploy.sh
```

**Keputusan kunci:**
- **`app/Livewire/Storefront/` & `app/Livewire/Admin/`** — pemisahan logis dalam 1 app, setara route groups di versi sebelumnya.
- **`app/Services/`** tetap dipertahankan sebagai business logic layer terpisah dari Livewire component — mencegah "fat component" (setara fat controller), memudahkan unit test tanpa render Livewire.
- **Form Request classes** — Laravel-native equivalent dari Zod validators sebelumnya. Livewire boleh pakai `#[Validate]` untuk validasi realtime di form, tapi validasi final tetap wajib di Form Request/Service layer — jangan trust validasi client-side Livewire saja.
- **Scheduled command** menggantikan "cron job" — didaftarkan di `routes/console.php`, dijalankan lewat 1 cron entry `* * * * * php artisan schedule:run`.

---

## 4. Data Flow — Alur Kritis

### 4.1 Checkout & Payment Flow

```
User          Livewire Checkout    PostgreSQL      Xendit          Queue → WAHA
 │─ submit cart ──▶│                   │              │                │
 │                 │─ validate stok ──▶│              │                │
 │                 │  (StockReservation│              │                │
 │                 │   Service, lock)  │              │                │
 │                 │◀─ stok OK ────────│              │                │
 │                 │─ create order ───▶│ (PENDING)    │                │
 │                 │─ create invoice ─────────────────▶│                │
 │                 │◀─ invoice URL ─────────────────────│                │
 │◀─ redirect ─────│                   │              │                │
 │─ bayar di Xendit page ──────────────────────────────▶│                │
 │                 │◀── webhook: payment success ──────│                │
 │     (XenditWebhookController, verify signature)      │                │
 │                 │─ update order (PAID) ─▶│           │                │
 │                 │─ dispatch job SendWhatsAppNotification (async) ────▶│
 │◀─ redirect ke order tracking page ──│              │                │
```

**Keputusan kunci (konsisten dengan versi sebelumnya, beda implementasi):**
- Webhook-driven, bukan polling — status final dari webhook Xendit
- Idempotency via model `WebhookEvent` (`unique(source, event_id)`)
- Signature verification wajib di `XenditWebhookController` sebelum trust payload
- Notifikasi WA **queued job**, bukan synchronous call — supaya response webhook tetap cepat (Xendit retry terus kalau webhook lambat/gagal direspon)

### 4.2 Shipping & Order Fulfillment Flow

```
Admin       Livewire OrderDetail   PostgreSQL       Biteship       Queue → WAHA
 │─ input order kirim ──▶│                │               │              │
 │                       │─ create shipment ──────────────▶│              │
 │                       │◀─ tracking no + label ───────────│              │
 │                       │─ update order (SHIPPED) ─▶│      │              │
 │                       │─ dispatch job notif resi ───────────────────────▶│
 │                       │◀── webhook status update (BiteshipWebhookController) │
 │                       │─ update order ─▶│             │              │
 │                       │─ dispatch job jika 'delivered' ─────────────────▶│
```

### 4.3 Product Image Upload Flow (Admin)

```
Admin    Livewire ProductManager   Cloudflare R2       PostgreSQL
 │─ upload foto ──▶│                      │                  │
 │  (WithFileUploads│─ validasi (Form      │                  │
 │   trait)         │  Request rule)       │                  │
 │                 │─ store via Filesystem │                  │
 │                 │  's3' disk ───────────▶│                  │
 │                 │◀── URL gambar ─────────│                  │
 │                 │─ simpan URL ───────────────────────────────▶│
```

**Keputusan kunci:** berbeda dari versi Next.js (presigned URL client→R2 langsung), Livewire `WithFileUploads` upload dulu ke server (temporary), baru service push ke R2. Trade-off: sedikit lebih banyak beban server dibanding presigned URL murni, tapi jauh lebih simpel diimplementasikan (built-in Livewire). Untuk skala MVP (upload oleh admin saja, bukan UGC volume tinggi), ini acceptable.

---

## 5. Keputusan Teknis & Rasional (ADR Ringkas)

| # | Keputusan | Alasan | Trade-off yang diterima |
|---|---|---|---|
| 1 | Laravel monolith (Blade+Livewire, bukan API+SPA) | Solo developer, 1 bahasa, operasional minim, Livewire cukup capable untuk UX interaktif | Tidak sefleksibel SPA untuk UX sangat dinamis; acceptable untuk skala ini |
| 2 | Eloquent sebagai ORM | Built-in, expressive, migration tooling matang | Kurang type-safe vs Prisma+TS — mitigasi dengan Larastan (PHPStan utk Laravel) |
| 3 | Redis untuk session + cache + queue | 1 service, 3 kebutuhan, kurangi footprint container | Perlu disiplin key namespacing supaya tidak collide |
| 4 | Webhook-driven payment | Real-time, reliable | Perlu handle retry & idempotency benar |
| 5 | Cloudflare R2 (Laravel Filesystem `s3` driver) | Egress gratis, S3-compatible, native support | Perlu config endpoint custom (bukan AWS S3 default) |
| 6 | Docker Compose (bukan Kubernetes) | Single VPS, tim kecil | Tidak ada auto-scaling otomatis — mitigasi resource limit + health check |
| 7 | Traefik reverse proxy | Auto SSL, native Docker label routing | Learning curve vs Nginx config langsung |
| 8 | Validasi harga di server (Form Request + Service, bukan trust client) | Cegah price manipulation | Tambahan logic di service layer |
| 9 | WAHA self-hosted untuk notifikasi WA | Tanpa biaya per-message, full control | Perlu maintain sesi sendiri, risk flag — mitigasi rate limit & nomor dedicated |
| 10 | Livewire file upload (bukan presigned URL langsung) | Jauh lebih simpel, built-in | Sedikit beban server tambahan — acceptable untuk volume admin-only |
| 11 | Queue worker container terpisah dari `app` | Request HTTP tidak boleh nunggu job (kirim WA, dll) | Butuh 1 container tambahan, resource ringan |
| 12 | Trix editor (built-in Laravel-friendly, via `laravel/breeze` ekosistem atau paket `livewire-ui`) untuk konten blog, bukan full CMS/headless CMS terpisah | Blog cukup sederhana (artikel + kategori string), tidak perlu kompleksitas headless CMS terpisah untuk skala ini | Kurang fleksibel dibanding CMS dedicated (misal untuk multi-author workflow kompleks) — cukup untuk kebutuhan MVP |

---

## 6. Environment & Secrets Management

- Semua kredensial di `.env` Laravel — **tidak pernah commit ke git** (`.gitignore` sejak commit pertama; `.env.example` sebagai template tanpa value asli).
- `APP_KEY` di-generate sekali (`php artisan key:generate`) dan **tidak boleh berubah** setelah production — data terenkripsi pakai key ini akan rusak kalau key berubah.
- `.env` production dikelola manual di VPS, terpisah dari development.

```env
# .env.example
APP_NAME="Way Kopi"
APP_ENV=production
APP_KEY=
APP_URL=https://waykopi.com

DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=waykopi
DB_USERNAME=
DB_PASSWORD=

REDIS_HOST=cache
REDIS_PASSWORD=
REDIS_PORT=6379

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

XENDIT_SECRET_KEY=
XENDIT_WEBHOOK_TOKEN=

BITESHIP_API_KEY=

WAHA_API_URL=http://waha:3000
WAHA_API_KEY=
WAHA_SESSION_NAME=default

FILESYSTEM_DISK=r2
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=auto
AWS_BUCKET=
AWS_ENDPOINT=
AWS_URL=
```

---

## 7. Deployment Topology (Docker Compose — ringkas)

```yaml
# docker-compose.prod.yml (ringkas, detail lengkap saat implementasi)
services:
  traefik:
    image: traefik:v3.0
    restart: unless-stopped

  app:
    build: ./docker/app
    restart: unless-stopped
    depends_on:
      db:
        condition: service_healthy
      cache:
        condition: service_started
    env_file: .env
    labels:
      - "traefik.enable=true"
      - "traefik.http.routers.app.rule=Host(`waykopi.com`)"
      - "traefik.http.routers.app.tls.certresolver=letsencrypt"
    deploy:
      resources:
        limits:
          memory: 1024M

  worker:
    build: ./docker/app       # image sama dengan app, command beda
    command: php artisan queue:work --tries=3 --backoff=10
    restart: unless-stopped
    depends_on: [db, cache]
    env_file: .env
    deploy:
      resources:
        limits:
          memory: 512M

  scheduler:
    build: ./docker/app
    command: sh -c "while true; do php artisan schedule:run; sleep 60; done"
    restart: unless-stopped
    depends_on: [db, cache]
    env_file: .env
    deploy:
      resources:
        limits:
          memory: 256M

  db:
    image: postgres:16-alpine
    restart: unless-stopped
    volumes:
      - pgdata:/var/lib/postgresql/data
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U $$POSTGRES_USER"]
      interval: 10s
      timeout: 5s
      retries: 5
    deploy:
      resources:
        limits:
          memory: 2048M

  cache:
    image: redis:7-alpine
    restart: unless-stopped
    deploy:
      resources:
        limits:
          memory: 512M

  waha:
    image: devlikeapro/waha:latest
    restart: unless-stopped
    volumes:
      - waha_sessions:/app/.sessions
    env_file: .env
    deploy:
      resources:
        limits:
          memory: 1024M

volumes:
  pgdata:
  waha_sessions:
```

**Spek VPS:** Hostinger KVM 4 — 4 vCPU, 16 GB RAM, 200 GB NVMe SSD, 16 TB bandwidth. Total limit eksplisit (`app` 1GB + `worker` 512MB + `scheduler` 256MB + `db` 2GB + `cache` 512MB + `waha` 1GB ≈ 5.3GB) menyisakan headroom besar (~10.7GB) untuk OS, Docker overhead, buffer PostgreSQL, dan pertumbuhan traffic.

**Domain:** `waykopi.com` (sudah dimiliki). Pastikan DNS A record mengarah ke IP VPS sebelum deploy pertama.

**OS:** Ubuntu 24.04 LTS — pastikan Docker Engine & Compose plugin versi terbaru yang kompatibel saat instalasi.

---

## 8. Verifikasi Dokumen Ini Benar

- [ ] Semua service di diagram §2 match dengan `docker-compose.yml` final
- [ ] `XenditWebhookController` & `BiteshipWebhookController` sudah ada signature/token verification sebelum go-live
- [ ] Queue worker jalan terpisah, job `SendWhatsAppNotification` benar async (tidak blocking request webhook)
- [ ] `php artisan schedule:run` terjadwal jalan tiap menit untuk `ReleaseExpiredOrderStock`
- [ ] `.env.example` selalu sinkron dengan variable yang benar-benar dipakai

---

## 9. Open Items

Item lama (spek VPS, domain, staging) sudah final — tidak berubah akibat pivot stack. Item baru akibat pivot:

1. Laravel Octane (FrankenPHP/Swoole) untuk performa, atau PHP-FPM biasa cukup untuk MVP? (rekomendasi: PHP-FPM dulu, Octane kalau ada bottleneck performa terukur)
2. Laravel Horizon (dashboard monitoring queue) — ditambahkan dari awal untuk visibility job WA/webhook, atau cukup log biasa dulu?
