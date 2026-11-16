<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KatalogAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_katalog(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('siswa.katalog'))->assertOk();
    }

    public function test_staff_can_open_katalog(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->get(route('siswa.katalog'))->assertOk();
    }

    public function test_book_detail_renders_cover_from_public_storage(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['cover_image' => 'covers/contoh.jpg']);

        $this->actingAs($admin)
            ->get(route('siswa.katalog.show', $book))
            ->assertOk()
            ->assertSee('src="'.asset('storage/covers/contoh.jpg').'"', false);
    }

    public function test_book_detail_keeps_absolute_cover_url(): void
    {
        $admin = User::factory()->admin()->create();
        $book = Book::factory()->create(['cover_image' => 'https://example.com/sampul.jpg']);

        $this->actingAs($admin)
            ->get(route('siswa.katalog.show', $book))
            ->assertOk()
            ->assertSee('src="https://example.com/sampul.jpg"', false);
    }
}
