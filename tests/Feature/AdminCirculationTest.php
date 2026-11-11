<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCirculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_loan_for_guru_member(): void
    {
        $petugas = User::factory()->petugas()->create();
        $guru = User::factory()->guru()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $guru->id,
            'book_id' => $book->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('loans', [
            'user_id' => $guru->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_ACTIVE,
        ]);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_staff_can_create_loan_for_legacy_pengguna_member(): void
    {
        $petugas = User::factory()->petugas()->create();
        $pengguna = User::factory()->pengguna()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $pengguna->id,
            'book_id' => $book->id,
        ])->assertSessionHas('success');

        $this->assertDatabaseHas('loans', [
            'user_id' => $pengguna->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_staff_cannot_create_second_active_loan_for_same_book(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);

        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ])->assertSessionHas('error');

        $this->assertSame(1, Loan::query()->where('user_id', $siswa->id)->where('book_id', $book->id)->count());
        $this->assertSame(2, $book->fresh()->jumlah_stok);
    }

    public function test_staff_can_create_loan_for_same_book_after_previous_loan_returned(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        Loan::factory()->returned()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ])->assertSessionHas('success');

        $this->assertSame(2, Loan::query()->where('user_id', $siswa->id)->where('book_id', $book->id)->count());
    }

    public function test_staff_cannot_create_loan_when_member_has_overdue_loan(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        Loan::factory()->create([
            'user_id' => $siswa->id,
            'batas_kembali' => now()->subDays(2)->toDateString(),
        ]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('loans', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_staff_cannot_create_loan_when_member_quota_is_full(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        Loan::factory()->count(3)->create(['user_id' => $siswa->id]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ])->assertSessionHas('error');

        $this->assertSame(3, Loan::query()->where('user_id', $siswa->id)->count());
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_staff_cannot_create_loan_with_unknown_user_or_book(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create();

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => 999999,
            'book_id' => $book->id,
        ])->assertSessionHasErrors('user_id');

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => User::factory()->siswa()->create()->id,
            'book_id' => 999999,
        ])->assertSessionHasErrors('book_id');

        $this->assertDatabaseCount('loans', 0);
    }

    public function test_peminjaman_page_lists_all_member_roles_in_the_form(): void
    {
        $petugas = User::factory()->petugas()->create();
        $guru = User::factory()->guru()->create(['name' => 'Guru Anggota Uji']);
        User::factory()->pengguna()->create(['name' => 'Pengguna Lama Uji']);

        $this->actingAs($petugas)
            ->get('/admin/peminjaman')
            ->assertOk()
            ->assertSee('Guru Anggota Uji')
            ->assertSee('Pengguna Lama Uji');
    }

    public function test_returning_the_same_loan_twice_only_restores_stock_and_creates_one_fine(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(3)->toDateString(),
        ]);

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertSessionHas('success');

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertSessionHas('error');

        $this->assertSame(2, $book->fresh()->jumlah_stok);
        $this->assertSame(1, Fine::query()->where('loan_id', $loan->id)->count());
        $this->assertSame(Loan::STATUS_COMPLETED, $loan->fresh()->status);
    }

    public function test_pengembalian_page_only_lists_active_loans(): void
    {
        $petugas = User::factory()->petugas()->create();
        $activeBook = Book::factory()->create(['judul' => 'Buku Masih Dipinjam']);
        $returnedBook = Book::factory()->create(['judul' => 'Buku Sudah Kembali']);

        Loan::factory()->create(['book_id' => $activeBook->id]);
        Loan::factory()->returned()->create(['book_id' => $returnedBook->id]);

        $this->actingAs($petugas)
            ->get('/admin/pengembalian')
            ->assertOk()
            ->assertSee('Buku Masih Dipinjam')
            ->assertDontSee('Buku Sudah Kembali');
    }
}
