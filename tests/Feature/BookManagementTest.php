<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_book(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku Baru',
            'pengarang' => 'Penulis Terkenal',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 5,
            'sinopsis' => 'Sinopsis buku baru',
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseHas('books', [
            'judul' => 'Buku Baru',
            'pengarang' => 'Penulis Terkenal',
            'jumlah_stok' => 5,
        ]);
    }

    public function test_pustakawan_can_create_book(): void
    {
        $pustakawan = User::factory()->pustakawan()->create();

        $this->actingAs($pustakawan)->post('/admin/buku', [
            'judul' => 'Buku Pustakawan',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 3,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseHas('books', ['judul' => 'Buku Pustakawan']);
    }

    public function test_petugas_cannot_create_book(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->post('/admin/buku', [
            'judul' => 'Buku Petugas',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 2,
        ])->assertForbidden();

        $this->assertDatabaseMissing('books', ['judul' => 'Buku Petugas']);
    }

    public function test_siswa_cannot_create_book(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->post('/admin/buku', [
            'judul' => 'Buku Siswa',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 1,
        ])->assertForbidden();

        $this->assertDatabaseMissing('books', ['judul' => 'Buku Siswa']);
    }

    public function test_admin_can_update_book(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['judul' => 'Judul Lama']);

        $this->actingAs($admin)->put('/admin/buku/'.$book->id, [
            'judul' => 'Judul Baru',
            'pengarang' => $book->pengarang,
            'tahun_terbit' => $book->tahun_terbit,
            'jumlah_stok' => $book->jumlah_stok,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertSame('Judul Baru', $book->fresh()->judul);
    }

    public function test_pustakawan_can_update_book(): void
    {
        $pustakawan = User::factory()->pustakawan()->create();
        $book = Book::factory()->create(['judul' => 'Judul Lama']);

        $this->actingAs($pustakawan)->put('/admin/buku/'.$book->id, [
            'judul' => 'Judul Baru',
            'pengarang' => $book->pengarang,
            'tahun_terbit' => $book->tahun_terbit,
            'jumlah_stok' => $book->jumlah_stok,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertSame('Judul Baru', $book->fresh()->judul);
    }

    public function test_petugas_cannot_update_book(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create(['judul' => 'Judul Lama']);

        $this->actingAs($petugas)->put('/admin/buku/'.$book->id, [
            'judul' => 'Judul Baru',
            'pengarang' => $book->pengarang,
            'tahun_terbit' => $book->tahun_terbit,
            'jumlah_stok' => $book->jumlah_stok,
        ])->assertForbidden();

        $this->assertSame('Judul Lama', $book->fresh()->judul);
    }

    public function test_admin_can_delete_book_without_active_loans(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($admin)->delete('/admin/buku/'.$book->id)
            ->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_admin_cannot_delete_book_with_active_loans(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);
        Loan::factory()->create(['book_id' => $book->id]);

        $this->actingAs($admin)->delete('/admin/buku/'.$book->id)
            ->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    public function test_pustakawan_can_delete_book_without_active_loans(): void
    {
        $pustakawan = User::factory()->pustakawan()->create();
        $book = Book::factory()->create(['jumlah_stok' => 1]);

        $this->actingAs($pustakawan)->delete('/admin/buku/'.$book->id)
            ->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_book_search_by_title(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create(['judul' => 'Laravel untuk Pemula']);
        Book::factory()->create(['judul' => 'Python Advanced']);
        Book::factory()->create(['judul' => 'JavaScript Modern']);

        $this->actingAs($admin)->get('/admin/buku?q=Laravel')
            ->assertOk()
            ->assertSee('Laravel untuk Pemula')
            ->assertDontSee('Python Advanced')
            ->assertDontSee('JavaScript Modern');
    }

    public function test_book_search_by_author(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create(['pengarang' => 'John Doe']);
        Book::factory()->create(['pengarang' => 'Jane Smith']);
        Book::factory()->create(['pengarang' => 'Bob Johnson']);

        $this->actingAs($admin)->get('/admin/buku?q=John')
            ->assertOk()
            ->assertSee('John Doe')
            ->assertDontSee('Jane Smith');
    }

    public function test_book_search_by_code(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create(['kode_buku' => 'BKS-0001']);
        Book::factory()->create(['kode_buku' => 'BKS-0002']);
        Book::factory()->create(['kode_buku' => 'BKS-0003']);

        $this->actingAs($admin)->get('/admin/buku?q=BKS-0002')
            ->assertOk()
            ->assertSee('BKS-0002')
            ->assertDontSee('BKS-0001');
    }

    public function test_staff_can_view_book_catalog(): void
    {
        $admin = User::factory()->admin()->create();
        $pustakawan = User::factory()->pustakawan()->create();
        $petugas = User::factory()->petugas()->create();

        Book::factory()->create(['judul' => 'Buku Tes']);

        $this->actingAs($admin)->get('/admin/buku')->assertOk()->assertSee('Buku Tes');
        $this->actingAs($pustakawan)->get('/admin/buku')->assertOk()->assertSee('Buku Tes');
        $this->actingAs($petugas)->get('/admin/buku')->assertOk()->assertSee('Buku Tes');
    }

    public function test_member_can_view_catalog(): void
    {
        $siswa = User::factory()->siswa()->create();
        Book::factory()->create(['judul' => 'Buku Katalog']);

        $this->actingAs($siswa)->get('/katalog')
            ->assertOk()
            ->assertSee('Buku Katalog');
    }

    public function test_validation_requires_judul(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/buku', [
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 1,
        ])->assertSessionHasErrors('judul');
    }

    public function test_validation_requires_pengarang(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 1,
        ])->assertSessionHasErrors('pengarang');
    }

    public function test_validation_requires_tahun_terbit(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku',
            'pengarang' => 'Penulis',
            'jumlah_stok' => 1,
        ])->assertSessionHasErrors('tahun_terbit');
    }

    public function test_validation_tahun_terbit_must_be_between_1000_and_2100(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 999,
            'jumlah_stok' => 1,
        ])->assertSessionHasErrors('tahun_terbit');

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2101,
            'jumlah_stok' => 1,
        ])->assertSessionHasErrors('tahun_terbit');
    }

    public function test_validation_jumlah_stok_must_be_at_least_0(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku',
            'pengarang' => 'Penulis',
            'tahun_terbit' => 2024,
            'jumlah_stok' => -1,
        ])->assertSessionHasErrors('jumlah_stok');
    }

    public function test_admin_cannot_create_duplicate_book(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create([
            'judul' => 'Buku Duplikat',
            'pengarang' => 'Penulis Sama',
            'tahun_terbit' => 2024,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.buku.tambah'))
            ->post('/admin/buku', [
                'judul' => 'Buku Duplikat',
                'pengarang' => 'Penulis Sama',
                'tahun_terbit' => 2024,
                'jumlah_stok' => 2,
                'sinopsis' => 'Percobaan menambah buku yang sama',
            ])
            ->assertRedirect(route('admin.buku.tambah'))
            ->assertSessionHasErrors('judul')
            ->assertSessionHasInput('judul', 'Buku Duplikat');

        $this->assertDatabaseCount('books', 1);
    }

    public function test_admin_can_create_book_with_same_title_but_different_details(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create([
            'judul' => 'Buku Sama',
            'pengarang' => 'Penulis Pertama',
            'tahun_terbit' => 2024,
        ]);

        $this->actingAs($admin)->post('/admin/buku', [
            'judul' => 'Buku Sama',
            'pengarang' => 'Penulis Kedua',
            'tahun_terbit' => 2024,
            'jumlah_stok' => 1,
        ])->assertRedirect(route('admin.buku.index'));

        $this->assertDatabaseCount('books', 2);
    }

    public function test_admin_cannot_update_book_to_duplicate_another_book(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create([
            'judul' => 'Buku Pertama',
            'pengarang' => 'Penulis Sama',
            'tahun_terbit' => 2024,
        ]);
        Book::factory()->create([
            'judul' => 'Buku Kedua',
            'pengarang' => 'Penulis Sama',
            'tahun_terbit' => 2024,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.buku.index'))
            ->put('/admin/buku/'.$book->id, [
                'judul' => 'Buku Kedua',
                'pengarang' => 'Penulis Sama',
                'tahun_terbit' => 2024,
                'jumlah_stok' => 1,
            ])
            ->assertRedirect(route('admin.buku.index'))
            ->assertSessionHasErrors('judul');

        $this->assertSame('Buku Pertama', $book->fresh()->judul);
    }
}
