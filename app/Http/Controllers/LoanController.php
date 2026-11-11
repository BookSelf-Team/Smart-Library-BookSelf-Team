<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Loan::class);

        $request->validate([
            'book_id' => ['required', 'exists:books,id'],
        ]);

        return DB::transaction(function () use ($request) {
            $aktif = Loan::query()
                ->where('user_id', $request->user()->getKey())
                ->whereNull('tanggal_dikembalikan')
                ->count();

            if ($aktif >= self::KUOTA_PINJAM) {
                return back()->with('error', 'Kuota pinjam Anda sudah penuh ('.self::KUOTA_PINJAM.' buku).');
            }

            $book = Book::lockForUpdate()->findOrFail($request->integer('book_id'));

            if ($book->jumlah_stok < 1) {
                return back()->with('error', 'Maaf, stok buku ini sedang habis!');
            }

            $loan = Loan::create([
                'user_id' => $request->user()->getKey(),
                'book_id' => $book->id,
                'tanggal_pinjam' => Carbon::now(),
                'batas_kembali' => Carbon::now()->addDays(7),
                'status' => 'ACTIVE',
            ]);

            $book->decrement('jumlah_stok');

            return redirect()->route('loans.index')->with('success', 'Peminjaman buku berhasil diajukan!');
        });
    }
}
