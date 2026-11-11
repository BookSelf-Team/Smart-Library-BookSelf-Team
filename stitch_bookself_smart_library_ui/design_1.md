# DESIGN SPECIFICATION: PANEL ADMIN, PETUGAS & SISWA (SMART LIBRARY / BOOKSELF)

**Modul:** Panel Perpustakaan (Desktop) untuk **Admin**, **Petugas (Pustakawan)**, dan **Siswa**
**Karakter Visual:** *Academic Sanctuary*: hangat, tenang, fokus, bernuansa kertas buku dan hijau zaitun
**Stack tampilan:** Laravel Blade + Tailwind CSS (CDN) + Material Symbols Outlined
**Versi:** 2.0 (menggantikan `design-v1.md`)

---

## 1. Prinsip Desain

1. **Tenang untuk dipakai lama.** Latar krem hangat dan kontras lembut agar mata pustakawan tidak cepat lelah.
2. **Satu alur, satu layar.** Transaksi utama (pinjam, kembali, bayar denda) dapat dilakukan tanpa berpindah halaman.
3. **Keyboard dan scanner dulu.** Semua aksi cepat punya pintasan, dan kolom scan tidak mengirim form secara tidak sengaja.
4. **Status tidak hanya warna.** Setiap badge selalu memuat teks dan titik penanda, bukan warna saja.
5. **Konsisten antar halaman.** Satu layout, satu sidebar, satu set komponen. Hanya isi menu yang berbeda menurut peran.

---

## 2. Peran Pengguna dan Menu (Admin, Petugas, Siswa)

> Aplikasi saat ini baru mengenal peran `pustakawan` (petugas) dan `siswa`. Peran **Admin** adalah usulan, ditambahkan sebagai nilai baru `role = 'admin'`. Layout dan komponen ketiganya sama (satu `layouts/app.blade.php`); yang berbeda hanya **isi menu dan hak aksi**.

### 2.1 Ringkasan peran

| Peran | Fungsi | Menu |
|---|---|---|
| **Admin** | Mengelola sistem, pengguna, dan pengaturan | Semua menu staf (6 menu) |
| **Petugas** (pustakawan) | **Melayani** transaksi di meja sirkulasi: memproses pinjam, kembali, denda, dan mengelola data | 5 menu staf, tanpa Pengaturan |
| **Siswa** | **Melihat** pinjaman, denda, dan riwayat milik sendiri, serta mencari buku | 6 menu siswa |

Siswa meminjam buku, tetapi yang mencatat dan memproses transaksinya di sistem adalah petugas lewat menu Sirkulasi. Siswa tidak memiliki menu Sirkulasi, Rekap Laporan, atau Data Anggota.

### 2.2 Menu per peran

**Admin** (menu staf lengkap, 6 item):

1. Dashboard Ringkasan
2. Sirkulasi & Peminjaman
3. Katalog Buku
4. Rekap Laporan & Denda
5. Data Anggota & Pengguna
6. Pengaturan

**Petugas** (5 item):

1. Dashboard Ringkasan
2. Sirkulasi & Peminjaman
3. Katalog Buku
4. Rekap Laporan & Denda
5. Data Anggota

**Siswa** (6 item):

1. Dashboard Saya
2. Katalog Buku
3. Pinjaman Saya
4. Riwayat & Denda
5. Kartu Anggota
6. Profil Saya

### 2.3 Matriks hak akses

| Fitur / Menu | Admin | Petugas | Siswa |
|---|:---:|:---:|:---:|
| Dashboard Ringkasan (staf) | Penuh | Penuh | Tidak ada |
| Dashboard Saya | Tidak ada | Tidak ada | Penuh (milik sendiri) |
| Sirkulasi & Peminjaman | Penuh | Penuh | Tidak ada |
| Katalog Buku: lihat dan cari | Penuh | Penuh | Penuh |
| Katalog Buku: tambah dan ubah | Penuh | Penuh | Tidak ada |
| Katalog Buku: hapus | Penuh | Tidak ada | Tidak ada |
| Rekap Laporan & Denda | Penuh + ekspor | Lihat, terima pembayaran denda | Tidak ada |
| Data Anggota & Pengguna | Penuh (termasuk akun staf) | Lihat dan ubah data anggota, cetak kartu | Tidak ada |
| Pinjaman Saya, Riwayat & Denda | Tidak ada | Tidak ada | Penuh (milik sendiri) |
| Kartu Anggota, Profil Saya | Tidak ada | Tidak ada | Penuh (milik sendiri) |
| Pengaturan Sistem, audit log | Penuh | Tidak tampil | Tidak tampil |

### 2.4 Aturan tampilan dan keamanan

- Menu yang tidak boleh diakses **disembunyikan**, bukan dinonaktifkan.
- Server tetap memvalidasi hak akses (403) walaupun menu tersembunyi. Menyembunyikan menu saja tidak cukup.
- Siswa hanya dapat membaca data **miliknya sendiri** (filter berdasarkan `user_id` di server).
- Label peran di profil: `Administrator`, `Pustakawan Ahli`, atau `Siswa / Pemustaka`.
- Halaman `/katalog` berada di grup `auth` biasa karena dibuka oleh ketiga peran. Yang berbeda hanya tombol aksi (tambah, ubah, hapus) yang tampil sesuai hak akses.
- Pada layout, `$navItems` dipilih menurut `role`; bentuk dan posisi sidebar tetap sama.

---

## 3. Arsitektur Layout

Tampilan desktop dengan sidebar tetap di kiri dan topbar tetap di atas. Semua halaman memakai **satu layout** (`layouts/app.blade.php`).

| Elemen | Ukuran / Posisi |
|---|---|
| Sidebar | `fixed`, lebar **288px** (`w-72`), tinggi penuh, `z-50` |
| Topbar | `fixed`, tinggi **64px** (`h-16`), mulai dari kanan sidebar, `z-40`, latar `surface/85` + blur |
| Area konten | `padding-left: 288px`, `padding-top: 64px`, gutter horizontal `32px` (`px-8`) |

### 3.1 Sidebar (urutan tetap, posisi sama di semua halaman)

1. **Logo:** ikon buku dalam kotak hijau 36px + teks `Bookself` (serif) dan `SMART LIBRARY`.
2. **Kartu status:** titik berdenyut + `Koleksi Aktif` + jumlah eksemplar (mis. `14.820 Eks`).
3. **Menu utama** (6 item, jarak antar item `4px`):
   - Dashboard Ringkasan
   - Sirkulasi & Peminjaman
   - Katalog Buku
   - Rekap Laporan & Denda
   - Data Anggota & Pengguna
   - Pengaturan *(hanya Admin)*
4. **Kartu info (bawah):** status RFID Reader ("Port USB-3 terhubung • Siap").
5. **Tombol Keluar.**

**Status menu:**

| Status | Gaya |
|---|---|
| Aktif | Latar `primary` (#3E5219), teks `on-primary` (putih), ikon terisi (`FILL 1`), bayangan lembut, `aria-current="page"` |
| Normal | Teks `on-surface-variant`, tanpa latar |
| Hover | Latar `surface-container-high`, teks `on-surface` |
| Fokus keyboard | Cincin `primary/60` 2px (`focus-visible`) |

Status aktif **mengikuti URL** yang sedang dibuka, bukan angka tetap. Semua item adalah link asli (`<a href>`), bukan tombol `onclick`.

### 3.2 Topbar

- **Pencarian terpadu** (maks. lebar 512px) dengan hint `⌘K` / `Ctrl+K`. Placeholder: "Cari judul, ISBN, nomor panggil, atau ID anggota...".
- **Quick Scan** (RFID dan barcode) membuka form transaksi sirkulasi.
- **Notifikasi** dengan titik merah bila ada peringatan (jatuh tempo, sistem).
- **Chip profil:** avatar bulat, nama, dan peran.

### 3.3 Footer / indikator hardware

Status sinkronisasi ditampilkan di kartu bawah sidebar dan di header halaman Sirkulasi (mis. `Sensor RFID Gate-1: Siap • COM3 (9600 bps)`).

---

## 4. Token Warna

Warna dirancang tidak membuat mata cepat lelah. Nama token mengikuti konfigurasi Tailwind proyek.

### 4.1 Netral dan permukaan

| Token | Hex | Pemakaian |
|---|---|---|
| `background` / `surface` | `#FCF9EF` | Kanvas halaman (krem hangat, kesan kertas) |
| `surface-container-lowest` | `#FFFFFF` | Kartu data, input saat fokus, modal |
| `surface-container-low` | `#F6F4EA` | Sidebar, panel besar |
| `surface-container` | `#F1EEE4` | Chip, kolom pencarian, header tabel |
| `surface-container-high` | `#EBE8DE` | Hover, tombol sekunder |
| `surface-container-highest` | `#E5E2D9` | Tombol tonal, kartu info bawah sidebar |
| `on-surface` | `#1C1C16` | Teks utama |
| `on-surface-variant` | `#45483C` | Teks sekunder, label |
| `outline` / `outline-variant` | `#75796B` / `#C5C8B8` | Garis tepi, pemisah |

### 4.2 Aksen dan status

| Token | Hex | Pemakaian |
|---|---|---|
| `primary` | `#3E5219` | Menu aktif, tombol aksi utama, kartu KPI gelap |
| `primary-container` | `#556B2F` | Aksen hijau zaitun (ikon, progress, hover tombol) |
| `secondary-container` | `#DBE8C0` | Badge normal/aktif, ikon tile |
| `tertiary` | `#425223` | Ikon pendukung |
| `error` / `error-container` | `#BA1A1A` / `#FFDAD6` | Denda, terlambat, tombol destruktif |
| Peringatan (amber) | `#92400E` pada `#FEF3C7` | Jatuh tempo H-1, stok menipis |

### 4.3 Matriks status (badge)

| Status | Teks | Latar | Penanda |
|---|---|---|---|
| Normal / Tersedia | `#3E5C1B` | `#E8F0DC` | titik hijau |
| Peringatan / H-1 | `#92400E` | `#FEF3C7` | titik amber |
| Denda / Terlambat | `#991B1B` | `#FEE2E2` | titik merah |
| Baru / Netral | `on-surface` | `surface-container-highest` | titik abu |

---

## 5. Tipografi

| Peran | Font | Ukuran / Tinggi baris | Bobot |
|---|---|---|---|
| Display / judul besar | Newsreader (serif) | 56 / 64 | 500 |
| Judul halaman (`headline-lg`) | Newsreader | 36 / 44 | 600 |
| Judul panel (`headline-md`) | Newsreader | 28 / 36 | 600 |
| Judul kartu (`headline-sm`) | Newsreader | 22 / 30 | 600 |
| Judul bagian (`title-lg` / `title-md`) | Plus Jakarta Sans | 20 / 28 · 16 / 24 | 600 |
| Judul kecil / menu (`title-sm`) | Plus Jakarta Sans | 14 / 20 | 600 |
| Isi (`body-md`) | Plus Jakarta Sans | 14 / 22 | 400 |
| Keterangan (`body-sm`) | Plus Jakarta Sans | 12 / 18 | 400 |
| Label (`label-lg` / `label-md` / `label-sm`) | Plus Jakarta Sans | 14 · 12 · 11 | 500 · 500 · 600 |
| Kode (DDC, ISBN, UID RFID, ID transaksi) | `JetBrains Mono` / `Roboto Mono` | 11 – 15 | 400 – 500 |

Aturan: **serif untuk judul dan angka KPI besar, sans-serif untuk data dan formulir**, monospace khusus untuk kode agar karakter mudah dibedakan.

---

## 6. Spasi, Radius, Bayangan

- **Skala spasi:** `4 / 8 / 16 / 24 / 40 / 64 px` (`xs / sm / md / lg / xl / 2xl`). Gutter grid `24px`, margin halaman `32 – 40px`.
- **Radius:** kontrol `8px` (`rounded-lg`), kartu `12px` (`rounded-xl`), panel besar dan modal `16px` (`rounded-2xl`), badge dan chip `9999px`.
- **Bayangan:** sangat lembut, `0 1px 8px rgba(0,0,0,0.04)` untuk bilah, `0 2px 8px -2px rgba(35,45,19,0.12)` untuk tombol dan kartu. Kedalaman utama dibuat dengan **perbedaan warna permukaan**, bukan garis tepi tebal.
- **Ikon:** Material Symbols Outlined, 18 – 22px; ikon menu aktif memakai varian terisi.

---

## 7. Komponen Antarmuka

### 7.1 Tombol

| Jenis | Gaya |
|---|---|
| **Primary** (Simpan, Proses Pinjam) | Latar `primary`, teks putih, radius 8px, tinggi 40 – 44px; hover ke `primary-container` |
| **Tonal / sekunder** (Pengembalian, Cetak Barcode) | Latar `surface-container-highest`, teks `on-surface` |
| **Outline** (+7 Hari, Perpanjang) | Garis tepi `outline-variant`, latar transparan |
| **Danger** (Hapus, Blokir, Keluar saat hover) | Latar `error` atau `error-container`, teks `on-error-container` |
| **Ikon** | 32 – 40px, bulat atau persegi tumpul, hover `surface-container` |

Semua tombol memiliki status `hover`, `focus-visible` (cincin), `active` (skala 0.98), dan `disabled` (opasitas 60%).

### 7.2 Formulir dan input

- **Field teks / barcode:** latar `surface`, tanpa garis tepi tebal; saat fokus berubah menjadi `surface-container-lowest` dengan bayangan lembut (opsi garis tepi `#556B2F` 1.5px).
- **Error:** cincin `error` 1px + pesan di bawah atau banner di atas form.
- **Label** di atas input, dengan hint kecil di kanan (mis. "Tekan F2 untuk kartu", "Auto-detect").
- **Kolom kode** (nomor anggota, barcode, ISBN) memakai font monospace.
- **Scan field:** tombol `Enter` hanya mencari data dan tidak mengirim form; pengiriman lewat tombol aksi.

### 7.3 Kartu statistik (KPI)

- Kartu `surface-container-low`, radius 12px, padding 20px.
- Isi: label huruf kapital kecil, angka serif besar (28 – 36px), keterangan, dan **ikon tile 40px** di kanan atas.
- Satu kartu penekanan memakai latar `primary` dengan teks putih (mis. Kas Denda atau Buku Dibaca).
- Angka bermakna negatif (denda, terlambat) berwarna `error`.

### 7.4 Tabel (Antrean Sirkulasi / Laporan)

- Header: latar `surface-container`, teks `on-surface-variant` huruf kapital 11px, jarak huruf lebar.
- Kolom antrean: ID Transaksi dan Anggota, Judul Buku dan No. Panggil (DDC), Tgl Pinjam dan Batas, Status, Tindakan.
- Baris: `hover:bg-surface-container`, pemisah tipis atau jarak antar baris, tanpa garis tebal.
- **Aksi cepat** di baris: `+7 Hari` (outline) dan `Kembalikan` (primary).
- Kolom tanggal jatuh tempo berwarna `error` bila terlambat, disertai jumlah hari.
- Paginasi di bawah: kotak 32px, halaman aktif berlatar `primary`; teks "Menampilkan X dari Y transaksi".
- Tabel lebar dibungkus `overflow-x-auto`.

### 7.5 Chip dan tab filter

Berbentuk pil. Aktif: latar `primary`, teks putih, disertai jumlah, mis. `Jatuh Tempo (4)`. Tidak aktif: `surface-container`.

### 7.6 Kartu buku (Katalog)

- Sampul rasio **4:5**, sudut tumpul, zoom halus saat hover.
- Lencana nomor panggil DDC di kiri atas (monospace), badge status di kanan atas.
- Info: ISBN, kode rak, judul serif (maks. 1 baris), pengarang.
- Kotak ketersediaan fisik (`X dari Y Eks.`) berwarna sesuai status.
- Aksi: `Pinjamkan Kilat` (primary) atau `Reservasi Antrean` (tonal bila stok 0), dan tombol info.

### 7.7 Modal dan notifikasi

- **Modal** memakai elemen `<dialog>`, radius 16px, latar putih, backdrop `black/40`, tombol tutup di kanan atas, tutup juga saat klik di luar.
- **Banner hasil:** sukses (`secondary-container`, ikon centang) dan error (`error-container`, ikon peringatan, daftar pesan). Memakai `role="status"` / `role="alert"`.
- **Keadaan kosong:** ikon besar abu, judul singkat, dan saran tindakan.

### 7.8 Progress dan indikator

Bar tinggi 6 – 8px, trek `surface-container-high`, isi `primary`/`primary-container`. Warna isi berubah bila kapasitas tinggi (>= 75% hijau, 50 – 74% hijau tua, < 50% hijau muda).

---

## 8. Spesifikasi Halaman

### 8.1 Dashboard Ringkasan
1. Sapaan "Selamat Datang, *Admin Perpustakaan*" (nama dengan gaya miring serif hijau), badge shift operasional, dan tanggal.
2. Tombol: `Transaksi Baru` (F1), `Scan Cepat Barcode` (F2), `+ Tambah Judul Buku`.
3. **4 KPI:** Inventaris Koleksi, Sirkulasi Aktif, Perhatian Khusus (peminjam terlambat), Kas Denda Sirkulasi (kartu gelap).
4. **Pusat Modul Utama:** 4 kartu pintasan (Sirkulasi, Katalog, Rekap Denda, Data Anggota) lengkap dengan pintasan keyboard.
5. **Meja Transaksi Cepat** (kiri) dan **Aktivitas Sirkulasi Terkini** (kanan).
6. **Koleksi Terpopuler & Peringatan Stok Rendah** (4 kartu buku kecil dengan bar stok).

### 8.2 Sirkulasi & Peminjaman
- Header: "Stasiun Sirkulasi Terpadu" + status sensor RFID + tombol *Simulasi Tag*.
- **Kolom kiri:** Form Transaksi Cepat (anggota, preview anggota, buku, preview buku, tombol *Proses Pinjam* dan *Pengembalian*) dan Kasir Denda Berjalan (modal pelunasan Tunai / QRIS).
- **Kolom kanan:** Antrean Peminjaman Aktif dengan tab Semua / Jatuh Tempo / Pinjam Baru.
- **Aturan validasi server:** anggota aktif, tanpa denda tertunggak, kuota belum penuh, stok buku tersedia. Penyimpanan peminjaman dan pengurangan stok dalam **satu transaksi database** (cegah double-booking).

### 8.3 Katalog Koleksi & Preservasi
- Header, 4 KPI (Total Judul, Kapasitas Rak, Buku Dipinjam, Stok Kritis), toolbar pencarian dan filter status, tab kategori DDC.
- Grid kartu buku (3 kolom) di kiri, panel kanan: Audit Kilat Rak, Distribusi Rak Utama, Protokol Preservasi.
- Paginasi dengan elipsis dan hitungan eksemplar.

### 8.4 Rekap Laporan & Denda *(direncanakan)*
Tabel laporan harian / bulanan, filter rentang tanggal, ringkasan denda, tombol ekspor (Admin).

### 8.5 Data Anggota & Pengguna *(direncanakan)*
Daftar anggota dengan pencarian, filter peran dan kelas, cetak kartu RFID, dan (Admin) pengelolaan akun staf.

### 8.6 Pengaturan *(direncanakan, hanya Admin)*
Masa pinjam, tarif denda per hari, kuota pinjam, jam layanan, perangkat RFID, dan audit log.

---

## 9. Interaksi dan Pintasan Keyboard

| Pintasan | Aksi |
|---|---|
| `Ctrl+K` / `⌘K` | Fokus ke pencarian terpadu |
| `F1` | Transaksi baru (buka Sirkulasi) |
| `F2` | Fokus ke kolom kartu anggota / scan |
| `Enter` di kolom scan | Cari data (tidak mengirim form) |
| `Esc` | Tutup modal |
| `Tab` | Navigasi antar kontrol dengan cincin fokus yang jelas |

---

## 10. Status Antarmuka

| Keadaan | Perlakuan |
|---|---|
| Memuat | Skeleton abu-abu pada kartu dan baris tabel, tombol memperlihatkan teks "Memproses..." dan dinonaktifkan |
| Kosong | Ikon besar + kalimat penjelas + tindakan berikutnya |
| Error validasi | Banner merah di atas form + cincin merah pada kolom bermasalah |
| Sukses | Banner hijau yang menjelaskan apa yang tercatat (siapa, buku apa, tempo berapa hari) |
| Hak akses ditolak | Halaman 403 dengan penjelasan "Menu ini khusus pustakawan / admin" |

---

## 11. Aksesibilitas dan Responsivitas

- Rasio kontras teks minimal **4.5:1**. Menu aktif memakai putih di atas `#3E5219`.
- Target sentuh dan klik minimal **40px**, status tidak hanya bergantung pada warna.
- Seluruh navigasi dapat dijangkau keyboard; ada `aria-current`, `aria-label` pada tombol ikon, `role` pada banner.
- **Desktop dulu:** optimal pada lebar >= 1280px. Di bawah 1024px, sidebar diciutkan menjadi ikon atau drawer, dan grid 4 kolom menjadi 2 kolom lalu 1 kolom.

---

## 12. Catatan Implementasi (Laravel / Blade)

- **Satu layout** `resources/views/layouts/app.blade.php` untuk semua halaman; menu dan peran ditentukan dari `auth()->user()->role`.
- Token warna, tipografi, spasi, dan radius didefinisikan di `tailwind.config` (CDN) sehingga kelas seperti `bg-primary`, `text-on-surface-variant`, `font-headline-md` bisa langsung dipakai.
- Font: Newsreader dan Plus Jakarta Sans (Google Fonts), ikon Material Symbols Outlined.
- Logo memakai **ikon lokal**, bukan gambar eksternal, agar tidak rusak saat URL tidak dapat dimuat.
- Komponen berulang (kartu buku, baris tabel, badge) dipisah menjadi partial atau komponen Blade agar konsisten.
- Validasi dilakukan di sisi klien (kemudahan) **dan** di server (keamanan); server adalah sumber kebenaran.
