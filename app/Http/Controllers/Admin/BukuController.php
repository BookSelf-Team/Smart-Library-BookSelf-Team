<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Services\FileUploadSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuController extends Controller
{
    /**
     * Data Buku — daftar seluruh koleksi dengan pencarian.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Book::class);

        $q = trim((string) $request->query('q', ''));

        $books = Book::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->whereLike('judul', "%{$q}%")
                        ->orWhereLike('pengarang', "%{$q}%")
                        ->orWhereLike('kode_buku', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.buku.index', compact('books', 'q'));
    }

    /**
     * Simpan buku baru dari form Tambah Buku.
     */
    public function store(Request $request, FileUploadSanitizer $sanitizer): RedirectResponse
    {
        $this->authorize('create', Book::class);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'pengarang' => ['required', 'string', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'between:1000,2100'],
            'jumlah_stok' => ['required', 'integer', 'min:0'],
            'sinopsis' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $sanitizer->sanitizeAndStoreImage($request->file('cover_image'), 'covers');
        }

        $validated['kode_buku'] = $this->generateKodeBuku();

        Book::create($validated);

        return redirect()->route('admin.buku.index')->with('success', 'Buku "'.$validated['judul'].'" berhasil ditambahkan.');
    }

    /**
     * Perbarui data buku.
     */
    public function update(Request $request, Book $book, FileUploadSanitizer $sanitizer): RedirectResponse
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:150'],
            'pengarang' => ['required', 'string', 'max:100'],
            'tahun_terbit' => ['required', 'integer', 'between:1000,2100'],
            'jumlah_stok' => ['required', 'integer', 'min:0'],
            'sinopsis' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $sanitizer->sanitizeAndStoreImage($request->file('cover_image'), 'covers');
        }

        $book->update($validated);

        return redirect()->route('admin.buku.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    /**
     * Hapus buku dari katalog.
     */
    public function destroy(Book $book): RedirectResponse
    {
        $this->authorize('delete', $book);

        if ($book->loans()->whereNull('tanggal_dikembalikan')->exists()) {
            return redirect()->route('admin.buku.index')->with('error', 'Buku "'.$book->judul.'" masih sedang dipinjam dan tidak bisa dihapus.');
        }

        $book->delete();

        return redirect()->route('admin.buku.index')->with('success', 'Buku "'.$book->judul.'" berhasil dihapus.');
    }

    /**
     * Kode koleksi unik, contoh: BKS-0001.
     */
    private function generateKodeBuku(): string
    {
        $next = (Book::max('id') ?? 0) + 1;

        do {
            $kode = 'BKS-'.str_pad((string) $next, 4, '0', STR_PAD_LEFT);
            $next++;
        } while (Book::where('kode_buku', $kode)->exists());

        return $kode;
    }
}
