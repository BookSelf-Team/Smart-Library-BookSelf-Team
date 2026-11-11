<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;

class BookApiController extends Controller
{
    /**
     * S2-03: Menampilkan daftar buku perpustakaan dalam format JSON
     */
    public function index()
    {
        // Mengambil buku yang stok fisiknya masih tersedia (> 0)
        $books = Book::where('jumlah_stok', '>', 0)->get();

        // Mengembalikan respons dengan format API Restful yang terstandarisasi
        return response()->json([
            'success' => true,
            'message' => 'Katalog Buku Smart Library berhasil diambil',
            'data' => $books,
        ], 200);
    }
}
