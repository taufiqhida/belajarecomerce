# 🚀 Panduan Deploy ke Domainesia

> **Project:** Cold Storage (Laravel 12 + Filament 3)  
> **PHP Required:** 8.2 atau lebih tinggi  
> **Laravel:** 12.x

---

## Persyaratan Hosting

Sebelum mulai, pastikan paket hosting Domainesia kamu mendukung:

| Persyaratan | Nilai |
|---|---|
| PHP | **8.2 / 8.3 / 8.4** |
| MySQL | 8.0+ |
| Ekstensi PHP | `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath` |
| SSH / Terminal | ✅ Diperlukan (paket Business ke atas) |
| Composer | ✅ Tersedia di cPanel |

---

## Langkah 1 — Siapkan File Lokal

Di komputer kamu (Laragon), jalankan perintah ini:

```bash
# 1. Bersihkan cache lokal
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# 2. Build aset frontend
npm run build

# 3. Install dependencies tanpa dev packages
composer install --optimize-autoloader --no-dev
```

---

## Langkah 2 — Upload File ke Hosting

### Opsi A — via Git (Direkomendasikan)

1. Push semua perubahan ke GitHub terlebih dahulu:
   ```bash
   git add .
   git commit -m "chore: ready for production"
   git push origin main
   ```

2. Di cPanel Domainesia → **Terminal** (atau SSH), jalankan:
   ```bash
   cd ~/public_html  # atau folder domain kamu
   git clone https://github.com/USERNAME/REPO_NAME.git .
   ```

### Opsi B — via File Manager / FTP

Upload **semua file** kecuali folder berikut:
- `node_modules/`
- `.git/`

> ⚠️ Pastikan folder `public/` menjadi **Document Root** domain kamu di cPanel.

---

## Langkah 3 — Atur Document Root

Di cPanel Domainesia:

1. Buka **Domains** → pilih domain kamu → klik **Manage**
2. Ubah **Document Root** menjadi:
   ```
   public_html/barulagi/public
   ```
   *(sesuaikan dengan nama folder project kamu)*

---

## Langkah 4 — Buat Database MySQL

1. cPanel → **MySQL Databases**
2. Buat database baru, misal: `taufiq_barulagi`
3. Buat user MySQL baru dan beri **All Privileges** ke database tersebut
4. Catat:
   - `DB_DATABASE` = nama database
   - `DB_USERNAME` = nama user
   - `DB_PASSWORD` = password user
   - `DB_HOST` = `localhost`

---

## Langkah 5 — Buat File `.env` di Hosting

Di Terminal cPanel, masuk ke folder project:
```bash
cd ~/public_html/barulagi   # ganti sesuai folder kamu
cp .env.example .env
nano .env                    # atau gunakan File Manager untuk edit
```

Edit nilai-nilai berikut:
```env
APP_NAME="Cold Storage"
APP_ENV=production
APP_KEY=                    # akan diisi di langkah berikutnya
APP_DEBUG=false
APP_URL=https://domainmu.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=taufiq_barulagi   # sesuaikan
DB_USERNAME=user_db          # sesuaikan
DB_PASSWORD=password_db      # sesuaikan

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

FILESYSTEM_DISK=local
```

---

## Langkah 6 — Jalankan Perintah Artisan

Di Terminal cPanel:

```bash
cd ~/public_html/barulagi

# Generate APP_KEY
php artisan key:generate

# Install Composer dependencies (jika belum)
composer install --optimize-autoloader --no-dev

# Jalankan migrasi database
php artisan migrate --force

# Cache untuk performa production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Publish aset Filament
php artisan filament:upgrade
php artisan vendor:publish --tag=filament-assets --force
```

---

## Langkah 7 — Set Permission Folder

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Langkah 8 — Buat Link Storage

```bash
php artisan storage:link
```

> Jika muncul error "sudah ada", hapus dulu symlink lama:
> ```bash
> rm public/storage
> php artisan storage:link
> ```

---

## Langkah 9 — Atur PHP Version

Di cPanel Domainesia:
1. Buka **MultiPHP Manager** atau **PHP Selector**
2. Pilih domain kamu
3. Set PHP Version ke **8.2**, **8.3**, atau **8.4**

---

## Langkah 10 — Verifikasi

Buka browser dan akses:

| URL | Harapan |
|---|---|
| `https://domainmu.com` | Halaman utama store muncul |
| `https://domainmu.com/admin` | Halaman login Filament admin |
| `https://domainmu.com/up` | Menampilkan `{"status":"up"}` |

---

## Troubleshooting Umum

| Error | Solusi |
|---|---|
| `500 Internal Server Error` | Cek `storage/logs/laravel.log`, pastikan `APP_DEBUG=false` dan file `.env` sudah benar |
| `Class not found` | Jalankan `composer dump-autoload` |
| Gambar/file tidak muncul | Pastikan `storage:link` sudah dijalankan |
| Halaman login Filament error | Jalankan `php artisan filament:upgrade` |
| `SQLSTATE[HY000]` | Cek kredensial database di `.env` |
| `Permission denied` | Jalankan `chmod -R 775 storage bootstrap/cache` |

---

## Membuat Akun Admin Pertama

Setelah berhasil deploy, buat akun admin via terminal:

```bash
php artisan make:filament-user
```

Ikuti promptnya (isi nama, email, password).

---

## Update Setelah Perubahan Kode

Jika ada update kode baru dari GitHub:

```bash
cd ~/public_html/barulagi
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

*Dibuat untuk project Cold Storage — Laravel 12 / Filament 3*

