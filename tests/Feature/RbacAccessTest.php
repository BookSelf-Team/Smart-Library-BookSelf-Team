<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_protected_area(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
        $this->get('/siswa/dashboard')->assertRedirect('/login');
        $this->get('/katalog')->assertRedirect('/login');
    }

    public function test_admin_can_access_user_management(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin/users')->assertOk();
    }

    public function test_pustakawan_cannot_access_user_management(): void
    {
        $pustakawan = User::factory()->pustakawan()->create();

        $this->actingAs($pustakawan)->get('/admin/users')->assertForbidden();
    }

    public function test_pustakawan_can_manage_books(): void
    {
        $pustakawan = User::factory()->pustakawan()->create();

        $this->actingAs($pustakawan)->get('/admin/buku/tambah')->assertOk();

        $this->actingAs($pustakawan)->post('/admin/buku', [
            'judul' => 'Buku Baru',
            'pengarang' => 'Penulis Baru',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 2,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseHas('books', ['judul' => 'Buku Baru', 'pengarang' => 'Penulis Baru']);
    }

    public function test_petugas_can_view_books_but_cannot_manage_them(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create();

        $this->actingAs($petugas)->get('/admin/buku')->assertOk();
        $this->actingAs($petugas)->get('/admin/buku/tambah')->assertForbidden();
        $this->actingAs($petugas)->post('/admin/buku', [
            'judul' => 'Tidak Boleh',
            'pengarang' => 'X',
            'jumlah_stok' => 1,
        ])->assertForbidden();
        $this->actingAs($petugas)->delete('/admin/buku/'.$book->id)->assertForbidden();
    }

    public function test_petugas_can_access_circulation_pages(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->get('/admin/peminjaman')->assertOk();
        $this->actingAs($petugas)->get('/admin/pengembalian')->assertOk();
        $this->actingAs($petugas)->get('/petugas/dashboard')->assertOk();
    }

    public function test_guru_can_access_member_area_but_not_staff_area(): void
    {
        $guru = User::factory()->guru()->create();

        $this->actingAs($guru)->get('/siswa/dashboard')->assertOk();
        $this->actingAs($guru)->get('/katalog')->assertOk();
        $this->actingAs($guru)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($guru)->get('/admin/peminjaman')->assertForbidden();
    }

    public function test_siswa_cannot_access_staff_area(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->get('/siswa/dashboard')->assertOk();
        $this->actingAs($siswa)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($siswa)->get('/admin/buku')->assertForbidden();
        $this->actingAs($siswa)->get('/admin/users')->assertForbidden();
    }

    public function test_legacy_user_role_is_treated_as_member(): void
    {
        $pengguna = User::factory()->pengguna()->create();

        $this->actingAs($pengguna)->get('/siswa/dashboard')->assertOk();
        $this->actingAs($pengguna)->get('/katalog')->assertOk();
        $this->actingAs($pengguna)->get('/admin/dashboard')->assertForbidden();
    }

    public function test_admin_can_create_user_with_identifier(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Petugas Baru',
            'identifier' => 'P-999',
            'email' => 'petugas.baru@example.com',
            'role' => 'petugas',
            'password' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'identifier' => 'P-999',
            'role' => 'petugas',
        ]);
    }

    public function test_admin_can_create_user_without_identifier_and_one_is_generated_automatically(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Siswa Baru',
            'email' => 'siswa.baru@example.com',
            'role' => 'siswa',
            'password' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $siswa = User::query()->where('email', 'siswa.baru@example.com')->first();

        $this->assertNotNull($siswa);
        $this->assertMatchesRegularExpression('/^SIS-\d{6}$/', (string) $siswa->identifier);
    }

    public function test_admin_cannot_demote_the_last_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put('/admin/users/'.$admin->id, [
            'name' => $admin->name,
            'identifier' => $admin->identifier,
            'email' => $admin->email,
            'role' => 'petugas',
        ])->assertForbidden();

        $this->assertSame('admin', $admin->fresh()->roleEnum()->value);
    }

    public function test_member_cannot_process_book_return(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($siswa)->patch('/admin/pengembalian/'.$loan->id)->assertForbidden();
    }
}
