<?php

namespace App\Http\Controllers\Petugas;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const KUOTA_PINJAM = 3;

    /**
     * Meja Sirkulasi: cari anggota, lihat kartu anggota digital,
     * dan proses pengembalian buku dengan satu klik.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Loan::class);

        // Ringkasan cepat untuk papan sirkulasi.
        $stats = [
            'total_buku' => Book::count(),
            'buku_dipinjam' => Loan::query()->active()->count(),
            'total_anggota' => User::query()->whereIn('role', Role::memberValues())->count(),
            'terlambat' => Loan::query()->overdue()->count(),
        ];

        $q = trim((string) $request->query('q', ''));

        // Hasil pencarian anggota (hanya pemustaka, bukan staf).
        $results = $q === '' ? collect() : User::query()
            ->whereIn('role', Role::memberValues())
            ->where(function ($query) use ($q): void {
                $query->whereLike('name', "%{$q}%")
                    ->orWhereLike('identifier', "%{$q}%")
                    ->orWhereLike('email', "%{$q}%");
            })
            ->withCount(['loans as dipinjam' => fn ($query) => $query->active()])
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Anggota terpilih untuk ditampilkan di meja sirkulasi.
        $member = null;
        $memberId = (int) $request->query('member', 0);
        if ($memberId > 0) {
            $member = User::query()
                ->whereIn('role', Role::memberValues())
                ->withCount(['loans as dipinjam' => fn ($query) => $query->active()])
                ->findOrFail($memberId);
        }

        $pinjamanAktif = collect();
        $riwayat = collect();
        $dendaBelumLunas = 0;

        if ($member !== null) {
            $dendaPerHari = Setting::dendaPerHari();
            $today = Carbon::today();

            // Pinjaman berjalan: buku yang belum dikembalikan beserta denda keterlambatan.
            $pinjamanAktif = Loan::query()
                ->with('book')
                ->where('user_id', $member->getKey())
                ->active()
                ->orderBy('batas_kembali')
                ->get()
                ->map(function (Loan $loan) use ($today, $dendaPerHari): array {
                    $sisaHari = (int) $today->diffInDays($loan->batas_kembali->copy()->startOfDay(), false);

                    return [
                        'id' => $loan->getKey(),
                        'judul' => $loan->book?->judul ?? 'Buku tidak diketahui',
                        'kode' => $loan->book?->kode_buku ?? '-',
                        'tgl_pinjam' => $loan->tanggal_pinjam,
                        'jatuh_tempo' => $loan->batas_kembali,
                        'sisa_hari' => $sisaHari,
                        'telat' => max(0, -$sisaHari),
                        'denda' => max(0, -$sisaHari) * $dendaPerHari,
                    ];
                });

            // Riwayat pengembalian terakhir beserta status dendanya.
            $riwayat = Loan::query()
                ->with(['book', 'fines'])
                ->where('user_id', $member->getKey())
                ->whereNotNull('tanggal_dikembalikan')
                ->orderByDesc('tanggal_dikembalikan')
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->map(function (Loan $loan): array {
                    $dendaTercatat = (int) $loan->fines->sum('nominal');

                    return [
                        'id' => $loan->getKey(),
                        'judul' => $loan->book?->judul ?? 'Buku tidak diketahui',
                        'tgl_pinjam' => $loan->tanggal_pinjam,
                        'tgl_kembali' => $loan->tanggal_dikembalikan,
                        'denda' => $dendaTercatat,
                        'denda_dibayar' => $dendaTercatat > 0 && $loan->fines->every(
                            fn (Fine $fine): bool => in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true),
                        ),
                        'fine_id' => $loan->fines->first()?->getKey(),
                    ];
                });

            $dendaBelumLunas = (int) Fine::query()
                ->whereHas('loan', fn ($query) => $query->where('user_id', $member->getKey()))
                ->whereIn('status_pembayaran', ['BELUM', 'UNPAID'])
                ->sum('nominal');
        }

        return view('petugas.dashboard', compact(
            'stats',
            'q',
            'results',
            'member',
            'pinjamanAktif',
            'riwayat',
            'dendaBelumLunas',
        ));
    }
}
