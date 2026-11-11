# UI/UX DESIGN SPECIFICATION: ADMIN DASHBOARD (BOOKSELF)

**Modul:** Panel Pustakawan (Desktop Admin Dashboard)
**Karakter Visual:** *Academic Sanctuary* (Earthy, Calm, Focused)

---

## 1. Arsitektur Layout Utama

Tampilan admin dirancang untuk penggunaan *desktop* dengan tata letak multi-kolom yang mengedepankan efisiensi operasional pustakawan.

* **Top Header Bar:**
    * Bilah Pencarian Terpadu (Shortcut: `⌘K` atau `Ctrl+K`).
    * Tombol Terminal Quick Scan (RFID & Barcode).
    * Ikon Notifikasi (Jatuh tempo & peringatan sistem).
    * Profil Pustakawan & Sesi Operasional.
* **Persistent Left Sidebar:**
    * Navigasi utama yang selalu tampil: Dashboard Ringkasan, Sirkulasi & Peminjaman, Katalog Buku, Rekap Laporan & Denda, Data Anggota & Pengguna, Pengaturan.
* **Main Content Area (Kanvas):** Area dinamis untuk memuat widget KPI, tabel data, dan formulir input.
* **Footer Bar:** Menampilkan indikator status sinkronisasi hardware (misal: *Gate-01 Online / RFID COM3 9600 bps*).

---

## 2. Palet Warna Admin (Color Tokens)

Warna dirancang agar tidak membuat mata cepat lelah (*eye fatigue*) saat pustakawan memantau data dalam waktu lama.

* **Background (Kanvas):** `#FCF9EF` (Cream / Warm Alabaster) memberikan kesan kertas buku klasik.
* **Surface (Kartu & Panel):** `#FFFFFF` (Pure White) agar data terlihat kontras.
* **Primary Accent:** `#556B2F` (Dark Olive Green) untuk Sidebar, Tombol Aksi Utama, dan ikon aktif.
* **Text Utama:** `#1F2915` (Olive Black) untuk keterbacaan tinggi pada tabel dan label.
* **Indikator Status:**
    * *Normal/Tersedia:* `#3E5C1B` (Hijau Olive) pada latar `#E8F0DC`.
    * *Peringatan/Jatuh Tempo H-1:* `#92400E` (Amber) pada latar `#FEF3C7`.
    * *Denda/Terlambat:* `#991B1B` (Merah Kirmizi) pada latar `#FEE2E2`.

---

## 3. Komponen Antarmuka (UI Components)

### A. Tabel Sirkulasi & Antrean (Live Queue)

* **Header Tabel:** Latar `#F6F4EA` dengan warna teks `#4A5B29` (Medium).
* **Isi Kolom:** Menampilkan ID Transaksi, Nama/NISN, Judul Buku & DDC, Tanggal Pinjam, Hitung Mundur Jatuh Tempo, dan Status.
* **Aksi Cepat:** Tombol sekunder (*outline*) untuk perpanjangan (+7 Hari) atau penyelesaian sanksi denda.

### B. Formulir & Input Data

* **Field Teks/Barcode:** Latar `#FFFFFF` dengan garis tepi `#DDD8C8` tebal 1.5px. Saat fokus, berubah menjadi `#556B2F`.
* **Tipografi Kode:** Menggunakan font Monospace (`JetBrains Mono` atau `Roboto Mono`) khusus untuk input Nomor DDC (misal: `515.35 PUR k`), ISBN, dan UID RFID.

### C. Tombol Aksi (Buttons)

* **Primary (Simpan/Proses):** Latar `#556B2F`, Teks Putih `#FFFFFF`, dengan radius `8px`.
* **Danger (Hapus/Blokir):** Latar Merah `#991B1B` atau Soft Crimson `#FEE2E2` untuk tindakan destruktif.

---

## 4. Tipografi

* **Display & Heading (Judul Panel/Modul):** Font Serif (`Newsreader` / `Merriweather`) ukuran 20px - 28px dengan ketebalan SemiBold (600).
* **Body & Data (Isi Tabel/Deskripsi):** Font Sans-Serif (`Plus Jakarta Sans` / `Inter`) ukuran 14px dengan ketebalan Regular (400) hingga Medium (500).
