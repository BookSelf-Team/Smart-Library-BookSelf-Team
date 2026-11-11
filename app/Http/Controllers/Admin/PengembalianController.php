<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    /**
     * Halaman Pengembalian — daftar peminjaman aktif yang menunggu proses kembali.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Loan::class);

        $today = Carbon::today();
        $dendaPerHari = Setting::dendaPerHari();

        $loans = Loan::with(['user', 'book'])
            ->active()
            ->orderBy('batas_kembali')
            ->paginate(15);

        $rows = $loans->getCollection()->map(function (Loan $loan) use ($today, $dendaPerHari) {
            $sisaHari = (int) $today->diffInDays($loan->batas_kembali->copy()->startOfDay(), false);
            $telatHari = max(0, -$sisaHari);

            return [
                'loan' => $loan,
                'sisa_hari' => $sisaHari,
                'telat' => $telatHari,
                'denda' => $telatHari * $dendaPerHari,
            ];
        });

        $loans->setCollection($rows);

        return view('admin.pengembalian.index', compact('loans', 'dendaPerHari'));
    }

    /**
     * Proses pengembalian buku + penalty denda keterlambatan (jika ada).
     */
    public function proses(Loan $loan): RedirectResponse
    {
        $this->authorize('returnBook', $loan);

        return DB::transaction(function () use ($loan) {
            // Kunci baris pinjaman agar dua permintaan bersamaan tidak
            // memproses pengembalian yang sama dua kali.
            $loan = Loan::with(['user', 'book'])
                ->lockForUpdate()
                ->findOrFail($loan->getKey());

            if ($loan->tanggal_dikembalikan !== null) {
                return back()->with('error', 'Pinjaman ini sudah pernah diproses pengembaliannya.');
            }

            $today = Carbon::today();
            $telatHari = max(0, (int) $loan->batas_kembali->copy()->startOfDay()->diffInDays($today, false));

            $loan->update([
                'tanggal_dikembalikan' => $today,
                'status' => Loan::STATUS_COMPLETED,
            ]);

            $book = $loan->book()->lockForUpdate()->first();
            $book?->increment('jumlah_stok');

            $denda = $telatHari * Setting::dendaPerHari();

            if ($denda > 0) {
                Fine::updateOrCreate(
                    ['loan_id' => $loan->getKey()],
                    ['nominal' => $denda, 'status_pembayaran' => 'BELUM'],
                );
            }

            $pesan = 'Pengembalian "'.($book?->judul ?? 'buku').'" atas nama '.($loan->user?->name ?? 'anggota').' berhasil diproses.';
            if ($denda > 0) {
                $pesan .= " Denda keterlambatan {$telatHari} hari: Rp ".number_format($denda, 0, ',', '.').'.';
            }

            return back()->with('success', $pesan);
        });
    }
}
