# Panduan Lengkap Manual Deployment Way Kopi v.2 ke Hostinger Web Hosting

Dokumen ini adalah panduan langkah-demi-langkah untuk melakukan deployment manual aplikasi **Way Kopi v.2** (Laravel 13 + Livewire 3) ke hosting Hostinger (**hPanel** / **cPanel**) tanpa menggunakan Docker ataupun GitHub Actions.

---

## 1. Persiapan di Komputer Lokal (Sebelum Upload)

Sebelum mengunggah file ke Hostinger, lakukan build asset frontend dan instalasi dependensi produksi:

```bash
# 1. Pastikan seluruh dependensi frontend terkompilasi
npm install
npm run build

# 2. Pastikan file vendor teroptimasi untuk produksi
composer install --optimize-autoloader --no-dev

# 3. Jalankan pengujian terakhir
php artisan test
```

Setelah `npm run build` selesai, pastikan folder `public/build` sudah terbuat dan berisi file manifest serta asset CSS/JS hasil bundling.

---

## 2. Pilihan Struktur Folder di Hostinger

Ada 2 metode penataan file di Hostinger hPanel:

### Opsi A: Subfolder Terpisah (Sangat Direkomendasikan untuk Keamanan)

1. Buat folder di luar `public_html`, misalnya `/home/uXXXXXXX/waykopi/`.
2. Upload seluruh source code proyek Laravel ke dalam folder `/home/uXXXXXXX/waykopi/`.
3. Pindahkan atau upload isi folder `public/` ke dalam `/home/uXXXXXXX/public_html/`.
4. Edit file `public_html/index.php`:

   ```php
   // Ubah path autoload & bootstrap agar mengarah ke folder waykopi
   require __DIR__.'/../waykopi/vendor/autoload.php';
   $app = require_once __DIR__.'/../waykopi/bootstrap/app.php';
   ```

### Opsi B: Seluruh Proyek di dalam `public_html` (Didukung oleh `.htaccess` Root)

1. Upload seluruh folder proyek ke dalam `/home/uXXXXXXX/public_html/`.
2. Pastikan file [`.htaccess`](file:///d:/Project/Waykopi%20v.2/.htaccess) di root sudah terpasang. File ini akan otomatis mengarahkan seluruh request pengunjung ke folder `/public` dan memblokir akses publik ke file sensitif (`.env`, `storage`, `artisan`, dll).

---

## 3. Konfigurasi Database di Hostinger hPanel

1. Buka menu **Databases** / **MySQL Databases** di hPanel Hostinger.
2. Buat database baru (misal: `uXXXXXXX_waykopi`), buat username dan password database.
3. Buat file `.env` di server hosting (atau salin dari `.env.example`) dan isi kredensial produksi:

```dotenv
APP_NAME="Way Kopi"
APP_ENV=production
APP_KEY=base64:GENERATE_KEY_DARI_LOCAL
APP_DEBUG=false
APP_URL=https://waykopi.com

# Database (MySQL Hostinger)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=uXXXXXXX_waykopi
DB_USERNAME=uXXXXXXX_waykopi_user
DB_PASSWORD="password_database_anda"

# Cache & Session (Jika tidak ada Redis di paket hosting, gunakan database/file)
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file

# Biteship
BITESHIP_API_KEY=biteship_live_xxxx
BITESHIP_ORIGIN_AREA_ID=IDNP9IDNC74IDND6752IDZ16320
BITESHIP_ORIGIN_POSTAL_CODE=16320

# Xendit Payment Gateway
XENDIT_SECRET_KEY=xnd_production_xxxx
XENDIT_WEBHOOK_TOKEN=token_verifikasi_webhook_xendit

# Storage Gambar Cloudflare R2
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=r2_access_key_id
AWS_SECRET_ACCESS_KEY=r2_secret_access_key
AWS_DEFAULT_REGION=auto
AWS_BUCKET=waykopi-images
AWS_ENDPOINT=https://xxxx.r2.cloudflarestorage.com
AWS_URL=https://images.waykopi.com
AWS_USE_PATH_STYLE_ENDPOINT=false

# WAHA WhatsApp Service
WAHA_BASE_URL=https://waha.waykopi.com
WAHA_SESSION=default
WAHA_API_KEY=
```

---

## 4. Eksekusi Perintah Artisan di Server Hostinger

Buka menu **SSH Access** di hPanel Hostinger, login melalui terminal (Putty / Terminal bawaan):

```bash
# Pindah ke direktori proyek
cd /home/uXXXXXXX/public_html   # atau path folder Anda

# 1. Jalankan migrasi database dan buat tabel
php artisan migrate --force

# 2. Buat symbolic link untuk storage
php artisan storage:link

# 3. Optimasi cache konfigurasi, route, dan view
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan optimize
```

> **Catatan Izin Folder (Permissions):**
> Pastikan folder `storage` dan `bootstrap/cache` memiliki permission `775`:
>
> ```bash
> chmod -R 775 storage bootstrap/cache
> ```

---

## 5. Konfigurasi Cron Jobs di Hostinger hPanel

Agar pembatalan otomatis pesanan yang kedaluwarsa (`CancelExpiredOrdersCommand`) dan job background berjalan teratur, buat Cron Job di hPanel:

1. Buka menu **Advanced** &rarr; **Cron Jobs** di hPanel Hostinger.
2. Tambahkan Cron Job baru:
   - **Type:** Custom
   - **Schedule:** Setiap Menit (`* * * * *`)
   - **Command:**

     ```bash
     /usr/bin/php /home/uXXXXXXX/public_html/artisan schedule:run >> /dev/null 2>&1
     ```

     *(Ganti `/home/uXXXXXXX/public_html` dengan direktori root proyek Anda)*

3. **Background Queue Worker (Untuk Notifikasi WA):**
   Tambahkan satu Cron Job untuk memproses antrean queue jika tidak menggunakan daemon SSH:
   - **Schedule:** Setiap Menit (`* * * * *`)
   - **Command:**

     ```bash
     /usr/bin/php /home/uXXXXXXX/public_html/artisan queue:work --stop-when-empty >> /dev/null 2>&1
     ```

---

## 6. Pengaturan Webhook Xendit di Dashboard Xendit

1. Login ke [Dashboard Xendit](https://dashboard.xendit.co).
2. Masuk ke menu **Settings** &rarr; **Webhooks**.
3. Tambahkan URL webhook produksi:

   ```text
   https://waykopi.com/webhooks/xendit
   ```

4. Masukkan **Webhook Verification Token** dari Xendit ke variable `XENDIT_WEBHOOK_TOKEN` di file `.env`.
5. Centang event: **Invoice Paid**, **Invoice Expired**.

---

## 7. Checklist Verifikasi Pasca-Deployment

- [ ] Halaman depan `https://waykopi.com` dapat diakses dengan layout & styling Tailwind rapi.
- [ ] Gambar produk, banner, dan cerita petani tampil utuh.
- [ ] Katalog & filter kopi berfungsi dinamis (biji utuh / bubuk / varian berat).
- [ ] Autocomplete area kecamatan Biteship merespon cepat (ter-cache otomatis).
- [ ] Checkout dengan metode *Transfer Bank Langsung* menampilkan kode unik 3 digit.
- [ ] Checkout dengan metode *COD* dapat diproses langsung.
- [ ] Admin panel `https://waykopi.com/admin` dapat diakses dengan akun admin.
- [ ] Perubahan status pesanan di admin mencatat riwayat ke `OrderStatusHistory`.
- [ ] Invoice cetak resi pengiriman dapat dibuka dan dicetak.

---

## 8. Solusi Error Umum Deployment

### Error: `SQLSTATE[HY000] [1045] Access denied for user 'uXXXXXXXX_user'@'127.0.0.1'`

Penyebab utama di Hostinger shared hosting:

1. **Penggunaan IP `127.0.0.1` vs `localhost`**: Di Hostinger, user MySQL didaftarkan dengan host `localhost` (Unix socket). Ubah `DB_HOST=127.0.0.1` menjadi `DB_HOST=localhost` pada file `.env`.
2. **Password Salah / Mengandung Karakter Khusus**: Jika password memiliki karakter khusus (seperti `#`, `$`, `!`), wajib dibungkus tanda petik dua di `.env`: `DB_PASSWORD="PasswordKopi123!"`.
3. **Cache Konfigurasi Lama**: Jalankan `php artisan config:clear` terlebih dahulu sebelum mengulang `php artisan migrate --force`.

### Error: `Call to undefined function Illuminate\Filesystem\exec()` (saat `php artisan storage:link`)

Penyebab utama di Hostinger shared hosting:
Fungsi `symlink` dan `exec` diblokir secara default oleh Hostinger pada file `php.ini` demi alasan keamanan.

#### Solusi 1: Aktifkan via hPanel Hostinger (Sangat Mudah)

1. Buka **hPanel Hostinger** &rarr; **Advanced** &rarr; **PHP Configuration**.
2. Pilih tab **PHP Functions**.
3. Pada daftar *Disable functions*, hapus `exec` dan `symlink`.
4. Klik **Save**, lalu jalankan ulang `php artisan storage:link` di terminal SSH.

#### Solusi 2: Buat Symbolic Link Manual via Terminal SSH

Jika fungsi PHP tidak ingin diaktifkan, jalankan perintah Linux native di terminal SSH:

```bash
ln -s /home/uXXXXXXX/domains/waykopi.com/storage/app/public /home/uXXXXXXX/domains/waykopi.com/public_html/storage
```

*(Ganti `/home/uXXXXXXX/domains/waykopi.com` dengan path absolut akun hosting Anda)*
