<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Tampilkan lembar bukti transaksi peminjaman buku (Invoice / Receipt).
     */
    public function loan(Loan $loan): View
    {
        $this->authorize('view', $loan);

        $loan->loadMissing(['user', 'book', 'fines']);

        $invoiceNumber = sprintf('INV-LOAN-%05d-%s', $loan->id, $loan->tanggal_pinjam->format('Ymd'));

        return view('invoices.loan', array_merge(
            compact('loan', 'invoiceNumber'),
            ['dendaPerHari' => Setting::dendaPerHari()],
        ));
    }

    /**
     * Tampilkan lembar bukti tagihan / pembayaran denda keterlambatan (Fine Receipt).
     */
    public function fine(Fine $fine): View
    {
        $this->authorize('view', $fine);

        $fine->loadMissing(['loan.user', 'loan.book']);

        $datePart = $fine->created_at ? $fine->created_at->format('Ymd') : date('Ymd');
        $invoiceNumber = sprintf('INV-FINE-%05d-%s', $fine->id, $datePart);

        // Tarif per hari diambil dari denda itu sendiri (nominal ÷ hari terlambat)
        // agar struk tetap akurat walau tarif berubah setelah denda dibuat.
        $telatHari = 0;
        if ($fine->loan?->batas_kembali && $fine->loan?->tanggal_dikembalikan) {
            $telatHari = max(0, (int) $fine->loan->batas_kembali
                ->copy()
                ->startOfDay()
                ->diffInDays($fine->loan->tanggal_dikembalikan, false));
        }
        $tarifPerHari = $telatHari > 0 ? intdiv((int) $fine->nominal, $telatHari) : (int) $fine->nominal;

        return view('invoices.fine', array_merge(
            compact('fine', 'invoiceNumber'),
            ['tarifPerHari' => $tarifPerHari],
        ));
    }
}
