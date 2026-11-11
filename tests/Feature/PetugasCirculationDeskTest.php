<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetugasCirculationDeskTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_can_view_the_circulation_desk_dashboard(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard'))
            ->assertOk()
            ->assertSee('Meja')
            ->assertSee('Cari Anggota')
            ->assertSee('Pilih anggota untuk mulai transaksi sirkulasi');
    }

    public function test_member_cannot_access_the_circulation_desk(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->get(route('petugas.dashboard'))->assertForbidden();
    }

    public function test_searching_by_identifier_lists_matching_members_with_active_loan_count(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create(['identifier' => 'SIS-000123']);
        User::factory()->siswa()->create(['identifier' => 'SIS-000999', 'name' => 'Anggota Lain']);
        $book = Book::factory()->create();
        Loan::factory()->create(['user_id' => $siswa->id, 'book_id' => $book->id]);

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['q' => 'SIS-000123']))
            ->assertOk()
            ->assertSee($siswa->name)
            ->assertDontSee('Anggota Lain')
            ->assertSee('1 buku dipinjam');
    }

    public function test_search_only_finds_members_not_staff(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create(['name' => 'Budi Santoso']);
        User::factory()->admin()->create(['name' => 'Admin Santoso']);

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['q' => 'Santoso']))
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Admin Santoso');
    }

    public function test_search_without_results_shows_empty_state(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['q' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Tidak ada anggota yang cocok');
    }

    public function test_selected_member_shows_digital_card_and_active_loans(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create(['identifier' => 'SIS-000123']);
        $book = Book::factory()->create(['judul' => 'Laskar Pelangi']);
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'batas_kembali' => now()->addDays(5)->toDateString(),
        ]);

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['member' => $siswa->id]))
            ->assertOk()
            ->assertSee($siswa->name)
            ->assertSee('SIS-000123')
            ->assertSee('Laskar Pelangi')
            ->assertSee('Buku Belum Dikembalikan')
            ->assertSee('Kembalikan')
            // Tombol pengembalian satu klik harus oranye cerah (spesifikasi meja sirkulasi).
            ->assertSee('bg-[#F97316]', false)
            ->assertSee('hover:bg-[#EA580C]', false)
            ->assertSee('active:bg-[#C2410C]', false)
            ->assertSee('data-loading-text', false)
            ->assertSee('Riwayat & Denda', false);
    }

    public function test_selecting_a_staff_member_is_not_allowed(): void
    {
        $petugas = User::factory()->petugas()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['member' => $admin->id]))
            ->assertNotFound();
    }

    public function test_overdue_active_loan_shows_late_status_and_fine(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['judul' => 'Bumi Manusia']);
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(3)->toDateString(),
        ]);

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['member' => $siswa->id]))
            ->assertOk()
            ->assertSee('Telat 3 hari')
            ->assertSee('3.000');
    }

    public function test_one_click_return_button_processes_the_return(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create(['jumlah_stok' => 2]);
        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);

        $this->actingAs($petugas)
            ->from(route('petugas.dashboard', ['member' => $siswa->id]))
            ->patch(route('admin.pengembalian.proses', $loan->id))
            ->assertRedirect(route('petugas.dashboard', ['member' => $siswa->id]))
            ->assertSessionHas('success');

        $this->assertNotNull($loan->fresh()->tanggal_dikembalikan);
        $this->assertSame(3, $book->fresh()->jumlah_stok);

        // Setelah kembali, pinjaman pindah ke tabel riwayat.
        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['member' => $siswa->id]))
            ->assertOk()
            ->assertSee('Riwayat & Denda', false)
            ->assertSee($book->judul);
    }

    public function test_unpaid_fine_is_summarized_on_the_member_card(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->returned()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
        ]);
        Fine::factory()->create(['loan_id' => $loan->id, 'nominal' => 7500]);

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard', ['member' => $siswa->id]))
            ->assertOk()
            ->assertSee('Denda belum lunas')
            ->assertSee('7.500')
            ->assertSee('Denda belum dibayar');
    }
}
