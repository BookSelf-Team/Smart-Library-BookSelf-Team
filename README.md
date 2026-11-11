# Bookself — Smart Library

Bookself adalah sistem informasi perpustakaan berbasis web untuk mengelola katalog buku, anggota/pemustaka, peminjaman, pengembalian, denda keterlambatan, dan administrasi perpustakaan dalam satu aplikasi.

Antarmuka tersedia dalam Bahasa Indonesia dengan dashboard terpisah untuk anggota, petugas sirkulasi, pustakawan, dan administrator.

## Fitur Utama

### Autentikasi dan Keamanan

- Login menggunakan **identifier** (NISN/NIP/NUPTK/username) atau **email**.
- Mendukung opsi *remember me* dan logout yang menginvalidasi sesi.
- Rate limiting pada proses login: maksimal 5 percobaan gagal per identifier + alamat IP.
- Pembatasan akses berbasis role menggunakan middleware `role` dan policy Laravel (`BookPolicy`, `LoanPolicy`, `FinePolicy`, `UserPolicy`).
- Password disimpan dalam bentuk hash dan perubahan password dari profil wajib memverifikasi password lama.
- Upload gambar avatar dan sampul buku divalidasi serta disanitasi: pengecekan MIME asli, ekstensi aman, batas ukuran 2MB, nama file acak, dan penyimpanan pada disk `public`.
- Halaman error khusus untuk `403`, `404`, `500`, dan `503`.
- Responsive layout untuk desktop dan mobile, termasuk sidebar mobile.

### Katalog Buku

- Halaman `/katalog` dapat diakses semua pengguna yang sudah login.
- Pencarian judul dan pengarang.
- Filter ketersediaan: semua buku, tersedia, dan sedang dipinjam.
- Paginasi hasil katalog.
- Status stok pada kartu buku: tersedia, stok menipis, atau habis.
- Anggota dapat meminjam langsung dari katalog selama stok tersedia.
- Admin dan pustakawan dapat:
  - Menambah buku baru dengan kode koleksi otomatis berformat `BKS-####`.
  - Mengedit judul, pengarang, tahun terbit, stok, sinopsis, dan sampul.
  - Menghapus buku yang tidak sedang dipinjam.
  - Mencari koleksi berdasarkan judul, pengarang, atau kode buku.
- Petugas dapat melihat data buku tetapi tidak dapat mengubahnya.

### Portal Anggota / Pemustaka

Berlaku untuk role `siswa`, `guru`, dan `user` (role lama yang tetap diperlakukan sebagai anggota).

- **Dashboard anggota**
  - Ringkasan pinjaman aktif.
  - Jatuh tempo terdekat.
  - Estimasi denda berjalan.
  - Statistik buku yang sudah dikembalikan.
  - Pengingat otomatis untuk pinjaman hampir jatuh tempo atau terlambat.
  - Rekomendasi koleksi.

- **Pinjaman saya**
  - Daftar buku yang sedang dipinjam.
  - Status aman, hampir jatuh tempo, jatuh tempo hari ini, dan terlambat.
  - Perpanjangan jatuh tempo sebesar **+7 hari** selama pinjaman belum dikembalikan.
  - Tombol cetak bukti peminjaman.
  - Kuota pinjam maksimal **3 buku aktif**.

- **Riwayat dan denda**
  - Arsip seluruh transaksi peminjaman.
  - Filter: semua, sedang dipinjam, denda belum dibayar, dan denda lunas.
  - Statistik total transaksi, selesai, aktif, terlambat, dan total denda.
  - Cetak bukti peminjaman dan kuitansi denda.

- **Kartu anggota digital**
  - Menampilkan nama, identifier, kelas, nomor anggota, masa berlaku, dan kuota pinjaman.
  - Statistik penggunaan kartu.
  - Tampilan siap cetak melalui browser.

- **Profil**
  - Ubah nama tampilan dan email.
  - Upload foto profil.
  - Ganti kata sandi dengan verifikasi password lama.

### Meja Sirkulasi Petugas

- Dashboard khusus petugas dengan ringkasan:
  - Total buku.
  - Jumlah pinjaman aktif.
  - Total anggota.
  - Jumlah pinjaman terlambat.
- Pencarian anggota berdasarkan nama, identifier, atau email.
- Kartu anggota digital untuk anggota yang dipilih.
- Daftar buku yang belum dikembalikan beserta estimasi denda.
- Riwayat 10 pengembalian terakhir dan status denda.
- Proses pengembalian buku dengan satu klik.

### Peminjaman dan Pengembalian oleh Staf

- Admin, pustakawan, dan petugas dapat memproses peminjaman untuk anggota.
- Lama peminjaman standar **7 hari**.
- Sistem menolak peminjaman baru jika:
  - Kuota anggota sudah penuh.
  - Anggota masih memiliki pinjaman terlambat.
  - Stok buku habis.
  - Anggota yang sama masih meminjam judul buku yang sama.
- Pengembalian buku mencatat tanggal kembali, mengembalikan stok, dan mengubah status pinjaman menjadi selesai.
- Denda keterlambatan dihitung otomatis sebagai `jumlah hari terlambat × tarif per hari`.
- Transaksi peminjaman dan pengembalian memakai database transaction serta row locking untuk mengurangi risiko double-submit dan race condition.

### Manajemen Admin

- Kelola pengguna:
  - Tambah, edit, dan hapus akun.
  - Assign role: `admin`, `pustakawan`, `petugas`, `guru`, atau `siswa`.
  - Identifier dibuat otomatis bila kosong, misalnya `SIS-000001`, `GRU-000001`, `ADM-000001`.
  - Proteksi akun sendiri dan admin terakhir agar tidak terhapus/terdemote.
- Pengaturan sistem:
  - Atur tarif denda per hari dalam Rupiah.
  - Nilai `0` menonaktifkan perhitungan denda.

### API Buku

Endpoint JSON untuk katalog buku:

```http
GET /api/books
```

Response berisi daftar buku yang masih memiliki stok:

```json
{
  "success": true,
  "message": "Katalog Buku Smart Library berhasil diambil",
  "data": [
    {
      "id": 1,
      "kode_buku": "BKS-0001",
      "judul": "Bumi Manusia (Tetralogi Buru)",
      "pengarang": "Pramoedya Ananta Toer",
      "tahun_terbit": 1980,
      "jumlah_stok": 6,
      "sinopsis": null,
      "cover_image": null,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

## Role dan Hak Akses

| Role | Label | Dashboard | Akses utama |
|---|---|---|---|
| `admin` | Administrator | `/admin/dashboard` | Semua fitur, termasuk pengguna dan pengaturan |
| `pustakawan` | Pustakawan Ahli | `/admin/dashboard` | Sirkulasi, buku, dan melihat koleksi |
| `petugas` | Petugas Sirkulasi | `/petugas/dashboard` dan `/admin/petugas` | Meja sirkulasi, peminjaman, pengembalian, lihat buku |
| `guru` | Dewan Guru | `/siswa/dashboard` | Portal anggota: katalog, pinjaman, riwayat, kartu, profil |
| `siswa` | Siswa / Pemustaka | `/siswa/dashboard` | Portal anggota: katalog, pinjaman, riwayat, kartu, profil |
| `user` | Pengguna | `/siswa/dashboard` | Role lama yang tetap diperlakukan sebagai anggota |

## Teknologi

- **Backend:** Laravel 13, PHP 8.3+
- **Frontend:** Blade templates, Tailwind CSS 4, Vite
- **Database:** Laravel database layer; mendukung SQLite, MySQL/MariaDB, PostgreSQL, dan SQL Server melalui konfigurasi `DB_*`
- **Testing:** PHPUnit 12
- **Tooling:** Composer, npm, Laravel Pint, Laravel Boost (development)
- **Package terkait:** Laravel Sanctum tersedia untuk token API bila diperlukan

## Struktur Penting

```text
app/
├── Enums/Role.php                 # Definisi role dan hak akses
├── Http/Controllers/              # Web, Admin, Siswa, Petugas, API, Auth
├── Http/Middleware/               # RoleCheck dan AuthorizeMiddleware
├── Models/                        # User, Book, Loan, Fine, Setting, Reservation
├── Policies/                      # Otorisasi model
└── Services/FileUploadSanitizer.php

resources/views/
├── auth/                          # Halaman login
├── siswa/                         # Portal anggota
├── admin/                         # Dashboard dan manajemen admin/pustakawan
├── petugas/                       # Meja sirkulasi
├── invoices/                      # Bukti pinjam dan kuitansi denda
└── layouts/                       # Layout siswa dan admin

database/migrations/               # Skema users, books, loans, fines, settings, dll.
routes/
├── web.php                        # Route utama aplikasi
└── api.php                        # Endpoint API buku
```

## Instalasi

### Prasyarat

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- Database yang didukung Laravel (SQLite untuk development paling mudah)

### Langkah Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur minimal variabel berikut di `.env`. Untuk SQLite, hapus atau komentari `DB_HOST`, `DB_PORT`, `DB_USERNAME`, dan `DB_PASSWORD`, lalu isi `DB_DATABASE` dengan path absolut ke file database proyek ini:

```env
APP_NAME="Bookself"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/smart_library/database/database.sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local
```

Untuk SQLite, buat file database terlebih dahulu:

```bash
touch database/database.sqlite
```

Untuk MySQL/MariaDB/PostgreSQL, ganti `DB_CONNECTION` dan isi `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, serta `DB_PASSWORD` sesuai environment Anda.

Jalankan migrasi dan seed data:

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Untuk development frontend dengan hot reload:

```bash
npm run dev
```

Aplikasi dapat diakses melalui URL yang tertera di `APP_URL`, biasanya `http://localhost:8000`.

### Deployment Produksi

Untuk environment produksi, pastikan minimal:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.example
```

Lalu jalankan:

```bash
php artisan migrate --force
php artisan storage:link
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jalankan queue worker jika aplikasi mulai menggunakan queue/job.

### Akun Seed Development

Seeder membuat akun berikut. **Semua memakai password `password123` dan hanya untuk development lokal.**

| Nama | Role | Identifier |
|---|---|---|
| Administrator | `admin` | `ADMIN001` |
| Pustakawan Contoh | `pustakawan` | `198503122009121003` |
| Petugas Sirkulasi | `petugas` | `P-001` |
| Guru Contoh | `guru` | `9283719482910293` |
| Siswa Contoh | `siswa` | `0072938491` |

## Alur Penggunaan

### Anggota

1. Login menggunakan identifier atau email.
2. Masuk ke dashboard anggota.
3. Buka **Katalog Buku**, cari buku, lalu gunakan tombol **Pinjamkan Kilat**.
4. Pantau pinjaman aktif di **Pinjaman Saya**.
5. Perpanjang pinjaman sebelum jatuh tempo bila perlu.
6. Kembalikan buku ke petugas/pustakawan.
7. Cek **Riwayat & Denda** untuk status transaksi dan kuitansi.
8. Gunakan **Kartu Anggota** dan **Profil Saya** untuk data diri.

### Petugas

1. Login sebagai `petugas`.
2. Buka **Dashboard Petugas**.
3. Cari anggota berdasarkan nama, identifier, atau email.
4. Periksa kartu anggota, buku yang belum kembali, dan estimasi denda.
5. Proses pengembalian dari meja sirkulasi atau halaman pengembalian.

### Admin / Pustakawan

1. Login sebagai `admin` atau `pustakawan`.
2. Kelola koleksi melalui **Data Buku** dan **Tambah Buku**.
3. Proses peminjaman dan pengembalian melalui halaman sirkulasi.
4. Admin dapat mengatur tarif denda di **Pengaturan Sistem** dan mengelola akun di **Kelola Pengguna**.

## Pengujian

Jalankan seluruh test:

```bash
php artisan test --compact
```

Atau melalui Composer:

```bash
composer test
```

Test suite mencakup autentikasi, RBAC, manajemen buku, sirkulasi, pengembalian, denda, invoice, pengaturan, rate limiting, responsive layout, serta security/performance checks.

Untuk formatting kode PHP:

```bash
vendor/bin/pint --dirty --format agent
```

## Catatan Implementasi dan Batasan Saat Ini

Agar dokumentasi sesuai kondisi kode saat ini:

- Tabel `reservations` sudah tersedia, tetapi workflow reservasi buku belum diimplementasikan.
- Status pembayaran denda mendukung nilai `BELUM`, `UNPAID`, `LUNAS`, dan `PAID`, tetapi belum ada endpoint/UI untuk menandai denda lunas. Status dapat ditangani manual melalui data bila diperlukan.
- Tombol **Tap Kartu RFID**, **Akun Belajar.id/SSO**, dan indikator scanner RFID pada UI masih bersifat placeholder; integrasi hardware/SSO belum tersedia.
- Beberapa kartu metrik pada dashboard admin dan rekomendasi pada dashboard anggota masih memakai data contoh/statik dan perlu dihubungkan ke data operasional sebelum produksi.
- Field `kelas` dan beberapa tanggal keanggotaan pada tampilan anggota masih menggunakan nilai default/placeholder.
- Endpoint `GET /api/books` saat ini bersifat publik dan tidak memiliki pagination; tambahkan autentikasi dan rate limiting jika API akan dipakai secara luas.
- Peminjaman mandiri anggota memeriksa kuota dan stok, tetapi aturan blokir pinjaman terlambat serta pencegahan meminjam judul yang sama saat ini baru ditegakkan pada alur peminjaman oleh staf.
- Backend belum membatasi jumlah atau periode perpanjangan pinjaman; saat ini pinjaman aktif dapat diperpanjang +7 hari selama belum dikembalikan.
- Jangan commit `.env`. File `.env.example` idealnya hanya berisi placeholder, bukan kredensial database nyata.

## Lisensi

Proyek ini adalah aplikasi internal/edukasi yang dibangun dengan Laravel. Detail lisensi dapat disesuaikan dengan kebijakan repository Anda.
