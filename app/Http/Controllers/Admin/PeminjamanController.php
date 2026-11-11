<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    private const LAMA_PINJAM_HARI = 7;

    private const KUOTA_PINJAM = 3;

    /**
     * Halaman Peminjaman — daftar semua peminjaman + form proses pinjam.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Loan::class);

        $loans = Loan::with(['user', 'book'])
            ->orderByDesc('tanggal_pinjam')
            ->orderByDesc('id')
            ->paginate(15);

        $members = User::query()
            ->whereIn('role', Role::memberValues())
            ->orderBy('name')
            ->get();

        $books = Book::where('jumlah_stok', '>', 0)->orderBy('judul')->get();

        return view('admin.peminjaman.index', compact('loans', 'members', 'books'));
    }

    /**
     * Proses peminjaman buku oleh petugas untuk seorang anggota.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Loan::class);

        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'book_id' => ['required', 'integer', 'exists:books,id'],
        ]);

        return DB::transaction(function () use ($validated) {
            // Lock baris user agar kuota dan pinjaman ganda tidak bisa lolos
            // saat ada dua permintaan bersamaan untuk anggota yang sama.
            $user = User::query()->lockForUpdate()->findOrFail($validated['user_id']);

            if (! $user->isMember()) {
                return back()->with('error', 'Peminjaman hanya bisa diproses untuk akun anggota (siswa, guru, atau pengguna).')->withInput();
            }

            $aktif = Loan::query()
                ->where('user_id', $user->getKey())
                ->active()
                ->count();

            if ($aktif >= self::KUOTA_PINJAM) {
                return back()->with('error', "Kuota pinjam {$user->name} sudah penuh ({$aktif}/".self::KUOTA_PINJAM.').')->withInput();
            }

            $terlambat = Loan::query()
                ->where('user_id', $user->getKey())
                ->overdue()
                ->count();

            if ($terlambat > 0) {
                return back()->with('error', "{$user->name} masih memiliki {$terlambat} pinjaman terlambat. Proses pengembaliannya terlebih dahulu.")->withInput();
            }

            $book = Book::query()->lockForUpdate()->findOrFail($validated['book_id']);

            if ($book->jumlah_stok < 1) {
                return back()->with('error', 'Stok buku "'.$book->judul.'" sedang habis.')->withInput();
            }

            $sudahDipinjam = Loan::query()
                ->where('user_id', $user->getKey())
                ->where('book_id', $book->getKey())
                ->active()
                ->exists();

            if ($sudahDipinjam) {
                return back()->with('error', $user->name.' masih meminjam "'.$book->judul.'". Buku yang sama belum dikembalikan.')->withInput();
            }

            $tanggalPinjam = Carbon::today();
            $batasKembali = $tanggalPinjam->copy()->addDays(self::LAMA_PINJAM_HARI);

            Loan::create([
                'user_id' => $user->getKey(),
                'book_id' => $book->getKey(),
                'tanggal_pinjam' => $tanggalPinjam,
                'batas_kembali' => $batasKembali,
                'status' => Loan::STATUS_ACTIVE,
            ]);

            $book->decrement('jumlah_stok');

            return back()->with('success', 'Peminjaman "'.$book->judul.'" atas nama '.$user->name.' berhasil dicatat. Jatuh tempo '.$batasKembali->translatedFormat('d M Y').'.');
        });
    }
}
