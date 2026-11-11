<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_view_and_print_own_loan_invoice(): void
    {
        $siswa = User::factory()->siswa()->create(['name' => 'Ahmad Dahlan']);
        $book = Book::factory()->create(['judul' => 'Laskar Pelangi', 'kode_buku' => 'BKS-1001']);

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::parse('2026-10-10'),
            'batas_kembali' => Carbon::parse('2026-10-17'),
            'status' => 'ACTIVE',
        ]);

        $response = $this->actingAs($siswa)->get(route('invoices.loan', $loan));

        $response->assertOk();
        $response->assertSee('INV-LOAN-');
        $response->assertSee('Laskar Pelangi');
        $response->assertSee('BKS-1001');
        $response->assertSee('Ahmad Dahlan');
        $response->assertSee('window.print()', false);
        $response->assertSee('no-print', false);
    }

    public function test_member_cannot_view_other_members_loan_invoice_anti_idor(): void
    {
        $siswaA = User::factory()->siswa()->create();
        $siswaB = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $loanB = Loan::factory()->create([
            'user_id' => $siswaB->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($siswaA)->get(route('invoices.loan', $loanB));

        $response->assertForbidden();
    }

    public function test_staff_can_view_any_members_loan_invoice(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create(['name' => 'Siti Nurbaya']);
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($admin)->get(route('invoices.loan', $loan))->assertOk();
        $this->actingAs($petugas)->get(route('invoices.loan', $loan))->assertOk();
    }

    public function test_member_can_view_and_print_own_fine_receipt(): void
    {
        $siswa = User::factory()->siswa()->create(['name' => 'Chairil Anwar']);
        $book = Book::factory()->create(['judul' => 'Aku', 'kode_buku' => 'BKS-2002']);

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::parse('2026-10-01'),
            'batas_kembali' => Carbon::parse('2026-10-08'),
            'tanggal_dikembalikan' => Carbon::parse('2026-10-13'),
            'status' => 'COMPLETED',
        ]);

        $fine = Fine::factory()->create([
            'loan_id' => $loan->id,
            'nominal' => 5000,
            'status_pembayaran' => 'LUNAS',
        ]);

        $response = $this->actingAs($siswa)->get(route('invoices.fine', $fine));

        $response->assertOk();
        $response->assertSee('INV-FINE-');
        $response->assertSee('Rp 5.000');
        $response->assertSee('LUNAS / SELESAI');
        $response->assertSee('Chairil Anwar');
        $response->assertSee('Aku');
        $response->assertSee('window.print()', false);
    }

    public function test_member_cannot_view_other_members_fine_receipt_anti_idor(): void
    {
        $siswaA = User::factory()->siswa()->create();
        $siswaB = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $loanB = Loan::factory()->create([
            'user_id' => $siswaB->id,
            'book_id' => $book->id,
        ]);

        $fineB = Fine::factory()->create([
            'loan_id' => $loanB->id,
            'nominal' => 3000,
            'status_pembayaran' => 'BELUM',
        ]);

        $response = $this->actingAs($siswaA)->get(route('invoices.fine', $fineB));

        $response->assertForbidden();
    }

    public function test_staff_can_view_any_fine_receipt(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $fine = Fine::factory()->create([
            'loan_id' => $loan->id,
            'nominal' => 2000,
            'status_pembayaran' => 'BELUM',
        ]);

        $this->actingAs($admin)->get(route('invoices.fine', $fine))->assertOk();
        $this->actingAs($petugas)->get(route('invoices.fine', $fine))->assertOk();
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create(['user_id' => $siswa->id, 'book_id' => $book->id]);
        $fine = Fine::factory()->create(['loan_id' => $loan->id]);

        $this->get(route('invoices.loan', $loan))->assertRedirect(route('login'));
        $this->get(route('invoices.fine', $fine))->assertRedirect(route('login'));
    }

    public function test_receipt_links_are_rendered_on_circulation_views(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'status' => 'ACTIVE',
        ]);

        Fine::factory()->create([
            'loan_id' => $loan->id,
            'nominal' => 1000,
            'status_pembayaran' => 'BELUM',
        ]);

        // Siswa: Pinjaman Saya
        $resPinjaman = $this->actingAs($siswa)->get(route('siswa.pinjaman'));
        $resPinjaman->assertOk();
        $resPinjaman->assertSee(route('invoices.loan', $loan->id), false);

        // Siswa: Riwayat
        $resRiwayat = $this->actingAs($siswa)->get(route('siswa.riwayat'));
        $resRiwayat->assertOk();
        $resRiwayat->assertSee(route('invoices.loan', $loan->id), false);

        // Admin: Peminjaman
        $resAdminPeminjaman = $this->actingAs($admin)->get(route('admin.peminjaman'));
        $resAdminPeminjaman->assertOk();
        $resAdminPeminjaman->assertSee(route('invoices.loan', $loan->id), false);

        // Admin: Pengembalian
        $resAdminPengembalian = $this->actingAs($admin)->get(route('admin.pengembalian'));
        $resAdminPengembalian->assertOk();
        $resAdminPengembalian->assertSee(route('invoices.loan', $loan->id), false);
    }
}
