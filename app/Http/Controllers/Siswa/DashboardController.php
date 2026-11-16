<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Hanya akun anggota/pemustaka yang boleh membuka halaman ini.
        abort_unless($user->isMember(), 403, 'Halaman ini khusus anggota perpustakaan.');

        $siswa = [
            'nama' => $user->name,
            'nisn' => $user->identifier,
            'kelas' => $user->kelas ?? 'XI - MIPA 2',   // ganti jika ada kolom/relasi kelas
        ];

        $loans = Loan::query()
            ->with('book')
            ->where('user_id', $user->getKey())
            ->whereNull('tanggal_dikembalikan')
            ->orderBy('batas_kembali')
            ->get()
            ->map(fn (Loan $loan): array => [
                'id' => $loan->getKey(),
                'title' => $loan->book?->judul ?? 'Buku tidak diketahui',
                'author' => $loan->book?->pengarang ?? '-',
                'isbn' => $loan->book?->kode_buku ?? '-',
                'tgl_pinjam' => $loan->tanggal_pinjam,
                'jatuh_tempo' => $loan->batas_kembali,
            ]);

        $dibaca = Loan::query()
            ->where('user_id', $user->getKey())
            ->whereNotNull('tanggal_dikembalikan')
            ->count();

        // Hitung ringkasan dari data pinjaman
        $today = Carbon::today();
        $sisaHari = fn ($l) => (int) $today->diffInDays($l['jatuh_tempo']->copy()->startOfDay(), false);
        $terdekat = $loans->sortBy(fn ($l) => $sisaHari($l))->first();

        $dendaPerHari = Setting::dendaPerHari();
        $denda = $loans->sum(fn ($l) => max(0, -$sisaHari($l)) * $dendaPerHari);

        $stats = [
            'aktif' => $loans->count(),
            'kuota' => 3,
            'terdekat_hari' => $terdekat ? $sisaHari($terdekat) : null,
            'terdekat_judul' => $terdekat['title'] ?? null,
            'denda' => $denda,
            'dibaca' => $dibaca,
            'target' => 24,
        ];

        $notifications = [];
        foreach ($loans as $l) {
            $d = $sisaHari($l);
            if ($d < 0) {
                $notifications[] = ['icon' => 'error', 'tone' => 'danger', 'title' => 'Buku terlambat',
                    'text' => "{$l['title']} terlambat ".abs($d).' hari. Segera kembalikan.'];
            } elseif ($d <= 1) {
                $notifications[] = ['icon' => 'schedule', 'tone' => 'warn', 'title' => 'Segera jatuh tempo',
                    'text' => "{$l['title']} harus dikembalikan ".($d === 0 ? 'hari ini' : 'besok').'.'];
            }
        }

        $recommendations = Book::query()
            ->orderByDesc('created_at')
            ->limit(4)
            ->get()
            ->map(fn (Book $book): array => [
                'id' => $book->getKey(),
                'cover' => $book->coverUrl(),
                'title' => $book->judul,
                'author' => $book->pengarang,
                'kategori' => 'Koleksi',
                'stok' => $book->jumlah_stok,
            ]);

        return view('siswa.dashboard', compact('siswa', 'loans', 'stats', 'notifications', 'recommendations', 'dendaPerHari'));
    }
}
