# Dokumentasi API — Bookself Smart Library

Dokumentasi ini menjelaskan API publik yang tersedia pada aplikasi **Bookself — Smart Library**.

| Item | Keterangan |
|---|---|
| Format respons | JSON (`application/json; charset=UTF-8`) |
| Base URL (lokal) | `http://localhost:8000` |
| Base URL API | `http://localhost:8000/api` |
| Autentikasi | Tidak ada (endpoint publik) |
| Rate limiting | Belum diterapkan |
| Versi API | Belum menggunakan prefix versi (mis. `/api/v1`) |

Nilai `Base URL` mengikuti konfigurasi `APP_URL` pada file `.env`. Pada lingkungan produksi, ganti dengan domain aplikasi Anda.

---

## Daftar Endpoint

| Method | Path | Deskripsi | Autentikasi |
|---|---|---|---|
| `GET` | `/api/books` | Mengambil daftar katalog buku yang stoknya tersedia | Tidak perlu |

---

## 1. Ambil Katalog Buku

Mengembalikan seluruh buku yang memiliki stok fisik lebih dari `0`.

```http
GET /api/books
```

### Request

- **Method:** `GET`
- **Headers:** `Accept: application/json` (opsional, respons sudah berupa JSON)
- **Query parameter:** tidak ada
- **Body:** tidak ada

Tidak ada parameter pencarian, filter, pengurutan, maupun paginasi. Seluruh data yang memenuhi kriteria dikembalikan sekaligus tanpa jaminan urutan tertentu.

### Contoh Request

```bash
curl -i --request GET "http://localhost:8000/api/books" \
  --header "Accept: application/json"
```

### Response Berhasil — `200 OK`

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

Jika tidak ada buku dengan stok tersedia, `data` berisi array kosong:

```json
{
  "success": true,
  "message": "Katalog Buku Smart Library berhasil diambil",
  "data": []
}
```

### Struktur Respons

| Field | Tipe | Deskripsi |
|---|---|---|
| `success` | `boolean` | Status keberhasilan permintaan. Selalu `true` pada respons `200`. |
| `message` | `string` | Pesan deskriptif hasil permintaan. |
| `data` | `array` | Daftar objek buku. |

### Properti Objek Buku

| Field | Tipe | Nullable | Deskripsi |
|---|---|---|---|
| `id` | `integer` | Tidak | ID unik buku. |
| `kode_buku` | `string` (maks. 15) | Tidak | Kode koleksi unik, contoh `BKS-0001`. |
| `judul` | `string` (maks. 150) | Tidak | Judul buku. |
| `pengarang` | `string` (maks. 100) | Tidak | Nama pengarang. |
| `tahun_terbit` | `integer` | Tidak | Tahun terbit buku. |
| `jumlah_stok` | `integer` | Tidak | Jumlah stok tersedia. Endpoint hanya mengembalikan nilai `> 0`. |
| `sinopsis` | `string` | Ya | Ringkasan isi buku. |
| `cover_image` | `string` (maks. 255) | Ya | Path relatif pada disk `public` atau URL absolut gambar sampul. Path relatif memerlukan prefix `storage/` agar dapat diakses, contoh `http://localhost:8000/storage/{cover_image}`. |
| `created_at` | `string` (ISO 8601) | Ya | Waktu data dibuat. |
| `updated_at` | `string` (ISO 8601) | Ya | Waktu data terakhir diubah. |

---

## Format Respons Error

Seluruh error pada rute `api/*` dikembalikan dalam format JSON.

### `404 Not Found`

```json
{
  "message": "The route api/unknown could not be found.",
  "status": 404
}
```

### `405 Method Not Allowed`

Terjadi bila method yang digunakan tidak didukung, misalnya `POST /api/books`.

```json
{
  "message": "The POST method is not supported for route api/books. Supported methods: GET, HEAD.",
  "status": 405
}
```

### `500 Internal Server Error`

Terjadi bila server mengalami kegagalan, termasuk kegagalan koneksi atau query database.

```json
{
  "message": "Terjadi kesalahan database. Silakan coba lagi nanti.",
  "error": null
}
```

Field `error` hanya berisi detail teknis ketika `APP_DEBUG=true` pada `.env`. Jangan aktifkan `APP_DEBUG` di lingkungan produksi.

---

## Contoh Integrasi

### JavaScript (`fetch`)

```js
const response = await fetch('http://localhost:8000/api/books', {
  headers: { Accept: 'application/json' },
});

if (!response.ok) {
  throw new Error(`Permintaan gagal dengan status ${response.status}`);
}

const { success, message, data } = await response.json();

if (success) {
  console.log(message);
  data.forEach((book) => {
    const cover = book.cover_image
      ? `http://localhost:8000/storage/${book.cover_image}`
      : null;

    console.log(book.kode_buku, book.judul, cover);
  });
}
```

### PHP (Laravel HTTP Client)

```php
use Illuminate\Support\Facades\Http;

$response = Http::acceptJson()->get('http://localhost:8000/api/books');

if ($response->successful()) {
    $books = $response->json('data');
}
```

---

## Catatan dan Batasan

- Endpoint bersifat **publik** dan tidak memerlukan token, sesi, maupun header autentikasi.
- Hanya buku dengan `jumlah_stok > 0` yang dikembalikan; buku dengan stok habis tidak ditampilkan.
- Belum tersedia paginasi, pencarian, filter kategori, maupun pengurutan. Gunakan data di sisi klien bila memerlukan pengurutan.
- Respons mengembalikan seluruh kolom model `Book` apa adanya, tanpa API Resource, sehingga perubahan skema tabel `books` dapat memengaruhi kontrak API.
- `cover_image` dikembalikan sebagai nilai mentah. Gunakan `asset('storage/'.$cover_image)` (PHP) atau prefix `/storage/` (klien) untuk membentuk URL gambar.
- Rute menggunakan middleware grup `api` bawaan Laravel tanpa middleware autentikasi.
- Nama field, path, dan format respons mengikuti implementasi pada `app/Http/Controllers/Api/BookApiController.php` dan `routes/api.php`.

---

## Status Implementasi

Dokumentasi ini mencerminkan kondisi kode saat ini:

| Aspek | Status |
|---|---|
| Katalog buku | Tersedia |
| Autentikasi API (Sanctum) | Belum diterapkan, meskipun package tersedia |
| Paginasi dan filter | Belum tersedia |
| Dokumentasi interaktif (Swagger/OpenAPI) | Belum tersedia |
