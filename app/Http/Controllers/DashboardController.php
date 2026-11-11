<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = 6;

        // Menghubungkan langsung ke model Book (database)
        $query = Book::query();

        // --- Filter: kata kunci pencarian ---
        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->whereLike('judul', "%{$q}%")
                    ->orWhereLike('pengarang', "%{$q}%");
            });
        }

        // --- Filter: status ketersediaan ---
        $status = (string) $request->query('status', 'all');
        if ($status === 'ready') {
            $query->where('jumlah_stok', '>', 0);
        } elseif ($status === 'loan') {
            $query->where('jumlah_stok', '=', 0);
        }

        // Ambil data dengan paginasi
        $books = $query->paginate($perPage)->withQueryString();

        return view('siswa.katalog', [
            'books' => $books,
        ]);
    }

    public function adminDashboard()
    {
        $this->authorize('is-staff');

        $loans = Loan::query()
            ->with(['user', 'book'])
            ->orderByDesc('tanggal_pinjam')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('loans'));
    }

    // --- Menampilkan halaman Tambah Buku ---
    public function tambahBuku()
    {
        $this->authorize('create', Book::class);

        return view('admin.tambah-buku');
    }
}
