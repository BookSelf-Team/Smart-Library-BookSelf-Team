<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_cannot_extend_another_members_loan(): void
    {
        $owner = User::factory()->siswa()->create();
        $attacker = User::factory()->siswa()->create();
        $loan = Loan::factory()->create(['user_id' => $owner->id]);

        $originalDueDate = $loan->batas_kembali->toDateString();

        $this->actingAs($attacker)
            ->post('/siswa/pinjaman/'.$loan->id.'/perpanjang')
            ->assertForbidden();

        $this->assertSame($originalDueDate, $loan->fresh()->batas_kembali->toDateString());
    }

    public function test_member_can_extend_own_active_loan(): void
    {
        $owner = User::factory()->siswa()->create();
        $loan = Loan::factory()->create(['user_id' => $owner->id]);

        $originalDueDate = $loan->batas_kembali->copy();

        $this->actingAs($owner)
            ->post('/siswa/pinjaman/'.$loan->id.'/perpanjang')
            ->assertRedirect();

        $this->assertSame(
            $originalDueDate->addDays(7)->toDateString(),
            $loan->fresh()->batas_kembali->toDateString(),
        );
    }

    public function test_member_cannot_extend_returned_loan(): void
    {
        $owner = User::factory()->siswa()->create();
        $loan = Loan::factory()->returned()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->post('/siswa/pinjaman/'.$loan->id.'/perpanjang')
            ->assertForbidden();
    }

    public function test_staff_can_return_any_active_loan_and_stock_is_restored(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create(['book_id' => $book->id]);

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertSame('COMPLETED', $loan->fresh()->status);
        $this->assertSame(2, $book->fresh()->jumlah_stok);
    }

    public function test_staff_cannot_return_already_returned_loan(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->returned()->create(['book_id' => $book->id]);

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_late_return_creates_fine(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create([
            'batas_kembali' => now()->subDays(3)->toDateString(),
        ]);

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertDatabaseHas('fines', [
            'loan_id' => $loan->id,
            'nominal' => 3000,
            'status_pembayaran' => 'BELUM',
        ]);
    }

    public function test_member_cannot_borrow_beyond_quota(): void
    {
        $user = User::factory()->siswa()->create();
        Loan::factory()->count(3)->create(['user_id' => $user->id]);
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($user)
            ->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertSessionHas('error');

        $this->assertSame(3, Loan::query()->where('user_id', $user->id)->count());
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_member_borrow_endpoint_ignores_submitted_user_id(): void
    {
        $user = User::factory()->siswa()->create();
        $other = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 3]);

        $this->actingAs($user)->post('/peminjaman/simpan', [
            'book_id' => $book->id,
            'user_id' => $other->id,
        ])->assertRedirect(route('loans.index'));

        $this->assertDatabaseHas('loans', ['user_id' => $user->id, 'book_id' => $book->id]);
        $this->assertDatabaseMissing('loans', ['user_id' => $other->id]);
        $this->assertSame(2, $book->fresh()->jumlah_stok);
    }

    public function test_staff_cannot_use_member_borrow_endpoint(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($petugas)
            ->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertForbidden();

        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_member_dashboard_only_shows_own_loans(): void
    {
        $owner = User::factory()->siswa()->create();
        $other = User::factory()->siswa()->create();

        $ownBook = Book::factory()->create(['judul' => 'Buku Milik Sendiri']);
        $otherBook = Book::factory()->create(['judul' => 'Buku Milik Orang Lain']);

        $ownLoan = Loan::factory()->create(['user_id' => $owner->id, 'book_id' => $ownBook->id]);
        $otherLoan = Loan::factory()->create(['user_id' => $other->id, 'book_id' => $otherBook->id]);

        $this->actingAs($owner)
            ->get('/siswa/dashboard')
            ->assertOk()
            ->assertSee('Buku Milik Sendiri')
            ->assertSee(route('siswa.pinjaman.perpanjang', $ownLoan))
            ->assertDontSee(route('siswa.pinjaman.perpanjang', $otherLoan));
    }

    public function test_member_loan_and_history_pages_only_show_own_loans(): void
    {
        $owner = User::factory()->siswa()->create();
        $other = User::factory()->siswa()->create();

        $ownBook = Book::factory()->create(['judul' => 'Buku Sendiri Aktif']);
        $otherBook = Book::factory()->create(['judul' => 'Buku Orang Lain Aktif']);

        Loan::factory()->create(['user_id' => $owner->id, 'book_id' => $ownBook->id]);
        Loan::factory()->create(['user_id' => $other->id, 'book_id' => $otherBook->id]);

        $this->actingAs($owner)
            ->get('/siswa/pinjaman')
            ->assertOk()
            ->assertSee('Buku Sendiri Aktif')
            ->assertDontSee('Buku Orang Lain Aktif');

        $this->actingAs($owner)
            ->get('/siswa/riwayat')
            ->assertOk()
            ->assertSee('Buku Sendiri Aktif')
            ->assertDontSee('Buku Orang Lain Aktif');
    }

    public function test_member_can_complete_own_active_loan_and_restore_stock(): void
    {
        $owner = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($owner)
            ->patch(route('siswa.pinjaman.selesai', $loan))
            ->assertSessionHas('success');

        $this->assertSame(Loan::STATUS_COMPLETED, $loan->fresh()->status);
        $this->assertNotNull($loan->fresh()->tanggal_dikembalikan);
        $this->assertSame(2, $book->fresh()->jumlah_stok);
    }

    public function test_member_completing_overdue_loan_creates_a_fine(): void
    {
        Setting::setDendaPerHari(1000);

        $owner = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(3)->toDateString(),
        ]);

        $this->actingAs($owner)
            ->patch(route('siswa.pinjaman.selesai', $loan))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('fines', [
            'loan_id' => $loan->id,
            'nominal' => 3000,
            'status_pembayaran' => 'BELUM',
        ]);
    }

    public function test_member_can_cancel_own_active_loan_and_restore_stock(): void
    {
        $owner = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($owner)
            ->patch(route('siswa.pinjaman.batal', $loan))
            ->assertSessionHas('success');

        $this->assertSame(Loan::STATUS_CANCELLED, $loan->fresh()->status);
        $this->assertNull($loan->fresh()->tanggal_dikembalikan);
        $this->assertSame(2, $book->fresh()->jumlah_stok);
    }

    public function test_member_cannot_complete_another_members_loan(): void
    {
        $owner = User::factory()->siswa()->create();
        $attacker = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($attacker)
            ->patch(route('siswa.pinjaman.selesai', $loan))
            ->assertForbidden();

        $this->assertSame(Loan::STATUS_ACTIVE, $loan->fresh()->status);
        $this->assertNull($loan->fresh()->tanggal_dikembalikan);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_member_cannot_cancel_another_members_loan(): void
    {
        $owner = User::factory()->siswa()->create();
        $attacker = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($attacker)
            ->patch(route('siswa.pinjaman.batal', $loan))
            ->assertForbidden();

        $this->assertSame(Loan::STATUS_ACTIVE, $loan->fresh()->status);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_pinjaman_page_shows_selesai_and_batal_actions_for_own_loan(): void
    {
        $owner = User::factory()->siswa()->create();
        $loan = Loan::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->get('/siswa/pinjaman')
            ->assertOk()
            ->assertSee(route('siswa.pinjaman.selesai', $loan))
            ->assertSee(route('siswa.pinjaman.batal', $loan));
    }

    public function test_cancelled_loan_is_not_listed_on_the_active_loans_page(): void
    {
        $owner = User::factory()->siswa()->create();
        $book = Book::factory()->create(['judul' => 'Buku Sudah Dibatalkan']);
        Loan::factory()->cancelled()->create(['user_id' => $owner->id, 'book_id' => $book->id]);

        $this->actingAs($owner)
            ->get('/siswa/pinjaman')
            ->assertOk()
            ->assertDontSee('Buku Sudah Dibatalkan');
    }
}
