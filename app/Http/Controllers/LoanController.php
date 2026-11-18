<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private const KUOTA_PINJAM = 3;

    /**
     * Halaman peminjaman umum — dialihkan ke halaman sirkulasi sesuai role.
     */
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->isMember()) {
            return redirect()->route('siswa.pinjaman');
        }

        if ($user->isStaff()) {
            return redirect()->route('admin.peminjaman');
        }

        abort(403, 'Akses Ditolak.');
    }

    /**
     * S2-02 & S2-06: Alur Bisnis Utama Peminjaman Buku (End-to-End)
     *
     * Anggota hanya mengajukan peminjaman (status PENDING). Pinjaman
     * baru menjadi aktif setelah disetujui petugas di sirkulasi.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Loan::class);

        $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        $user = $request->user();
        $book = Book::findOrFail($request->integer('book_id'));

        $terisi = Loan::query()
            ->where('user_id', $user->getKey())
            ->whereNull('tanggal_dikembalikan')
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_PENDING])
            ->count();

        if ($terisi >= self::KUOTA_PINJAM) {
            return back()->with('error', 'Kuota pinjam Anda sudah penuh ('.self::KUOTA_PINJAM.' buku, termasuk pengajuan yang menunggu persetujuan).');
        }

        $sudahAda = Loan::query()
            ->where('user_id', $user->getKey())
            ->where('book_id', $book->getKey())
            ->whereNull('tanggal_dikembalikan')
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_PENDING])
            ->exists();

        if ($sudahAda) {
            return back()->with('error', 'Anda sudah memiliki pinjaman atau pengajuan untuk buku "'.$book->judul.'".');
        }

        if ($book->jumlah_stok < 1) {
            return back()->with('error', 'Maaf, stok buku ini sedang habis!');
        }

        Loan::create([
            'user_id' => $user->getKey(),
            'book_id' => $book->getKey(),
            'tanggal_pinjam' => Carbon::today(),
            'batas_kembali' => Carbon::today()->addDays(7),
            'status' => Loan::STATUS_PENDING,
        ]);

        return redirect()->route('loans.index')->with('success', 'Pengajuan pinjaman "'.$book->judul.'" terkirim. Menunggu persetujuan petugas perpustakaan.');
    }
}
