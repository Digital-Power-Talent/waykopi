# Database Schema Document — Way Kopi E-Commerce

**Versi:** 0.2 (Revisi — pivot stack ke Laravel/Eloquent)
**Tanggal:** 30 Juli 2026
**Referensi:** 01-PRD, 02-Architecture (v0.2)
**ORM:** Eloquent (Laravel 13) · **Database:** PostgreSQL 16

> **Catatan revisi:** struktur tabel, relasi, dan keputusan bisnis **tidak berubah** dari versi Prisma sebelumnya (stock reservation 1 jam, varian 200g, guest checkout tanpa claim). Yang berubah murni implementasi: dari Prisma schema ke Laravel migration + Eloquent model.

---

## 1. Entity Relationship Overview

```
User ──1:N── Address
User ──1:N── Order
User ──1:N── CartItem

Product ──1:N── ProductVariant   (MVP: 1 varian per produk, 200g)
Product ──1:N── ProductImage
ProductVariant ──1:N── CartItem
ProductVariant ──1:N── OrderItem

Order ──1:N── OrderItem
Order ──1:1── Payment
Order ──1:1── Shipment
Order ──1:N── OrderStatusHistory
Order ──1:N── NotificationLog

User ──1:N── Post   (author, admin-only)

WebhookEvent — tabel idempotency generik (Xendit & Biteship)
```

**Keputusan kunci (tidak berubah):**
- **`ProductVariant`** dipertahankan (bukan flatten ke `Product`) — MVP 1 varian (200g), struktur extensible untuk varian berat lain di fase 2 tanpa migration ulang.
- **`OrderItem` menyimpan snapshot** harga & nama produk — riwayat order tidak berubah walau produk asli diedit kemudian.
- **`Order` menyimpan alamat sebagai kolom langsung** (bukan foreign key ke `Address`) — order lama tidak rusak kalau customer edit/hapus alamat tersimpan.
- **`WebhookEvent`** — idempotency generik untuk Xendit & Biteship webhook.
- **`Post` (blog)** — `category` disimpan sebagai string sederhana, bukan tabel `categories` terpisah. Untuk volume konten blog skala kecil-menengah, over-engineering bikin tabel relasi terpisah belum perlu; gampang diubah jadi tabel relasi kalau nanti butuh multi-kategori per post atau kategori dengan metadata sendiri.

---

## 2. Migrations (Laravel)

```php
// database/migrations/xxxx_xx_xx_000001_create_users_table.php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('email')->nullable()->unique();
    $table->string('phone')->nullable()->unique();
    $table->string('password')->nullable();   // nullable, bisa login via OTP WA di fase depan
    $table->string('name');
    $table->enum('role', ['customer', 'admin'])->default('customer');
    $table->timestamps();

    $table->index('email');
    $table->index('phone');
});

// xxxx_xx_xx_000002_create_addresses_table.php
Schema::create('addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('label');              // "Rumah", "Kantor"
    $table->string('recipient_name');
    $table->string('phone');
    $table->string('province');
    $table->string('city');
    $table->string('district');
    $table->string('postal_code');
    $table->text('full_address');
    $table->boolean('is_default')->default(false);
    $table->timestamps();

    $table->index('user_id');
});

// xxxx_xx_xx_000003_create_products_table.php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('name');
    $table->text('description');
    $table->string('roast_profile')->nullable();
    $table->string('origin')->default('Lampung');
    $table->boolean('is_active')->default(true);   // soft-delete pattern, bukan hard delete
    $table->timestamps();

    $table->index('slug');
    $table->index('is_active');
});

// xxxx_xx_xx_000004_create_product_variants_table.php
Schema::create('product_variants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->integer('weight_grams');            // MVP: 200
    $table->string('sku')->unique();
    $table->decimal('price', 12, 2);             // WAJIB decimal, bukan float
    $table->integer('stock')->default(0);
    $table->integer('reserved_stock')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();

    $table->index('product_id');
    $table->index('sku');
});

// Tambahan CHECK constraint (Eloquent/Blueprint tidak generate ini otomatis)
DB::statement('ALTER TABLE product_variants ADD CONSTRAINT stock_non_negative CHECK (stock >= 0)');
DB::statement('ALTER TABLE product_variants ADD CONSTRAINT price_positive CHECK (price > 0)');

// xxxx_xx_xx_000005_create_product_images_table.php
Schema::create('product_images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('product_id')->constrained()->cascadeOnDelete();
    $table->string('url');           // URL publik dari Cloudflare R2
    $table->string('alt_text')->nullable();
    $table->integer('sort_order')->default(0);
    $table->timestamps();

    $table->index('product_id');
});

// xxxx_xx_xx_000006_create_cart_items_table.php
Schema::create('cart_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
    $table->string('session_id')->nullable();   // guest cart, disimpan di cookie
    $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
    $table->integer('quantity')->default(1);
    $table->timestamps();

    $table->unique(['user_id', 'product_variant_id']);
    $table->unique(['session_id', 'product_variant_id']);
    $table->index('user_id');
    $table->index('session_id');
});

// xxxx_xx_xx_000007_create_orders_table.php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->string('order_number')->unique();   // "WK-20260730-0001"
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

    // Guest contact info (dipakai kalau user_id null)
    $table->string('guest_email')->nullable();
    $table->string('guest_phone')->nullable();

    // Address snapshot — SENGAJA bukan foreign key, lihat §1 rationale
    $table->string('recipient_name');
    $table->string('recipient_phone');
    $table->text('shipping_address');
    $table->string('province');
    $table->string('city');
    $table->string('district');
    $table->string('postal_code');

    $table->decimal('subtotal', 12, 2);
    $table->decimal('shipping_cost', 12, 2);
    $table->decimal('total', 12, 2);

    $table->enum('status', [
        'pending_payment', 'paid', 'processing',
        'shipped', 'delivered', 'cancelled', 'expired',
    ])->default('pending_payment');

    $table->string('courier_name')->nullable();   // dari Biteship, misal "JNE REG"
    $table->text('notes')->nullable();
    $table->timestamp('expires_at')->nullable();  // 1 jam dari created_at, lihat §5
    $table->timestamps();

    $table->index('user_id');
    $table->index('order_number');
    $table->index('status');
    $table->index('created_at');
});

// xxxx_xx_xx_000008_create_order_items_table.php
Schema::create('order_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->foreignId('product_variant_id')->constrained();

    // Snapshot — harga/nama TIDAK boleh ikut berubah walau produk asli berubah
    $table->string('product_name');
    $table->string('variant_label');       // "200g"
    $table->decimal('price_at_purchase', 12, 2);
    $table->integer('quantity');
    $table->timestamps();

    $table->index('order_id');
    $table->index('product_variant_id');
});

// xxxx_xx_xx_000009_create_order_status_histories_table.php
Schema::create('order_status_histories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained()->cascadeOnDelete();
    $table->string('from_status')->nullable();
    $table->string('to_status');
    $table->foreignId('changed_by')->nullable()->constrained('users');   // null kalau otomatis (webhook)
    $table->string('note')->nullable();
    $table->timestamp('created_at')->useCurrent();

    $table->index('order_id');
});

// xxxx_xx_xx_000010_create_payments_table.php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
    $table->string('xendit_invoice_id')->nullable()->unique();
    $table->string('xendit_invoice_url')->nullable();
    $table->string('method')->nullable();       // "bank_transfer", "cod", dll
    $table->decimal('amount', 12, 2);
    $table->enum('status', ['pending', 'succeeded', 'failed', 'expired', 'refunded'])->default('pending');
    $table->timestamp('paid_at')->nullable();
    $table->timestamps();

    $table->index('xendit_invoice_id');
    $table->index('status');
});

// xxxx_xx_xx_000011_create_shipments_table.php
Schema::create('shipments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
    $table->string('biteship_order_id')->nullable()->unique();
    $table->string('tracking_number')->nullable();
    $table->string('courier_code')->nullable();     // "jne", "sicepat"
    $table->string('courier_service')->nullable();  // "REG", "YES"
    $table->enum('status', ['pending', 'booked', 'in_transit', 'delivered', 'failed'])->default('pending');
    $table->string('label_url')->nullable();
    $table->timestamp('shipped_at')->nullable();
    $table->timestamp('delivered_at')->nullable();
    $table->timestamps();

    $table->index('biteship_order_id');
    $table->index('tracking_number');
});

// xxxx_xx_xx_000012_create_notification_logs_table.php
Schema::create('notification_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
    $table->enum('channel', ['whatsapp', 'email'])->default('whatsapp');
    $table->string('recipient');            // nomor WA atau email tujuan
    $table->string('template_key');         // "order_confirmed", "order_shipped" — bukan hardcode teks
    $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
    $table->text('error_reason')->nullable();   // penting untuk debug WAHA session logout, dll
    $table->timestamp('sent_at')->nullable();
    $table->timestamps();

    $table->index('order_id');
    $table->index('status');
});

// xxxx_xx_xx_000013_create_webhook_events_table.php
Schema::create('webhook_events', function (Blueprint $table) {
    $table->id();
    $table->enum('source', ['xendit', 'biteship']);
    $table->string('event_id');       // ID unik dari provider
    $table->string('event_type');
    $table->jsonb('payload');
    $table->timestamp('processed_at')->useCurrent();

    $table->unique(['source', 'event_id']);   // KUNCI idempotency
});

// xxxx_xx_xx_000014_create_posts_table.php  (Blog — MVP)
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('author_id')->constrained('users');
    $table->string('title');
    $table->string('slug')->unique();
    $table->string('excerpt')->nullable();       // ringkasan untuk card list
    $table->longText('content');                 // rich text/markdown
    $table->string('cover_image_url')->nullable();
    $table->string('category')->nullable();      // "Recipes", "Lifestyle", "Coffee" — string sederhana, bukan tabel terpisah utk MVP
    $table->enum('status', ['draft', 'published'])->default('draft');
    $table->timestamp('published_at')->nullable();
    $table->timestamps();

    $table->index('slug');
    $table->index(['status', 'published_at']);
    $table->index('category');
});
```

---

## 3. Eloquent Models (ringkas)

```php
// app/Models/ProductVariant.php
class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'weight_grams', 'sku', 'price', 'stock', 'reserved_stock', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',   // wajib cast decimal, bukan float, hindari rounding error
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function availableStock(): int
    {
        return $this->stock - $this->reserved_stock;
    }
}

// app/Models/Order.php
class Order extends Model
{
    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number = 'WK-' . now()->format('Ymd') . '-' . str_pad(
                static::whereDate('created_at', now())->count() + 1, 4, '0', STR_PAD_LEFT
            );
            $order->expires_at = now()->addHour();   // 1 jam, lihat §5
        });
    }

    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function payment(): HasOne { return $this->hasOne(Payment::class); }
    public function shipment(): HasOne { return $this->hasOne(Shipment::class); }
    public function statusHistory(): HasMany { return $this->hasMany(OrderStatusHistory::class); }
}

// app/Models/WebhookEvent.php
class WebhookEvent extends Model
{
    protected $casts = ['payload' => 'array'];

    public static function alreadyProcessed(string $source, string $eventId): bool
    {
        return static::where('source', $source)->where('event_id', $eventId)->exists();
    }
}
```

**Keputusan kunci:** `price` di-cast sebagai `decimal:2` di setiap model yang punya kolom uang — Laravel akan otomatis handle sebagai string presisi tinggi, hindari kalkulasi float PHP native yang rawan rounding error untuk uang.

---

## 4. Constraint & Validasi Penting (di luar level database)

| Area | Constraint DB | Validasi tambahan (Form Request / Service layer) |
|---|---|---|
| Harga | `decimal(12,2)`, `CHECK (price > 0)` | Rule `numeric\|gt:0` di Form Request sebelum insert/update |
| Stok | `CHECK (stock >= 0)` | `StockReservationService`: reserve dulu (`reserved_stock += qty`), konfirmasi kurangi `stock` setelah payment `succeeded`. Kalau gagal/expired, `reserved_stock` dilepas |
| Order total | — | **Wajib dihitung ulang di `OrderService`** dari `ProductVariant->price` saat ini, bukan trust angka dari Livewire component/client |
| Webhook | `unique(source, event_id)` | Kalau insert gagal (duplikat) → return 200 OK ke provider tapi skip proses (provider akan retry terus kalau dapat non-2xx) |
| Email/Phone User | `unique` masing-masing | Minimal salah satu (email ATAU phone) harus diisi — validasi custom Form Request rule, tidak bisa diekspresikan langsung di migration |

---

## 5. Reservasi Stok — Alur Detail (via Eloquent, dalam DB Transaction)

```php
// app/Services/StockReservationService.php (ringkas)
DB::transaction(function () use ($variantId, $qty) {
    $variant = ProductVariant::lockForUpdate()->findOrFail($variantId);   // row lock, setara "SELECT ... FOR UPDATE"

    if ($variant->availableStock() < $qty) {
        throw new InsufficientStockException();
    }

    $variant->increment('reserved_stock', $qty);
    // ... create Order (status: pending_payment, expires_at: now()->addHour())
});

// Setelah payment SUCCEEDED (webhook):
DB::transaction(function () use ($order) {
    foreach ($order->items as $item) {
        $item->productVariant->decrement('stock', $item->quantity);
        $item->productVariant->decrement('reserved_stock', $item->quantity);
    }
    $order->update(['status' => 'paid']);
});

// Setelah payment EXPIRED/FAILED atau via scheduled command ReleaseExpiredOrderStock:
DB::transaction(function () use ($order) {
    foreach ($order->items as $item) {
        $item->productVariant->decrement('reserved_stock', $item->quantity);
    }
    $order->update(['status' => 'expired']);
});
```

> **Keputusan final:** `expires_at` = **1 jam** dari order dibuat (di-set otomatis via model event `creating`, lihat §3). `ReleaseExpiredOrderStock` (scheduled command, Architecture.md §3 & §7) jalan tiap beberapa menit via Laravel Scheduler untuk melepas stok order yang lewat `expires_at` tanpa webhook masuk — mencegah stok "hangus" kalau webhook Xendit gagal terkirim karena alasan network dll.

> Saat create Xendit invoice, set `invoice_duration` = 3600 detik (1 jam) — sinkron dengan `expires_at` di atas.

---

## 6. Indexing Strategy

Index ditandai di setiap migration untuk kolom yang sering dipakai di:
- **Filter** — `status` di `orders`, `is_active` di `products`
- **Lookup** — `slug`, `sku`, `order_number`, `email`, `phone`
- **Foreign key** — semua FK di-index eksplisit (Laravel migration `foreignId()->constrained()` otomatis index, konsisten dengan default PostgreSQL FK index behavior)

> ⚠️ Setelah beberapa bulan production, cek `EXPLAIN ANALYZE` untuk query admin dashboard (report revenue, order list dengan filter tanggal) — kemungkinan perlu composite index tambahan misal `(status, created_at)`.

---

## 7. Seeder Strategy

```php
// database/seeders/DatabaseSeeder.php — ringkas
public function run(): void
{
    User::factory()->create(['role' => 'admin', 'email' => 'admin@waykopi.com']);

    $product = Product::create([
        'slug' => 'way-kopi-robusta',
        'name' => 'Way Kopi Robusta',
        'description' => '...',
        'roast_profile' => 'Medium',
    ]);

    ProductVariant::create([
        'product_id' => $product->id,
        'weight_grams' => 200,   // MVP: 1 varian
        'sku' => 'WK-ROBUSTA-200',
        'price' => 45000,
        'stock' => 100,
    ]);

    // Dummy customer + order dengan berbagai status untuk testing UI admin
    Order::factory()->count(10)->create();
}
```

---

## 8. Verifikasi Dokumen Ini Benar

- [ ] `php artisan migrate:fresh --seed` jalan tanpa error di local
- [ ] CHECK constraint manual (stock, price) sudah dites dengan insert data invalid (harus gagal)
- [ ] `php artisan tinker` bisa query semua relasi Eloquent tanpa N+1 query tak terduga (cek dengan `DB::enableQueryLog()`)
- [ ] Test race condition reservasi stok dengan concurrent request (misal k6/artillery) sebelum go-live — pastikan `lockForUpdate()` benar-benar mencegah overselling

---

## 9. Open Items

Tidak ada open item tersisa — semua keputusan bisnis (durasi expiry, varian, guest checkout) sudah final dari sesi sebelumnya dan tetap berlaku di pivot stack ini.
