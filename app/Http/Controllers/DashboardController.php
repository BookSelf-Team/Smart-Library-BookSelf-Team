<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

    public function show(Book $book): View
    {
        return view('siswa.detail-buku', compact('book'));
    }

    public function adminDashboard()
    {
        $this->authorize('is-staff');

        // KPI ringkasan operasional.
        $stats = [
            'total_buku' => (int) Book::sum('jumlah_stok'),
            'sedang_dipinjam' => Loan::query()->active()->count(),
            'lewat_tempo' => Loan::query()->overdue()->count(),
            'denda_belum_lunas' => (int) Fine::query()
                ->whereIn('status_pembayaran', ['BELUM', 'UNPAID'])
                ->sum('nominal'),
        ];

        // Rekap agregat: jumlah peminjaman dikelompokkan per status.
        $rekapPinjaman = Loan::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // Rekap agregat: total nominal denda dikelompokkan per status pembayaran.
        $rekapDenda = $this->rekapDendaPerStatus();

        $loans = Loan::query()
            ->with(['user', 'book'])
            ->orderByDesc('tanggal_pinjam')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('loans', 'stats', 'rekapPinjaman', 'rekapDenda'));
    }

    /**
     * Rekap denda: COUNT dan SUM(nominal) dikelompokkan per status pembayaran.
     *
     * @return Collection<string, array{jumlah: int, total: int}>
     */
    private function rekapDendaPerStatus(): Collection
    {
        return Fine::query()
            ->selectRaw('status_pembayaran, COUNT(*) as jumlah, SUM(nominal) as total')
            ->groupBy('status_pembayaran')
            ->get()
            ->mapWithKeys(fn (Fine $fine): array => [
                $fine->status_pembayaran => [
                    'jumlah' => (int) $fine->jumlah,
                    'total' => (int) $fine->total,
                ],
            ]);
    }

    // --- Menampilkan halaman Tambah Buku ---
    public function tambahBuku()
    {
        $this->authorize('create', Book::class);

        return view('admin.tambah-buku');
    }
}
