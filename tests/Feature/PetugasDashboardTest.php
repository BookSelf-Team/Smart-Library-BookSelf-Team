<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_dashboard_renders_search_and_selected_member(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create(['name' => 'Budi Santoso']);
        $book = Book::factory()->create();
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($petugas)->get('/petugas/dashboard?q=Budi')->assertOk();
        $this->actingAs($petugas)->get('/petugas/dashboard?member='.$siswa->id)->assertOk();
        $this->actingAs($petugas)->get('/admin/petugas?q=Budi&member='.$siswa->id)->assertOk();
    }

    public function test_petugas_login_redirects_to_petugas_dashboard(): void
    {
        User::factory()->petugas()->create([
            'identifier' => 'P-TEST',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'identifier' => 'P-TEST',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
    }

    public function test_petugas_login_ignores_dashboard_intended_url(): void
    {
        User::factory()->petugas()->create([
            'identifier' => 'P-TEST2',
            'password' => 'password123',
        ]);

        // Tamu membuka dashboard admin lalu login: petugas tetap ke dashboard-nya.
        $this->get('/admin/dashboard')->assertRedirect(route('login'));

        $response = $this->post('/login', [
            'identifier' => 'P-TEST2',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
    }

    public function test_petugas_login_honors_non_dashboard_intended_url(): void
    {
        User::factory()->petugas()->create([
            'identifier' => 'P-TEST3',
            'password' => 'password123',
        ]);

        $this->get('/admin/peminjaman')->assertRedirect(route('login'));

        $response = $this->post('/login', [
            'identifier' => 'P-TEST3',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/peminjaman');
    }
}
