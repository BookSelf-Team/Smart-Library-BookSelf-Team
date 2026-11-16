<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Setting;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
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
    public function proses(Loan $loan, LoanService $service): RedirectResponse
    {
        $this->authorize('returnBook', $loan);

        $hasil = $service->complete($loan);

        if ($hasil === null) {
            return back()->with('error', 'Pinjaman ini sudah pernah diproses pengembaliannya.');
        }

        $pesan = 'Pengembalian "'.$hasil['judul'].'" atas nama '.$hasil['anggota'].' berhasil diproses.';
        if ($hasil['denda'] > 0) {
            $pesan .= " Denda keterlambatan {$hasil['telat']} hari: Rp ".number_format($hasil['denda'], 0, ',', '.').'.';
        }

        return back()->with('success', $pesan);
    }
}
