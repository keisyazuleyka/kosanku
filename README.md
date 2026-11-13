# Kosanku — Web App Penyewaan Kost (Full-Stack Laravel + MySQL + HeidiSQL)

> **Brand**: Kosanku  
> **Tagline**: *Find your space. Feel at home.*  
> **Target**: Mahasiswa dan pekerja Indonesia.

Kosanku adalah aplikasi web penyewaan kost lengkap berbasis **Laravel**, **MySQL**, **Blade**, dan **Tailwind CSS**. Aplikasi ini mengintegrasikan tiga peran pengguna (**Admin**, **Pemilik Kost / Owner**, dan **Penyewa / Tenant**), alur pengajuan booking transparan dengan snapshot harga, moderasi ulasan dan laporan, audit log keamanan, serta manajemen database lokal via **HeidiSQL**.

---

## 🎨 Palet Warna Brand

- **Forest Green**: `#173F35` (Warna utama header, tombol, identitas)
- **Deep Green**: `#102F28` (Sidebar admin & owner, footer)
- **Warm Cream**: `#F6F3EC` (Background utama aplikasi)
- **Sage**: `#D8E6D5` (Aksen lembut, tag fasilitas, highlight)
- **Accent**: `#E9A86B` (Aksen tombol & badge promo)
- **Text / Ink**: `#202722` (Teks utama)
- **Mute**: `#737B74` (Teks sekunder / keterangan)
- **Border**: `#E7E9E3` (Garis pemisah)
- **White**: `#FFFFFF` (Card dan kontainer)

Font yang digunakan: **Plus Jakarta Sans**.

---

## 👥 Role & Hak Akses Pengguna

1. **Admin** (`/admin`)
   - Overview statistik dari database MySQL (total pengguna, pemilik, penyewa, kost aktif, antrian verifikasi, total booking, grafik 14 hari).
   - Manajemen kost (filter kota/status, ubah status, penangguhan, aktivasi, hapus).
   - Antrian verifikasi listing (status pending, approved, rejected, permintaan revisi, riwayat keputusan).
   - Manajemen pengguna (pencarian, filter role/status, penangguhan/aktivasi akun aman).
   - Manajemen booking & resolusi sengketa (catatan sengketa tercatat ke audit log).
   - Moderasi ulasan (sembunyikan/tampilkan ulasan publik).
   - Pemrosesan laporan pengguna atas kost bermasalah.
   - Manajemen kategori dan fasilitas kost.
   - Audit log permanen (aktor, tindakan, objek, waktu, metadata aman).
   - Pengaturan platform (nama situs, kontak resmi, batas harga minimum, konten halaman statis).

2. **Pemilik Kost / Owner** (`/owner`)
   - Dashboard ringkasan kost dan booking masuk.
   - Tambah, edit, dan hapus listing kost milik sendiri.
   - Unggah multiple foto (JPG, PNG, WebP) dengan thumbnail dan fallback placeholder.
   - Pengaturan harga sewa per bulan, deposit, biaya tambahan, dan aturan kost.
   - Pengaturan kapasitas dan ketersediaan kamar.
   - Proses pengajuan booking penyewa (setujui atau tolak dengan alasan).
   - Selesaikan masa sewa penyewa.
   - Daftar penyewa aktif dan riwayat penyewa.
   - Ulasan dari penyewa untuk unit kost miliknya.
   - Edit profil pemilik.

3. **Penyewa / Tenant** (`/dashboard` & Halaman Publik)
   - Cari kost dengan filter pencarian: nama, kota, kecamatan, tipe (Putra, Putri, Campur), budget harga, fasilitas, dan sorting.
   - Simpan kost ke daftar Favorit (`/favorit`).
   - Ajukan sewa dengan tanggal masuk, durasi sewa (bulan), dan jumlah kamar.
   - Kalkulasi estimasi total biaya sewa + deposit secara otomatis.
   - Pantau status booking di dashboard (`/dashboard/bookings`).
   - Batalkan pengajuan sewa jika status masih *Pending*.
   - Beri ulasan bintang (1-5) dan testimoni setelah masa sewa berstatus *Completed*.
   - Laporkan listing kost yang tidak sesuai atau bermasalah.
   - Edit profil penyewa.

---

## 🛠️ Prasyarat Lingkungan

Pastikan perangkat lokal Anda telah terpasang:
- **PHP** versi 8.2 atau 8.3 (dengan ekstensi `pdo_mysql`, `mbstring`, `fileinfo`, `openssl`).
- **Composer** (package manager PHP).
- **Node.js** (v18+) dan **npm**.
- **MySQL Server** atau **MariaDB** (bisa melalui Laragon, XAMPP, atau instalasi mandiri).
- **HeidiSQL** (aplikasi GUI manajemen database MySQL untuk Windows).

---

## 🚀 Panduan Setup & Menjalankan Project

Ikuti 14 langkah berikut untuk menjalankan Kosanku dari awal:

### 1. Periksa Instalasi PHP, Composer, dan Node.js
Buka terminal (PowerShell / Command Prompt / Git Bash) dan jalankan:
```bash
php -v
composer -V
node -v
npm -v
```

### 2. Pastikan MySQL Server Aktif
Jika menggunakan Laragon atau XAMPP, klik tombol **Start** pada Apache/MySQL. Pastikan port `3306` menyala dan dapat menerima koneksi.

### 3. Buat Database `kosanku_db` Melalui HeidiSQL
1. Buka aplikasi **HeidiSQL**.
2. Klik tombol **New** di pojok kiri bawah Session manager.
3. Masukkan parameter koneksi:
   - **Network type**: `MariaDB or MySQL (TCP/IP)`
   - **Hostname / IP**: `127.0.0.1` (atau `localhost`)
   - **User**: `root`
   - **Password**: *(sesuai instalasi lokal Anda, biarkan kosong jika default Laragon/XAMPP)*
   - **Port**: `3306`
4. Klik **Open**.
5. Klik kanan pada panel kiri (area daftar database) > pilih **Create new** > **Database**.
6. Beri nama: `kosanku_db`, pilih Collation: `utf8mb4_unicode_ci` > klik **OK**.

### 4. Salin File `.env.example` Menjadi `.env`
Jika belum ada file `.env`, jalankan:
```bash
copy .env.example .env
```
*(Di Linux/macOS gunakan `cp .env.example .env`)*.

### 5. Atur Konfigurasi Database di `.env`
Buka file `.env` di text editor dan pastikan bagian koneksi MySQL sudah benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kosanku_db
DB_USERNAME=root
DB_PASSWORD=
```
> **Catatan**: Jika user `root` MySQL Anda menggunakan password, isi pada `DB_PASSWORD=password_anda`.

### 6. Generate Application Key
```bash
php artisan key:generate
```

### 7. Hubungkan Storage Link
Buat symlink folder storage agar foto yang diunggah dapat diakses publik:
```bash
php artisan storage:link
```

### 8. Jalankan Database Migration & Seeder
Perintah ini akan membuat seluruh 13 tabel dan mengisinya dengan kategori, fasilitas, aturan platform, serta minimal 8 listing kost demo (di Depok, Bogor, Bandung, dan Jakarta Selatan):
```bash
php artisan migrate --seed
```

### 9. Buat Akun Administrator Pertama Secara Aman
Gunakan command Artisan khusus berikut:
```bash
php artisan kosanku:create-admin --email=admin@kosanku.test --name="Super Admin"
```
Anda akan diminta memasukkan password admin secara interaktif (minimal 10 karakter). Password tidak dicatat di terminal history maupun file kode.

*(Atau sediakan password langsung via parameter untuk setup otomatis)*:
```bash
php artisan kosanku:create-admin --email=admin@kosanku.test --name="Super Admin" --password=adminpassword123
```

### 10. Install & Build Asset Frontend
```bash
npm install
npm run build
```
*(Untuk mode pengembangan dengan hot-reload, Anda dapat menjalankan `npm run dev` di terminal terpisah)*.

### 11. Jalankan Web Server Laravel
```bash
php artisan serve
```

### 12. Buka Website di Browser
Buka browser Anda dan akses:
```
http://localhost:8000
```

---

## 🔑 Akun Demo Siap Pakai

Database seeder telah menyiapkan akun demo berikut (password semua akun demo adalah `password`):

| Peran (Role) | Email | Password | Keterangan |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@kosanku.test` | `adminpassword123` *(atau password yang Anda buat di langkah 9)* | Akses `/admin` |
| **Pemilik Kost** | `owner.raka@kosanku.test` | `password` | Pemilik kost di Depok |
| **Pemilik Kost** | `owner.sinta@kosanku.test` | `password` | Pemilik kost di Bandung & Bogor |
| **Pemilik Kost** | `owner.budi@kosanku.test` | `password` | Pemilik kost di Jakarta Selatan |
| **Penyewa** | `penyewa@kosanku.test` | `password` | Akun penyewa (Nadia Putri) |

Anda juga dapat mendaftar akun baru melalui form `/register` sebagai **Penyewa** atau **Pemilik Kost**. Akun baru tidak dapat memilih role Admin melalui form publik.

---

## 🗄️ Mengakses & Mengedit Data via HeidiSQL

Setelah migration dan seed selesai, Anda dapat memeriksa dan mengedit data langsung di HeidiSQL:

1. Buka **HeidiSQL** dan hubungkan ke session database lokal Anda.
2. Di pohon sebelah kiri, klik dua kali pada database **`kosanku_db`**.
3. Anda akan melihat tabel-tabel utama:
   - `users`: Tabel akun pengguna dengan kolom `role` (`admin`, `owner`, `tenant`) dan `is_active`.
   - `properties`: Seluruh data listing kost (harga, lokasi, ketersediaan kamar, status verifikasi).
   - `property_images`: Foto kost (URL eksternal Unsplash demo atau path lokal storage).
   - `categories`: Kategori kost (Dekat Kampus, Eksklusif, Budget, Dekat Kantor).
   - `facilities`: Fasilitas (AC, Wi-Fi, Kamar Mandi Dalam, Kasur, dsb.).
   - `facility_property`: Relasi many-to-many fasilitas dan kost.
   - `favorites`: Daftar kost yang disimpan penyewa.
   - `bookings`: Pengajuan sewa dengan snapshot harga saat booking diajukan.
   - `booking_status_histories`: Riwayat perubahan status booking dan aktor yang mengubahnya.
   - `reviews`: Ulasan dan rating dari penyewa.
   - `reports`: Laporan pengguna terhadap kost yang melanggar.
   - `audit_logs`: Log aksi administratif yang aman (tidak dapat diedit via web UI).
   - `platform_settings`: Pengaturan nama situs, kontak, dan konten teks statis.
4. Klik tab **Data** pada HeidiSQL untuk melihat isi baris dan mengedit secara langsung jika diperlukan.

---

## ⚠️ Mengatasi Masalah (Troubleshooting)

### 1. Error: `SQLSTATE[HY000] [2002] Connection refused`
- **Penyebab**: Service MySQL belum aktif.
- **Solusi**: Nyalakan MySQL di control panel Laragon atau XAMPP. Pastikan port 3306 berstatus running.

### 2. Error: `Access denied for user 'root'@'localhost'`
- **Penyebab**: Password user `root` di MySQL lokal Anda tidak kosong.
- **Solusi**: Perbarui `DB_PASSWORD=isi_password_anda` di file `.env`.

### 3. Error: `Vite manifest not found`
- **Penyebab**: File build CSS/JS belum di-bundle.
- **Solusi**: Jalankan `npm run build` di terminal project.

### 4. Error: Foto yang diunggah tidak muncul di browser
- **Penyebab**: Symlink storage public belum dibuat.
- **Solusi**: Jalankan `php artisan storage:link`.

---

## 🧪 Menjalankan Pengujian (Automated Tests)

Project dilengkapi 38 automated feature tests yang memvalidasi seluruh alur:
```bash
php artisan test
```

Semua pengujian berjalan secara otomatis dan menguji autentikasi, proteksi role, CRUD kost oleh pemilik, alur verifikasi admin, transaksi booking kamar, snapshot harga, pengelolaan kamar, ulasan, laporan, dan halaman publik.
