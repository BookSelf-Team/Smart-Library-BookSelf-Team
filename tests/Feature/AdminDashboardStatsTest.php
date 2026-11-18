<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AdminDashboardStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_operational_and_aggregate_statistics(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        $bookA = Book::factory()->create(['jumlah_stok' => 4]);
        $bookB = Book::factory()->create(['jumlah_stok' => 2]);

        // Satu pinjaman aktif yang sudah lewat tempo.
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $bookB->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10),
            'batas_kembali' => Carbon::today()->subDays(3),
            'tanggal_dikembalikan' => null,
            'status' => Loan::STATUS_ACTIVE,
        ]);

        // Dua pinjaman selesai, masing-masing dengan denda belum lunas dan lunas.
        $lateLoan = Loan::factory()->returned()->create([
            'book_id' => $bookA->id,
        ]);
        Fine::factory()->create([
            'loan_id' => $lateLoan->id,
            'nominal' => 12000,
            'status_pembayaran' => 'BELUM',
        ]);

        $paidLoan = Loan::factory()->returned()->create([
            'book_id' => $bookA->id,
        ]);
        Fine::factory()->create([
            'loan_id' => $paidLoan->id,
            'nominal' => 5000,
            'status_pembayaran' => 'LUNAS',
        ]);

        // Satu transaksi dibatalkan.
        Loan::factory()->cancelled()->create([
            'book_id' => $bookA->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSeeText('Rekap Status Peminjaman')
            ->assertSeeText('Rekap Status Denda')
            ->assertSeeText('4 transaksi')
            ->assertSeeTextInOrder(['Inventaris Koleksi', '7', 'Buku Fisik'])
            ->assertSeeText('2 judul • 6 tersedia di rak')
            ->assertSeeText('Rp 12.000')
            ->assertSeeText('Rp 5.000')
            ->assertSeeText('Rp 17.000')
            ->assertSeeText('1 denda belum dibayar')
            ->assertSeeText('1 denda sudah dibayar');
    }

    public function test_admin_dashboard_shows_zero_values_when_library_is_empty(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('0 judul • 0 tersedia di rak')
            ->assertSeeText('0 transaksi')
            ->assertSeeText('0 denda belum dibayar')
            ->assertSeeText('0 denda sudah dibayar');
    }
}
