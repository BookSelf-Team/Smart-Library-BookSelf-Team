<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    /**
     * Selesaikan pinjaman karena buku sudah dikembalikan.
     *
     * @return array{judul: string, anggota: string, telat: int, denda: int}|null
     */
    public function complete(Loan $loan): ?array
    {
        return DB::transaction(function () use ($loan): ?array {
            // Kunci baris pinjaman agar dua permintaan bersamaan tidak
            // memproses pengembalian yang sama dua kali.
            $loan = Loan::query()
                ->with(['book', 'user'])
                ->lockForUpdate()
                ->find($loan->getKey());

            if ($loan === null || ! $loan->isActive()) {
                return null;
            }

            $today = Carbon::today();
            $telatHari = max(0, (int) $loan->batas_kembali->copy()->startOfDay()->diffInDays($today, false));

            $loan->update([
                'tanggal_dikembalikan' => $today,
                'status' => Loan::STATUS_COMPLETED,
            ]);

            $book = Book::query()->lockForUpdate()->find($loan->book_id);
            $book?->increment('jumlah_stok');

            $denda = $telatHari * Setting::dendaPerHari();

            if ($denda > 0) {
                Fine::updateOrCreate(
                    ['loan_id' => $loan->getKey()],
                    ['nominal' => $denda, 'status_pembayaran' => 'BELUM'],
                );
            }

            return [
                'judul' => $book?->judul ?? 'buku',
                'anggota' => $loan->user?->name ?? 'anggota',
                'telat' => $telatHari,
                'denda' => $denda,
            ];
        });
    }

    /**
     * Batalkan pinjaman aktif dan kembalikan stok buku.
     *
     * @return array{judul: string, anggota: string}|null
     */
    public function cancel(Loan $loan): ?array
    {
        return DB::transaction(function () use ($loan): ?array {
            // Kunci baris pinjaman agar pembatalan dan pengembalian tidak
            // sama-sama mengubah stok untuk pinjaman yang sama.
            $loan = Loan::query()
                ->with(['book', 'user'])
                ->lockForUpdate()
                ->find($loan->getKey());

            if ($loan === null || ! $loan->isActive()) {
                return null;
            }

            $loan->update(['status' => Loan::STATUS_CANCELLED]);

            $book = Book::query()->lockForUpdate()->find($loan->book_id);
            $book?->increment('jumlah_stok');

            return [
                'judul' => $book?->judul ?? 'buku',
                'anggota' => $loan->user?->name ?? 'anggota',
            ];
        });
    }
}
