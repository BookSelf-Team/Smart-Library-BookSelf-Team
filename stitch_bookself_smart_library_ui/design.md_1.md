# Design System Documentation: Bookself

**Project Name:** Bookself (Smart Library System)
**Version:** 1.0.0
**Core Aesthetic:** Earthy, Calm, Academic, and Modern

---

## 1. Color Palette

Desain antarmuka Bookself menggunakan perpaduan warna natural dan hangat. Pemilihan warna ini dirancang untuk mengurangi kelelahan mata saat membaca katalog dan memberikan kesan perpustakaan klasik namun dikemas secara digital.

| Role             | Color Name       | Hex Code  | Usage Notes                                                                                                                                  |
| :--------------- | :--------------- | :-------- | :------------------------------------------------------------------------------------------------------------------------------------------- |
| **Primary**      | Dark Olive Green | `#556B2F` | Digunakan untuk _Navbar_, _Primary Buttons_, _Icons_, dan teks _heading_ utama. Memberikan kontras tinggi dan kesan elegan.                  |
| **Background**   | Cream            | `#FFFDD0` | Digunakan sebagai warna latar belakang utama halaman web (_body background_) dan area _canvas_ agar ramah di mata.                           |
| **Surface/Card** | Pure White       | `#FFFFFF` | Latar belakang untuk komponen melayang (kartu katalog buku, panel _form_ peminjaman, modal _QR Code_) agar menonjol dari _background_ Cream. |
| **Text (Dark)**  | Olive Black      | `#1F2915` | Digunakan untuk teks paragraf, deskripsi buku, dan label _form_ (turunan sangat gelap dari warna Primary).                                   |
| **Accent/Hover** | Light Olive      | `#6B843B` | Digunakan untuk efek _hover_ pada tombol Primary atau menu navigasi yang sedang aktif.                                                       |

---

## 2. Typography

Untuk mendukung tema warna _earthy_, tipografi harus memiliki tingkat keterbacaan yang tinggi, baik di portal _mobile_ siswa maupun _dashboard desktop_ admin.

- **Heading Font:** `Merriweather` atau `Playfair Display` (Memberikan kesan buku/akademis pada judul buku dan _header_ halaman).
- **Body Font:** `Inter` atau `Poppins` (Bersih, modern, dan sangat mudah dibaca untuk ukuran teks kecil seperti detail stok atau navigasi UI).

---

## 3. UI Component Guidelines

### A. Buttons (Tombol)

- **Primary Button (Ajukan Peminjaman, Login):**
    - Background: Dark Olive Green (`#556B2F`)
    - Text: White
    - Hover: Light Olive (`#6B843B`)
    - Border Radius: `8px` (Rounded-lg)
- **Secondary Button (Batal, Kembali):**
    - Background: Transparent atau Cream (`#FFFDD0`)
    - Text: Dark Olive Green (`#556B2F`)
    - Border: `1px solid #556B2F`

### B. Cards (Kartu Katalog Buku)

- **Background:** White (`#FFFFFF`) untuk membedakan dengan latar Cream (`#FFFDD0`).
- **Shadow:** Lembut dan menyebar (`box-shadow: 0 4px 6px -1px rgba(85, 107, 47, 0.1)`).
- **Border:** Tidak ada border keras, gunakan _subtle border_ jika diperlukan.

### C. Navigation Bar & Sidebar

- **Portal Mobile Siswa:** _Bottom Navigation_ menggunakan latar White dengan ikon yang menyala menggunakan Dark Olive Green (`#556B2F`) saat aktif.
- **Dashboard Admin:** _Sidebar_ menggunakan _background_ Dark Olive Green (`#556B2F`) penuh dengan teks/menu berwarna Cream (`#FFFDD0`) atau Putih.

---

## 4. Tailwind CSS Configuration Implementation

Untuk mempercepat integrasi _frontend_, berikut adalah ekstensi konfigurasi warna yang dapat langsung diterapkan ke dalam file `tailwind.config.js` Anda:

```javascript
/** @type {import('tailwindcss').Config} */
module.exports = {
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
                    hover: "#6B843B",
                },
                background: {
                    DEFAULT: "#FFFDD0", // Cream
                },
                surface: "#FFFFFF",
                text: {
                    DEFAULT: "#1F2915",
                    light: "#4A5B29",
                },
            },
            fontFamily: {
                heading: ["Merriweather", "serif"],
                body: ["Inter", "sans-serif"],
            },
            boxShadow: {
                card: "0 4px 6px -1px rgba(85, 107, 47, 0.1)",
            },
        },
    },
    plugins: [],
};
```
