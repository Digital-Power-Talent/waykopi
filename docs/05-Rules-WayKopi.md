# Rules & Contribution Guide — Way Kopi E-Commerce

**Versi:** 0.2 (Revisi — pivot stack ke Laravel)
**Tanggal:** 30 Juli 2026
**Referensi:** 01-PRD, 02-Architecture, 03-Design, 04-Schema (WayKopi, v0.2)

Dokumen ini mengikat semua kontributor — manusia maupun AI (Claude Code, Copilot, dll). Tujuannya menjaga codebase konsisten, aman, dan predictable untuk di-maintain jangka panjang oleh solo developer.

---

## 1. Naming Conventions

| Item | Konvensi | Contoh |
|---|---|---|
| Class (Model, Controller, Service, Livewire Component) | PascalCase (StudlyCase) | `ProductVariant`, `OrderService`, `Checkout` |
| Method & variable | camelCase | `calculateShippingCost()`, `$orderTotal` |
| Konstanta class | UPPER_SNAKE_CASE | `const MAX_CART_ITEMS = 10;` |
| File PHP | Ikut nama class (PascalCase) | `ProductVariant.php`, `OrderService.php` |
| Blade view file | kebab-case, `.blade.php` | `product-card.blade.php` |
| Database table | snake_case, plural | `product_variants`, `order_items` |
| Database column | snake_case | `weight_grams`, `reserved_stock` |
| Route name | kebab-case dengan dot notation | `admin.orders.show` |
| Environment variable | UPPER_SNAKE_CASE | `XENDIT_SECRET_KEY` |
| Git branch | `type/nama-ringkas` | `feat/checkout-flow`, `fix/stock-reservation-race` |

---

## 2. PHP & Laravel Code Style

- **PSR-12** sebagai baseline formatting — dijalankan otomatis via Laravel Pint (`./vendor/bin/pint`) sebelum commit.
- **Larastan (PHPStan untuk Laravel)** di level minimal 5 — static analysis wajib pass, terutama untuk `app/Services/` yang berisi business logic kritis (stock reservation, payment).
- **Return type eksplisit** di semua method `Service` dan `Repository` (kalau dipakai) — tidak infer, khususnya method yang dipanggil dari Livewire component atau controller.
- **Form Request class** untuk validasi setiap input — tidak ada `$request->validate()` inline di controller/Livewire untuk validasi kompleks (boleh inline untuk validasi sangat sederhana, 1-2 field).
- **Service layer wajib** untuk business logic — Livewire component dan Controller hanya orchestrate (panggil service, render view), tidak berisi logic kalkulasi/keputusan bisnis langsung.
- **Eloquent, bukan raw query**, kecuali untuk kasus performa spesifik yang didokumentasikan alasannya (misal report kompleks yang butuh raw SQL demi performa — beri komentar alasan).

```php
// ❌ Hindari — logic bisnis di Livewire component
class Checkout extends Component
{
    public function submit()
    {
        $total = $this->cartItems->sum(fn($i) => $i->variant->price * $i->qty); // logic harusnya di service
        // ...
    }
}

// ✅
class Checkout extends Component
{
    public function submit(OrderService $orderService)
    {
        $order = $orderService->createFromCart($this->cartItems, $this->address);
        // ...
    }
}
```

---

## 3. Git & Commit Conventions

- **Conventional Commits**: `type(scope): deskripsi singkat`
  - Type: `feat`, `fix`, `refactor`, `docs`, `chore`, `test`, `perf`
  - Contoh: `feat(checkout): add stock reservation with row lock`
- **1 commit = 1 perubahan logis** — jangan gabung `feat` besar dengan `fix` tidak terkait.
- **Branch dari `main`, PR balik ke `main`** — tidak ada long-lived branch lain untuk MVP. Untuk solo developer, PR tetap dibuat (bukan commit langsung ke `main`) supaya CI checks (§9) jalan sebagai gate sebelum merge.
- **Tidak ada force-push ke `main`** dalam kondisi apapun.
- **Commit message dalam Bahasa Inggris**, diskusi/dokumentasi boleh Bahasa Indonesia.
- **Tidak commit `vendor/`, `.env`, `storage/*.key`** — pastikan `.gitignore` Laravel default tetap terjaga, jangan pernah override demi "biar gampang".

---

## 4. Testing Requirements

Framework: **Pest** (lebih ringkas dari PHPUnit, tapi kompatibel — pilih salah satu dan konsisten, rekomendasi Pest untuk keterbacaan).

| Area | Requirement minimum |
|---|---|
| `app/Services/` | Unit test wajib — kalkulasi harga, reservasi stok, status transition order |
| Webhook controller (Xendit, Biteship) | Test idempotency (kirim event sama 2x, hasil harus sama) dan signature verification (payload invalid harus ditolak dengan 4xx) |
| Checkout flow | Feature test: race condition 2 request bersamaan pada stok terbatas (pakai `DB::transaction` testing pattern atau queue concurrent test) |
| Livewire component kritis | `Livewire::test()` untuk Cart, Checkout, ProductManager — minimal happy path + 1 error case |
| Model | Factory + minimal test relasi (`assertTrue($order->items->isNotEmpty())` dsb) untuk model dengan business rule di `booted()` |

**Sebelum merge ke `main`:** semua test harus pass (`php artisan test` atau `./vendor/bin/pest`). Tidak ada `->skip()` atau comment-out test yang gagal supaya PR "lolos".

---

## 5. Security Rules (Non-Negotiable)

Hard rule — pelanggaran ditolak di review tanpa pengecualian:

1. **Tidak ada secret/API key hardcoded** di kode, commit history, atau comment. Semua lewat `.env`, `.env` masuk `.gitignore` sejak commit pertama.
2. **Semua input user divalidasi di server** via Form Request — tidak percaya validasi client-side (Livewire `#[Validate]` boleh untuk UX realtime, tapi validasi final wajib di server saat submit, khususnya harga/quantity/total checkout).
3. **Webhook wajib verifikasi signature/token** (Xendit callback token, Biteship signature) sebelum diproses — tidak ada webhook endpoint yang trust by default.
4. **Eloquent/query builder, bukan raw SQL string concatenation** — kalau terpaksa raw query, wajib pakai parameter binding (`DB::select('... where id = ?', [$id])`), tidak pernah interpolasi variable langsung ke string SQL.
5. **Rate limiting wajib** (Laravel throttle middleware) di route: login, checkout, webhook.
6. **Dependency baru dicek** (`composer audit`) sebelum ditambahkan — terutama package yang handle payment/auth.
7. **Mass assignment protection** — `$fillable` di setiap model wajib eksplisit, tidak pakai `$guarded = []` kecuali ada alasan kuat yang didokumentasikan.
8. **Log tidak boleh berisi data sensitif** — jangan log payload webhook mentah tanpa redaction, jangan log password/token bahkan yang sudah di-hash.
9. **Route `/admin/*` wajib di-guard** middleware `EnsureIsAdmin` — tidak ada admin route yang hanya dilindungi oleh "security by obscurity" (URL tersembunyi).

---

## 6. Batasan untuk AI Contributor (Claude Code, Copilot, dll)

### 6.1 Boleh dilakukan tanpa konfirmasi tambahan
- Menulis/edit kode mengikuti pattern & struktur yang sudah ditetapkan di 4 dokumen sebelumnya
- Menambah unit/feature test untuk kode yang sudah ada
- Perbaikan bug dengan scope jelas dan surgical
- Refactor kecil yang tidak mengubah behavior (rename, extract method) — dengan test tetap dijalankan setelahnya

### 6.2 Wajib konfirmasi dulu ke manusia sebelum eksekusi
- **Membuat/mengubah migration** yang sudah pernah di-`migrate` di production — perubahan schema production butuh review manual (potensi data loss)
- **Menambah dependency baru** ke `composer.json` — jelaskan alasan & alternatif yang dipertimbangkan
- **Mengubah alur payment/webhook** (checkout, Xendit, Biteship integration) — area paling sensitif
- **Mengubah `docker-compose.yml`/Traefik config** di production — risiko downtime
- **Refactor besar lintas file** (>5 file sekaligus) — beri ringkasan rencana dulu
- **Mengubah struktur folder** yang sudah ditetapkan di Architecture.md §3
- **Menonaktifkan/mengubah CHECK constraint** di database (stock, price)

### 6.3 Dilarang total (tidak ada pengecualian)
- Commit/push langsung ke `main` tanpa PR & CI checks
- Menonaktifkan test yang gagal demi "cepat selesai" (`->skip()`, comment out, dll)
- Hardcode credential apapun, termasuk "sementara untuk testing"
- Menghapus `WebhookEvent` idempotency check atau signature verification demi "menyederhanakan debug"
- Mengubah `expires_at`/reservasi stok logic (Schema.md §5) tanpa memahami race condition-nya — kalau perlu diubah, jelaskan dulu trade-off-nya
- Set `$guarded = []` di model tanpa alasan eksplisit didokumentasikan
- Generate/commit data dummy yang menyerupai data customer asli (privasi)
- Mengasumsikan requirement ambigu tanpa menyebutkannya — tanyakan, jangan menebak diam-diam

### 6.4 Prinsip umum untuk AI contributor
- **Surgical edits, bukan rewrite total** — kecuali diminta atau ada alasan kuat yang dijelaskan dulu
- **Tantang keputusan berisiko** sebelum eksekusi
- **Definisikan cara verifikasi** setiap perubahan (test yang jalan, langkah manual testing, dll)

---

## 7. Pull Request Checklist

- [ ] Judul PR mengikuti Conventional Commits format
- [ ] Deskripsi PR menjelaskan **apa** yang berubah dan **kenapa**
- [ ] Test baru ditambahkan untuk logic baru (atau dijelaskan kenapa tidak perlu)
- [ ] `./vendor/bin/pint --test` dan `./vendor/bin/phpstan analyse` pass
- [ ] Tidak ada `dd()`, `dump()`, `Log::debug()` sisa debugging tertinggal
- [ ] Kalau menyentuh migration: sudah dites `migrate` & `migrate:rollback` di local
- [ ] Kalau menyentuh security-sensitive area (§5): disebutkan eksplisit di deskripsi PR

---

## 8. Dokumentasi Wajib Diperbarui Bersamaan Kode

- Ubah struktur folder → update Architecture.md §3
- Ubah/tambah tabel database → update Schema.md
- Ubah keputusan teknis besar (ganti library, ganti provider) → tambah baris baru di ADR table (Architecture.md §5), jangan hapus baris lama
- Ubah komponen/design token → update Design.md §2

---

## 9. CI/CD — GitHub Actions

```yaml
# .github/workflows/ci.yml
name: CI

on:
  pull_request:
    branches: [main]
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_PASSWORD: postgres
        ports: ["5432:5432"]
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5
      redis:
        image: redis:7-alpine
        ports: ["6379:6379"]
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pgsql, redis
      - run: composer install --no-progress --prefer-dist
      - run: cp .env.example .env
      - run: php artisan key:generate
      - run: php artisan migrate --force
        env:
          DB_CONNECTION: pgsql
          DB_HOST: localhost
          DB_PASSWORD: postgres
      - run: ./vendor/bin/pint --test
      - run: ./vendor/bin/phpstan analyse
      - run: ./vendor/bin/pest
```

**Keputusan kunci:** test jalan dengan PostgreSQL & Redis service container asli di CI (bukan mock/SQLite in-memory) — supaya CHECK constraint dan behavior queue/cache yang spesifik PostgreSQL/Redis benar-benar tervalidasi.

## 10. Verifikasi Dokumen Ini Diikuti

- [ ] Git hook (via `captainhook` atau `husky`-equivalent PHP) terpasang, menjalankan Pint + Larastan sebelum commit
- [ ] GitHub Actions menjalankan test + lint + static analysis di setiap PR sebelum bisa merge
- [ ] Branch protection rule di `main`: require status check pass (test + lint + analysis), disallow force-push. Require PR review dari reviewer lain di-skip (solo developer) — self-review checklist §7 wajib dicek manual sebelum merge sendiri

---

## 11. Open Items

Tidak ada open item tersisa — solo developer & GitHub Actions sudah dikonfirmasi sebelumnya dan tetap berlaku di pivot stack ini.
