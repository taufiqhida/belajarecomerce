# Cold Storage - Panduan Instalasi & Upload ke Hosting

Aplikasi ini dibangun menggunakan Laravel 11. Berikut adalah panduan langkah demi langkah untuk mengunggah (deploy) aplikasi ini ke CPanel / Shared Hosting dengan aman.

## Persiapan di Komputer Lokal (Sebelum Upload)

1. Buka terminal/command prompt di dalam folder project ini.
2. Pastikan file `.env` sudah sesuai (misal tidak ada kredensial lokal yang tertinggal).
3. Jalankan perintah optimasi Laravel agar performa web menjadi cepat di hosting:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. Eksport database lokal Anda (buka phpMyAdmin di localhost, pilih database Anda, lalu Export menjadi file `.sql`).
5. Jadikan seluruh isi folder project ini (semua file dan folder) ke dalam satu file `.zip` (misalnya: `project-store.zip`).

---

## Proses Upload & Setup di CPanel (Hosting)

### Langkah 1: Upload File Project
Sangat disarankan untuk **tidak** menaruh file inti/core sistem Laravel langsung di dalam `public_html` demi alasan keamanan.

1. Login ke akun cPanel Hosting Anda.
2. Buka menu **File Manager**.
3. Di root directory hosting / home (posisinya di luar dan sejajar dengan folder `public_html`), buat folder baru, misalnya `project-store`.
4. Masuk ke dalam folder `project-store` tersebut.
5. Klik **Upload**, lalu pilih file `project-store.zip` yang sudah Anda buat tadi.
6. Setelah upload selesai 100%, kembali ke File Manager, klik kanan pada file ZIP tersebut, lalu pilih **Extract**.
7. Pastikan semua file project (seperti folder `app`, `bootstrap`, file `.env`, dll) sudah terekstrak rapi di dalam folder tersebut.

### Langkah 2: Memindahkan Folder Public
1. Buka folder `public` yang ada di dalam instalasi Laravel tadi (`project-store/public`).
2. **Select All** (Pilih semua) file dan folder yang ada di dalamnya, termasuk `.htaccess` dan `index.php`.
3. Pindahkan (**Move**) semua file tersebut ke dalam folder utama web Anda, yaitu `public_html`.

### Langkah 3: Mengedit File `index.php`
Karena file `index.php` sekarang berada di `public_html`, sedangkan folder sistem Laravel ada di `project-store`, kita harus menyesuaikan lokasi filenya.

1. Masuk ke folder `public_html`.
2. Klik kanan pada file `index.php`, lalu pilih **Edit**.
3. Cari baris ini:
   ```php
   require __DIR__.'/../vendor/autoload.php';
   ```
   Lalu ubah menjadi:
   ```php
   require __DIR__.'/../project-store/vendor/autoload.php';
   ```
4. Cari juga baris ini:
   ```php
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   Lalu ubah menjadi:
   ```php
   $app = require_once __DIR__.'/../project-store/bootstrap/app.php';
   ```
5. Klik **Save Changes**.

### Langkah 4: Setup Database di cPanel
1. Kembali ke halaman utama cPanel, lalu buka **MySQL® Databases**.
2. Buat database baru (misalnya: `db_store`).
3. Buat user database baru beserta passwordnya (catat password ini).
4. Assign / Tambahkan user tersebut ke database `db_store`, lalu pastikan untuk mencentang kotak **ALL PRIVILEGES**.
5. Kembali ke cPanel, lalu buka **phpMyAdmin**.
6. Pilih database baru yang baru saja Anda buat, lalu masuk ke tab **Import**.
7. Unggah (Upload) file `.sql` milik Anda yang diekspor pada tahap persiapan. Klik Go/Import.

### Langkah 5: Konfigurasi `.env`
1. Buka kembali File Manager, cari folder `project-store` tempat inti Laravel berada.
2. Cari file bernama `.env` (Jika tidak terlihat, pergi ke bagian Settings/Pengaturan di sudut kanan atas File Manager, lalu centang *Show Hidden Files (dotfiles)*).
3. Klik kanan pada file `.env` dan pilih **Edit**.
4. Perbarui pengaturan berikut sesuai data web dan database hosting Anda:
   ```env
   APP_NAME="Cold Storage"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://namadomainanda.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=nama_database_cpanel_anda
   DB_USERNAME=user_database_cpanel_anda
   DB_PASSWORD=password_database_anda
   ```
5. Simpan perubahan.

### Langkah 6: Membuat Storage Link (Sangat Penting untuk Gambar)
Aplikasi memuat gambar produk dan media dari direktori `storage`. Di hosting, Anda tetap perlu membuat link `storage` agar bisa diakses dari `public_html`.

**Cara Termudah (Via Terminal cPanel jika tersedia):**
1. Buka menu **Terminal** di beranda cPanel.
2. Ketik perintah: `cd project-store` lalu tekan Enter.
3. Jalankan perintah: `php artisan storage:link`

**Alternatif (Via Web browser jika Terminal tidak tersedia di cPanel):**
1. Buat file baru bernama `buat_symlink.php` tepat di dalam folder `public_html`.
2. Isi dengan kode berikut:
   ```php
   <?php
   // Sesuaikan 'project-store' dengan nama folder Laravel Anda
   $targetFolder = $_SERVER['DOCUMENT_ROOT'].'/../project-store/storage/app/public';
   $linkFolder = $_SERVER['DOCUMENT_ROOT'].'/storage';
   
   if(symlink($targetFolder, $linkFolder)) {
       echo 'Symlink process successfully completed!';
   } else {
       echo 'Symlink process failed!';
   }
   ?>
   ```
3. Buka browser dan kunjungi `https://namadomainanda.com/buat_symlink.php`.
4. Jika berhasil, akan muncul tulisan berhasil. **SETELAH ITU, SEGARA HAPUS file `buat_symlink.php`** dari `public_html` masalah keamanan.

---

## FAQ & Solusi Kendala Populer

- **Muncul Error 500 (Server Error)**: 
  Cek versi PHP di cPanel Anda melalui fitur **Select PHP Version**. Laravel 11 membutuhkan setidaknya **PHP 8.2**. Pastikan ekstensi seperti `fileinfo`, `gd`, dan `pdo` telah diaktifkan di sana.
- **Gambar Produk Tidak Muncul / Error 404**: 
  Hal ini dikarenakan Anda melompati **Langkah 6**. Pastikan folder `storage` symlink sudah otomatis terbuat di dalam `public_html`.
- **Situs Lemot**: 
  Pastikan Anda telah menjalankan perintah cache di opsi persiapan (`config:cache`, `view:cache`). Pastikan `.env` bagian `APP_DEBUG` disetel ke `false`.

---
Selamat, website e-commerce Anda kini telah live di hosting! Halaman admin Filament dapat Anda akses di url `/admin` (misal: `https://namadomainanda.com/admin`).

