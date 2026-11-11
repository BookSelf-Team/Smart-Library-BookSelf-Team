<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PinjamanController extends Controller
{
    private const KUOTA_PINJAM = 3;

    private const LAMA_PINJAM_HARI = 7;

    /**
     * Halaman "Pinjaman Saya" — daftar buku yang sedang dipinjam.
     */
    public function index(Request $request): View
    {
        $this->authorize('is-member');

        $user = $request->user();

        $siswa = $this->siswa($user);

        $loans = $this->activeLoans($user);

        $today = Carbon::today();
        $dendaPerHari = Setting::dendaPerHari();
        $sisaHari = fn ($l) => (int) $today->diffInDays($l['jatuh_tempo']->copy()->startOfDay(), false);
        $terdekat = $loans->sortBy(fn ($l) => $sisaHari($l))->first();

        $stats = [
            'aktif' => $loans->count(),
            'kuota' => self::KUOTA_PINJAM,
            'sisa_kuota' => max(0, self::KUOTA_PINJAM - $loans->count()),
            'terdekat_hari' => $terdekat ? $sisaHari($terdekat) : null,
            'terdekat_judul' => $terdekat['title'] ?? null,
            'denda' => $loans->sum(fn ($l) => max(0, -$sisaHari($l)) * $dendaPerHari),
        ];

        $notifications = [];
        foreach ($loans as $l) {
            $d = $sisaHari($l);
            if ($d < 0) {
                $notifications[] = [
                    'icon' => 'error',
                    'tone' => 'danger',
                    'title' => 'Buku terlambat',
                    'text' => "{$l['title']} terlambat ".abs($d).' hari. Segera kembalikan ke petugas.',
                ];
            } elseif ($d <= 1) {
                $notifications[] = [
                    'icon' => 'schedule',
                    'tone' => 'warn',
                    'title' => 'Segera jatuh tempo',
                    'text' => "{$l['title']} harus dikembalikan ".($d === 0 ? 'hari ini' : 'besok').'.',
                ];
            }
        }

        return view('siswa.pinjaman', compact('siswa', 'loans', 'stats', 'notifications', 'dendaPerHari'));
    }

    /**
     * Halaman "Riwayat & Denda" — arsip peminjaman beserta denda keterlambatan.
     */
    public function riwayat(Request $request): View
    {
        $this->authorize('is-member');

        $user = $request->user();

        $siswa = $this->siswa($user);

        $filter = $request->query('filter', 'semua');
        if (! in_array($filter, ['semua', 'aktif', 'terlambat', 'lunas'], true)) {
            $filter = 'semua';
        }

        $semua = $this->history($user);
        $dendaPerHari = Setting::dendaPerHari();

        $riwayat = $semua->filter(fn ($r) => match ($filter) {
            'aktif' => $r['status'] === 'dipinjam',
            'terlambat' => $r['denda'] > 0 && ! $r['denda_dibayar'],
            'lunas' => $r['denda'] > 0 && $r['denda_dibayar'],
            default => true,
        })->values();

        $stats = [
            'total' => $semua->count(),
            'selesai' => $semua->where('status', 'selesai')->count(),
            'aktif' => $semua->where('status', 'dipinjam')->count(),
            'terlambat' => $semua->filter(fn ($r) => $r['denda'] > 0)->count(),
            'denda_total' => $semua->sum('denda'),
            'denda_belum' => $semua->filter(fn ($r) => ! $r['denda_dibayar'])->sum('denda'),
            'denda_lunas' => $semua->filter(fn ($r) => $r['denda_dibayar'])->sum('denda'),
        ];

        return view('siswa.riwayat', compact('siswa', 'riwayat', 'stats', 'filter', 'dendaPerHari'));
    }

    /**
     * Perpanjang pinjaman milik sendiri (+7 hari).
     */
    public function perpanjang(Request $request, Loan $loan): RedirectResponse
    {
        $this->authorize('extend', $loan);

        $loan->update([
            'batas_kembali' => $loan->batas_kembali->copy()->addDays(self::LAMA_PINJAM_HARI),
        ]);

        return back()->with('success', 'Pinjaman "'.$loan->book?->judul.'" berhasil diperpanjang '.self::LAMA_PINJAM_HARI.' hari.');
    }

    private function siswa(User $user): array
    {
        return [
            'nama' => $user->name,
            'nisn' => $user->identifier,
            'kelas' => $user->kelas ?? 'XI - MIPA 2',
        ];
    }

    /**
     * Daftar pinjaman aktif milik pengguna yang sedang login.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function activeLoans(User $user): Collection
    {
        return Loan::query()
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
    }

    /**
     * Riwayat peminjaman milik pengguna yang sedang login.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function history(User $user): Collection
    {
        $dendaPerHari = Setting::dendaPerHari();

        return Loan::query()
            ->with(['book', 'fines'])
            ->where('user_id', $user->getKey())
            ->orderByDesc('tanggal_pinjam')
            ->orderByDesc('id')
            ->get()
            ->map(function (Loan $loan) use ($dendaPerHari): array {
                $dendaTercatat = (int) $loan->fines->sum('nominal');
                $dendaDibayar = $dendaTercatat > 0 && $loan->fines->every(
                    fn ($fine): bool => in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true),
                );

                $tglKembali = $loan->tanggal_dikembalikan;
                $overdueHari = $tglKembali === null
                    ? max(0, (int) $loan->batas_kembali->copy()->startOfDay()->diffInDays(Carbon::today(), false))
                    : 0;
                $denda = $dendaTercatat > 0 ? $dendaTercatat : $overdueHari * $dendaPerHari;

                return [
                    'id' => $loan->getKey(),
                    'fine_id' => $loan->fines->first()?->id,
                    'title' => $loan->book?->judul ?? 'Buku tidak diketahui',
                    'author' => $loan->book?->pengarang ?? '-',
                    'status' => $tglKembali ? 'selesai' : 'dipinjam',
                    'tgl_pinjam' => $loan->tanggal_pinjam,
                    'tgl_kembali' => $tglKembali,
                    'hari' => (int) $loan->tanggal_pinjam->diffInDays($tglKembali ?? Carbon::today()),
                    'denda' => $denda,
                    'denda_dibayar' => $dendaDibayar,
                ];
            });
    }
}
