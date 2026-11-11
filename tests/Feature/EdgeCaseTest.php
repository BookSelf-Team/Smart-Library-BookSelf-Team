<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_borrow_book_with_zero_stock(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 0]);

        $this->actingAs($siswa)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertSessionHas('error');

        $this->assertSame(0, $book->fresh()->jumlah_stok);
        $this->assertDatabaseMissing('loans', ['book_id' => $book->id]);
    }

    public function test_stock_decrements_on_borrow(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 5]);

        $this->actingAs($siswa)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertRedirect();

        $this->assertSame(4, $book->fresh()->jumlah_stok);
    }

    public function test_stock_increments_on_return(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['jumlah_stok' => 4]);
        $loan = Loan::factory()->create(['book_id' => $book->id]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertSame(5, $book->fresh()->jumlah_stok);
    }

    public function test_stock_remains_same_on_failed_return(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['jumlah_stok' => 4]);
        $loan = Loan::factory()->returned()->create(['book_id' => $book->id]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertSame(4, $book->fresh()->jumlah_stok);
    }

    public function test_cannot_extend_loan_exceeding_quota(): void
    {
        $siswa = User::factory()->siswa()->create();
        Loan::factory()->count(3)->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)->get('/siswa/pinjaman')
            ->assertOk();
    }

    public function test_guru_treated_as_member_for_circulation(): void
    {
        $guru = User::factory()->guru()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);

        $this->actingAs($guru)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertRedirect();

        $this->assertDatabaseHas('loans', ['user_id' => $guru->id]);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_pengguna_treated_as_member_for_circulation(): void
    {
        $pengguna = User::factory()->pengguna()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);

        $this->actingAs($pengguna)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertRedirect();

        $this->assertDatabaseHas('loans', ['user_id' => $pengguna->id]);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_admin_cannot_use_member_borrow_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($admin)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertForbidden();

        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_multiple_simultaneous_borrows_do_not_exceed_stock(): void
    {
        $siswa1 = User::factory()->siswa()->create();
        $siswa2 = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($siswa1)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertRedirect();

        $this->actingAs($siswa2)->post('/peminjaman/simpan', ['book_id' => $book->id])
            ->assertSessionHas('error');

        $this->assertSame(0, $book->fresh()->jumlah_stok);
    }

    public function test_password_update_with_confirmation(): void
    {
        $siswa = User::factory()->siswa()->create(['password' => 'oldpassword']);

        $this->actingAs($siswa)->post('/siswa/profil/sandi', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect();

        $this->assertTrue(
            Hash::check('newpassword123', $siswa->fresh()->password)
        );
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $siswa = User::factory()->siswa()->create(['password' => 'oldpassword']);

        $this->actingAs($siswa)->post('/siswa/profil/sandi', [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(
            Hash::check('oldpassword', $siswa->fresh()->password)
        );
    }

    public function test_password_update_fails_without_confirmation(): void
    {
        $siswa = User::factory()->siswa()->create(['password' => 'oldpassword']);

        $this->actingAs($siswa)->post('/siswa/profil/sandi', [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'differentpassword',
        ])->assertSessionHasErrors('password');
    }

    public function test_profile_update_with_valid_email(): void
    {
        $siswa = User::factory()->siswa()->create(['email' => 'old@example.com']);

        $this->actingAs($siswa)->post('/siswa/profil', [
            'name' => $siswa->name,
            'email' => 'new@example.com',
        ])->assertRedirect();

        $this->assertSame('new@example.com', $siswa->fresh()->email);
    }

    public function test_profile_update_fails_with_duplicate_email(): void
    {
        $siswa1 = User::factory()->siswa()->create(['email' => 'siswa1@example.com']);
        User::factory()->siswa()->create(['email' => 'siswa2@example.com']);

        $this->actingAs($siswa1)->post('/siswa/profil', [
            'name' => $siswa1->name,
            'email' => 'siswa2@example.com',
        ])->assertSessionHasErrors('email');

        $this->assertSame('siswa1@example.com', $siswa1->fresh()->email);
    }

    public function test_staff_can_create_loan_for_member(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('loans', [
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);
        $this->assertSame(1, $book->fresh()->jumlah_stok);
    }

    public function test_staff_cannot_create_loan_for_staff_account(): void
    {
        $petugas = User::factory()->petugas()->create();
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);

        $this->actingAs($petugas)->post('/admin/peminjaman', [
            'user_id' => $admin->id,
            'book_id' => $book->id,
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('loans', [
            'user_id' => $admin->id,
            'book_id' => $book->id,
        ]);
    }

    public function test_loan_extension_adds_correct_days(): void
    {
        $siswa = User::factory()->siswa()->create();
        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'batas_kembali' => now()->toDateString(),
        ]);

        $originalDueDate = $loan->batas_kembali->copy();

        $this->actingAs($siswa)->post('/siswa/pinjaman/'.$loan->id.'/perpanjang')
            ->assertRedirect();

        $this->assertSame(
            $originalDueDate->addDays(7)->toDateString(),
            $loan->fresh()->batas_kembali->toDateString(),
        );
    }

    public function test_cannot_extend_same_loan_twice_quickly(): void
    {
        $siswa = User::factory()->siswa()->create();
        $loan = Loan::factory()->create(['user_id' => $siswa->id]);

        $this->actingAs($siswa)->post('/siswa/pinjaman/'.$loan->id.'/perpanjang')
            ->assertRedirect();

        $firstExtension = $loan->fresh()->batas_kembali;

        $this->actingAs($siswa)->post('/siswa/pinjaman/'.$loan->id.'/perpanjang')
            ->assertRedirect();

        $secondExtension = $loan->fresh()->batas_kembali;

        $this->assertNotSame($firstExtension->toDateString(), $secondExtension->toDateString());
    }
}
