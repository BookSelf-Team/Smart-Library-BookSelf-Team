# DESIGN SYSTEM & DESIGN SPECIFICATION: BOOKSELF (SMART LIBRARY)

**Nama Proyek:** Bookself — Smart Library Ecosystem  
**Versi:** 2.0 (Comprehensive Specification)  
**Karakter Visual:** *Academic Sanctuary* — Earthy, Calm, Focused, and Modern Digital Library  

---

## 1. Ringkasan Proyek & Visi Desain
**Bookself** adalah sistem manajemen perpustakaan modern berbasis kecerdasan digital dan otomasi RFID/Barcode yang menjembatani operasional internal pustakawan (Desktop Admin Dashboard) dengan portal kemandirian siswa/guru (Mobile & Web Client).

Visi desain berpusat pada konsep **"Academic Sanctuary"**: menghadirkan suasana ruang baca dan preservasi buku klasik yang teduh, hangat, dan menenangkan, dipadukan dengan efisiensi tata kelola digital berkecepatan tinggi.

---

## 2. Fondasi Warna (Color System)

Sistem warna Bookself mengadopsi perpaduan palet natural *Dark Olive Green* dan *Cream*, dirancang secara ergonomis untuk meminimalkan kelelahan mata (*eye fatigue*) selama operasional pencatatan katalog panjang maupun verifikasi sirkulasi harian.

### A. Palet Inti (Core Palette)

| Token Desain | Nama Warna | Hex Code | Role & Panduan Implementasi |
| :--- | :--- | :--- | :--- |
| `primary` | Dark Olive Green | `#556B2F` | Warna identitas utama. Digunakan untuk Sidebar, Tombol Aksi Utama (CTA), ikon aktif, header modal, dan navigasi terpilih. |
| `primary-hover` | Light Olive / Moss | `#6B843B` | State hover dan fokus pada tombol primary dan interaksi navigasi. |
| `primary-dark` | Deep Olive | `#3B4A20` | State active/pressed pada tombol dan elemen aksen gelap. |
| `background` | Cream / Warm Alabaster | `#FFFDD0` / `#FCF9EF` | Latar belakang kanvas aplikasi (*body background*), menghadirkan kehangatan kertas buku klasik. |
| `surface` | Pure White | `#FFFFFF` | Latar kartu koleksi, formulir input, panel modal, data grid, dan popover agar kontras jelas di atas background cream. |
| `surface-container-low` | Soft Cream Sand | `#F6F4EA` | Kontainer sekunder, baris tabel bergantian (*zebra striping*), dan header data grid. |
| `surface-dim` | Muted Beige Sand | `#DDD8C8` | Garis pemisah (*divider*), border kartu, dan outline input. |
| `text-primary` | Olive Black / Deep Espresso | `#1F2915` | Teks judul utama, isi data tabel, label formulir, dan angka metrik KPI. Rasio kontras > 7:1 (WCAG AAA). |
| `text-muted` | Forest Charcoal | `#4A5B29` / `#5E6C47` | Teks sekunder, metadata buku (Dewey Call Number, ISBN), dan keterangan status kecil. |

### B. Warna Semantik & Status Operasional

| Kategori Status | Background (Soft) | Teks / Border (Aksen) | Penggunaan Spesifik |
| :--- | :--- | :--- | :--- |
| **Success / Normal** | `#E8F0DC` (Pale Sage) | `#3E5C1B` (Olive Forest) | Peminjaman aktif normal, buku tersedia di rak, pengembalian sukses, hardware RFID terkoneksi. |
| **Warning / Due Soon** | `#FEF3C7` (Soft Amber) | `#92400E` (Deep Amber) | Buku mendekati jatuh tempo (≤ 24 jam), stok buku tersisa 1 eks. |
| **Danger / Denda Aktif** | `#FEE2E2` (Soft Crimson) | `#991B1B` (Deep Crimson) | Keterlambatan sirkulasi, tagihan denda belum lunas, sensor RFID terputus, stok habis. |
| **Info / RFID Scan** | `#E0F2FE` (Soft Sky) | `#0369A1` (Ocean Slate) | Mode deteksi otomatis kartu anggota, pemindaian buku baru, proses verifikasi barcode. |

---

## 3. Tipografi (Typography Hierarchy)

Perpaduan tipografi serif editorial akademis pada tajuk (*heading*) dan sans-serif geometris modern pada teks fungsional (*body* & *data display*).

### A. Font Families
- **Display & Heading:** `Merriweather`, `Playfair Display`, atau `Newsreader` (Serif elegan, akademis, hangat).
- **Body & Data Grid:** `Inter`, `Plus Jakarta Sans`, atau `Poppins` (Sans-serif berkejelasan tinggi pada ukuran kecil dan angka).
- **Data Kode / Barcode:** `JetBrains Mono` atau `Roboto Mono` (Monospace untuk ISBN, Dewey Decimal Call Number, RFID UID, ID Transaksi).

### B. Skala Tipe (Type Scale)

| Level | Font Family | Size / Line-Height | Weight | Contoh Penggunaan |
| :--- | :--- | :--- | :--- | :--- |
| **Display 1** | Serif | `28px / 36px` | SemiBold (600) | Judul Halaman Utama ("Layanan Sirkulasi Terpadu", "Katalog & Tata Kelola") |
| **Heading 2** | Serif | `20px / 28px` | SemiBold (600) | Judul Kartu Widget ("Antrean Peminjaman Hari Ini", "Form Transaksi Cepat") |
| **Heading 3** | Sans-Serif | `16px / 24px` | SemiBold (600) | Sub-header panel, nama pengguna pada profil, judul buku kartu |
| **Body Regular** | Sans-Serif | `14px / 20px` | Regular (400) | Deskripsi modul, isi paragraf sinopsis, baris data tabel |
| **Body Medium** | Sans-Serif | `14px / 20px` | Medium (500) | Label form input, tombol aksi, item menu sidebar |
| **Caption / Meta** | Sans-Serif / Mono | `12px / 16px` | Regular & Medium | Call Number Dewey (e.g. `515.35 PUR k`), tanggal pinjam, NISN, status badge |
| **Mono Numeric** | Monospace | `13px / 18px` | Medium (500) | Kode Transaksi (`#TRX-94810`), Tarif Denda (`Rp 4.000`), Serial COM Port |

---

## 4. Spacing, Elevation, & Grid Layout

### A. Sistem Jarak (Spacing System)
Menggunakan basis kelipatan **4px / 8px**:
- `4px` (xs): Padding internal badge status, gap icon kecil.
- `8px` (sm): Jarak antar elemen field input, padding tombol compact.
- `12px` (md): Jarak antar baris form input, gap button group.
- `16px` (lg): Padding standar kartu katalog, cell padding data table.
- `20px` - `24px` (xl): Padding kontainer modul utama, gap antar kolom grid.
- `32px` (2xl): Margin antar seksi fungsional pada dashboard.

### B. Elevation & Radius
- **Border Radius:**
  - Tombol, input text, badge: `8px` (`rounded-lg`)
  - Kartu modul, kontainer panel: `12px` hingga `16px` (`rounded-xl` / `rounded-2xl`)
  - Floating modal, Quick Scan drawer: `16px` (`rounded-2xl`)
  - Badge pill & avatar: `9999px` (`rounded-full`)
- **Bayangan (Elevation / Shadow):**
  - Card Default: `0 4px 12px -2px rgba(85, 107, 47, 0.08), 0 2px 4px -1px rgba(85, 107, 47, 0.04)`
  - Hover Card: `0 10px 20px -3px rgba(85, 107, 47, 0.12), 0 4px 6px -2px rgba(85, 107, 47, 0.06)`
  - Modal Popover: `0 20px 25px -5px rgba(31, 41, 21, 0.2), 0 8px 10px -6px rgba(31, 41, 21, 0.1)`

---

## 5. Komponen Antarmuka (UI Components Guideline)

### A. Tombol (Buttons)
1. **Primary Button** (e.g. *Proses Peminjaman*, *Bayar Tunai/QRIS*, *Quick Scan*):
   - Background: `#556B2F`
   - Text: `#FFFFFF` (Font Medium 14px)
   - Hover: `#6B843B`, Transform subtle `scale(0.99)`
   - Radius: `8px`
2. **Secondary / Outlined Button** (e.g. *Batal*, *Perpanjang +7 Hari*, *Simulasi Tag*):
   - Background: `#FFFFFF` atau `#F6F4EA`
   - Border: `1px solid #DDD8C8` atau `1px solid #556B2F`
   - Text: `#1F2915` atau `#556B2F`
   - Hover: `#E8F0DC`
3. **Danger Button** (e.g. *Selesaikan Sanksi*, *Blokir Anggota*):
   - Background: `#991B1B` atau Soft `#FEE2E2`
   - Text: `#FFFFFF` atau `#991B1B`

### B. Input Field & Filter Kontrol
- **Text & Barcode Input:**
  - Background: `#FFFFFF`
  - Border: `1.5px solid #DDD8C8`, Focus: `1.5px solid #556B2F` dengan ring `3px rgba(85, 107, 47, 0.15)`.
  - Placeholder: `#8E9A7B`
- **Global Search Bar (`⌘K`):**
  - Menampilkan badge shortcut keyboard `⌘K` / `Ctrl+K`.
  - Dilengkapi ikon pencarian olive dan modal pencarian lintas kategori (Buku, Anggota, Transaksi).

### C. Kartu Koleksi Buku & Anggota
- Menampilkan *Cover Thumbnail*, Judul Buku (Serif), Nama Penulis, Nomor Klasifikasi DDC (e.g., `813.01 PRAM`), dan label ketersediaan eksemplar fisik (*Tersedia: 4 eks*).
- State hover memunculkan opsi aksi kilat: *Pinjam Langsung* atau *Detail Sinopsis*.

### D. Tabel Sirkulasi & Live Queue
- Header tabel menggunakan background `#F6F4EA` dengan teks `#4A5B29` berbobot medium.
- Kolom mencakup: ID Transaksi, Anggota / NISN, Judul Koleksi & DDC, Tanggal Pinjam, Jatuh Tempo (dengan indikator hitung mundur sisa hari), Status, dan Tombol Aksi Cepat (*+7 Hari*, *Kembalikan*).

### E. Integrasi Hardware RFID & Kios Mandiri
- Indikator status koneksi hardware:
  - *Dot hijau berkedip halus*: Terhubung ke `COM3 (9600 bps)` — Siap memindai RFID ISO 15693 / 14443A.
  - Kartu simulasi tag untuk mempermudah uji coba pustakawan secara langsung.

---

## 6. Arsitektur Informasi & Struktur Modul

Berdasarkan diagram arsitektur sistem (`S1-02.png`, `S1-03.png`, `S1-04.png`), struktur aplikasi terbagi atas:

```text
Bookself Smart Library
│
├── Desktop Admin Dashboard (Panel Pustakawan)
│   ├── Top Header Bar
│   │   ├── Pencarian Terpadu (⌘K)
│   │   ├── Terminal Quick Scan (RFID & Barcode)
│   │   ├── Notifikasi Jatuh Tempo & Sistem
│   │   └── Profil Pustakawan & Sesi Operasional
│   │
│   ├── Persistent Left Sidebar
│   │   ├── Dashboard Ringkasan (KPI Inventaris, Sirkulasi Aktif, Denda, Anggota)
│   │   ├── Sirkulasi & Peminjaman (Form Kilat, Live Queue, Kas Denda)
│   │   ├── Katalog Buku (DDC Klasifikasi, Stok Rak, Entri Koleksi)
│   │   ├── Rekap Laporan & Denda (Audit Kas Harian/Bulanan, Ekspor PDF/Excel)
│   │   ├── Data Anggota & Pengguna (Direktori Siswa/Guru, Kuota Pinjam 3 Buku)
│   │   └── Pengaturan (Masa Pinjam, Tarif Denda Rp 1.000/hari, Port Hardware)
│   │
│   └── Footer Bar
│       └── Status Sinkronisasi Hardware RFID (Gate-01 Online)
│
└── Mobile & Web Client Tier (Portal Siswa & Guru)
    ├── Katalog Buku & Rekomendasi
    ├── Detail Buku & Ketersediaan Stok Real-time
    ├── Form Pengajuan Reservasi / Peminjaman
    ├── E-Ticket & QR Code Peminjaman (Untuk verifikasi di meja pustakawan)
    └── Riwayat Status Buku Aktif & Notifikasi Jatuh Tempo
```

---

## 7. Skema Basis Data Relasional (Database Schema Mapping)

Dokumentasi ERD dari diagram arsitektur sistem (`S1-03.png`):

```sql
-- 1. Tabel Kategori / Klasifikasi DDC
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- 2. Tabel Koleksi Buku
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    barcode VARCHAR(64) UNIQUE NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    is_recommended BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (kategori_id) REFERENCES categories(id)
);

-- 3. Tabel Pengguna (Siswa, Guru, Admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_induk VARCHAR(32) UNIQUE NOT NULL, -- NISN / NIP
    nama VARCHAR(150) NOT NULL,
    role ENUM('siswa', 'guru', 'admin') NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- 4. Tabel Transaksi Peminjaman (Borrowings)
CREATE TABLE borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    book_id INT NOT NULL,
    tgl_pinjam DATE NOT NULL,
    tgl_tenggat DATE NOT NULL,
    tgl_kembali DATE NULL,
    qr_code VARCHAR(255) NULL,
    status ENUM('dipinjam', 'dikembalikan', 'terlambat') NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (book_id) REFERENCES books(id)
);

-- 5. Tabel Tagihan Denda Keterlambatan (Fines)
CREATE TABLE fines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    borrowing_id INT NOT NULL,
    total_denda INT NOT NULL DEFAULT 0, -- Rp 1.000 / hari keterlambatan
    status_bayar ENUM('belum_lunas', 'lunas') NOT NULL DEFAULT 'belum_lunas',
    FOREIGN KEY (borrowing_id) REFERENCES borrowings(id)
);
```

---

## 8. Tailwind CSS Configuration Implementation

Salin ekstensi konfigurasi ini ke dalam berkas `tailwind.config.js` untuk menjaga konsistensi kode frontend:

```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: "#556B2F", // Dark Olive Green
          hover: "#6B843B",   // Light Olive
          dark: "#3B4A20",    // Deep Olive
          light: "#E8F0DC",   // Pale Sage
        },
        background: {
          DEFAULT: "#FFFDD0", // Classic Cream
          canvas: "#FCF9EF",  // Soft Alabaster Canvas
        },
        surface: {
          DEFAULT: "#FFFFFF",
          low: "#F6F4EA",
          dim: "#DDD8C8",
        },
        text: {
          DEFAULT: "#1F2915", // Olive Black
          muted: "#4A5B29",   // Muted Olive Charcoal
          light: "#7A8C5B",
        },
        semantic: {
          success: "#3E5C1B",
          warning: "#92400E",
          danger: "#991B1B",
          info: "#0369A1",
        }
      },
      fontFamily: {
        heading: ["Merriweather", "Playfair Display", "serif"],
        body: ["Inter", "Plus Jakarta Sans", "sans-serif"],
        mono: ["JetBrains Mono", "Roboto Mono", "monospace"],
      },
      borderRadius: {
        button: "8px",
        card: "12px",
        modal: "16px",
      },
      boxShadow: {
        card: "0 4px 12px -2px rgba(85, 107, 47, 0.08), 0 2px 4px -1px rgba(85, 107, 47, 0.04)",
        cardHover: "0 10px 20px -3px rgba(85, 107, 47, 0.12), 0 4px 6px -2px rgba(85, 107, 47, 0.06)",
        modal: "0 20px 25px -5px rgba(31, 41, 21, 0.2), 0 8px 10px -6px rgba(31, 41, 21, 0.1)",
      },
    },
  },
  plugins: [],
};
```

---

## 9. Aturan Aksesibilitas & Responsivitas (A11y & Responsiveness)
1. **Kontras Rasio Teks:** Setiap teks pada background Cream (`#FFFDD0` / `#FCF9EF`) atau Surface Putih wajib menggunakan `#1F2915` atau `#556B2F`, memastikan rasio kontras melebihi ambang batas WCAG AAA (minimal 7:1 untuk teks normal).
2. **State Interaktif:** Seluruh tombol dan baris tabel memiliki efek *focus-visible* dengan outline olive 2px yang jelas saat dinavigasi menggunakan keyboard (`Tab` & `Enter`).
3. **Perilaku Responsif:**
   - **Desktop (≥ 1024px):** Layout multi-kolom dengan sidebar persisten di kiri dan kanvas dashboard operasional di kanan.
   - **Mobile / Tablet (< 1024px):** Sidebar beralih menjadi drawer tersembunyi (*hamburger menu*) atau bottom navigation bar untuk portal siswa/guru.