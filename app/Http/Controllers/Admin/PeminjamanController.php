<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
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
            ->orderByRaw('CASE WHEN status = ? THEN 0 ELSE 1 END', [Loan::STATUS_PENDING])
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
            // Lock baris user dan buku agar kuota dan stok tidak bisa
            // dilanggar saat ada dua permintaan bersamaan.
            $user = User::query()->lockForUpdate()->findOrFail($validated['user_id']);
            $book = Book::query()->lockForUpdate()->findOrFail($validated['book_id']);

            if ($error = $this->errorKelayakan($user, $book)) {
                return back()->with('error', $error)->withInput();
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

    /**
     * Setujui pengajuan pinjaman anggota: aktifkan pinjaman,
     * hitung ulang tanggal pinjam/jatuh tempo, dan potong stok.
     */
    public function setujui(Loan $loan): RedirectResponse
    {
        $this->authorize('approve', $loan);

        return DB::transaction(function () use ($loan) {
            $loan = Loan::query()->lockForUpdate()->findOrFail($loan->getKey());

            if (! $loan->isPending()) {
                return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
            }

            $user = User::query()->lockForUpdate()->findOrFail($loan->user_id);
            $book = Book::query()->lockForUpdate()->findOrFail($loan->book_id);

            if ($error = $this->errorKelayakan($user, $book)) {
                return back()->with('error', 'Pengajuan "'.$book->judul.'" tidak dapat disetujui: '.$error);
            }

            $tanggalPinjam = Carbon::today();
            $batasKembali = $tanggalPinjam->copy()->addDays(self::LAMA_PINJAM_HARI);

            $loan->update([
                'status' => Loan::STATUS_ACTIVE,
                'tanggal_pinjam' => $tanggalPinjam,
                'batas_kembali' => $batasKembali,
            ]);

            $book->decrement('jumlah_stok');

            return back()->with('success', 'Pengajuan pinjaman "'.$book->judul.'" atas nama '.$user->name.' disetujui. Jatuh tempo '.$batasKembali->translatedFormat('d M Y').'.');
        });
    }

    /**
     * Tolak pengajuan pinjaman yang masih menunggu persetujuan.
     */
    public function tolak(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('reject', $loan);

        $hasil = $service->reject($loan);

        if ($hasil === null) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        return back()->with('success', 'Pengajuan pinjaman "'.$hasil['judul'].'" atas nama '.$hasil['anggota'].' ditolak.');
    }

    /**
     * Batalkan pinjaman yang masih aktif dan kembalikan stok buku.
     */
    public function batal(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('cancel', $loan);

        $hasil = $service->cancel($loan);

        if ($hasil === null) {
            return back()->with('error', 'Pinjaman ini sudah selesai atau dibatalkan sebelumnya.');
        }

        return back()->with('success', 'Peminjaman "'.$hasil['judul'].'" atas nama '.$hasil['anggota'].' dibatalkan. Stok buku sudah dikembalikan.');
    }

    /**
     * Periksa kelayakan peminjaman untuk seorang anggota.
     * Mengembalikan pesan error atau null bila pinjaman boleh diproses.
     */
    private function errorKelayakan(User $user, Book $book): ?string
    {
        if (! $user->isMember()) {
            return 'Peminjaman hanya bisa diproses untuk akun anggota (siswa, guru, atau pengguna).';
        }

        $aktif = Loan::query()
            ->where('user_id', $user->getKey())
            ->active()
            ->count();

        if ($aktif >= self::KUOTA_PINJAM) {
            return "Kuota pinjam {$user->name} sudah penuh ({$aktif}/".self::KUOTA_PINJAM.').';
        }

        $terlambat = Loan::query()
            ->where('user_id', $user->getKey())
            ->overdue()
            ->count();

        if ($terlambat > 0) {
            return "{$user->name} masih memiliki {$terlambat} pinjaman terlambat. Proses pengembaliannya terlebih dahulu.";
        }

        if ($book->jumlah_stok < 1) {
            return 'Stok buku "'.$book->judul.'" sedang habis.';
        }

        $sudahDipinjam = Loan::query()
            ->where('user_id', $user->getKey())
            ->where('book_id', $book->getKey())
            ->active()
            ->exists();

        if ($sudahDipinjam) {
            return $user->name.' masih meminjam "'.$book->judul.'". Buku yang sama belum dikembalikan.';
        }

        return null;
    }
}
