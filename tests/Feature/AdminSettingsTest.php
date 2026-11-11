<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_denda_per_hari_defaults_to_zero(): void
    {
        $this->assertSame(0, Setting::dendaPerHari());
    }

    public function test_admin_can_view_and_update_denda_per_hari(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.pengaturan'))
            ->assertOk()
            ->assertSee('Tarif Denda Keterlambatan');

        $this->actingAs($admin)
            ->put(route('admin.pengaturan.update'), ['denda_per_hari' => 2500])
            ->assertRedirect(route('admin.pengaturan'))
            ->assertSessionHas('success');

        $this->assertSame(2500, Setting::dendaPerHari());
    }

    public function test_non_admin_cannot_access_settings_pages(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->actingAs($petugas)->get(route('admin.pengaturan'))->assertForbidden();
        $this->actingAs($petugas)->put(route('admin.pengaturan.update'), ['denda_per_hari' => 500])->assertForbidden();
        $this->assertSame(0, Setting::dendaPerHari());
    }

    public function test_settings_validation_rejects_negative_or_missing_rate(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.pengaturan.update'), ['denda_per_hari' => -100])
            ->assertSessionHasErrors('denda_per_hari');

        $this->actingAs($admin)
            ->put(route('admin.pengaturan.update'), [])
            ->assertSessionHasErrors('denda_per_hari');

        $this->assertSame(0, Setting::dendaPerHari());
    }

    public function test_returning_overdue_loan_with_zero_rate_creates_no_fine(): void
    {
        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(5)->toDateString(),
        ]);

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertSessionHas('success');

        $this->assertSame(0, Fine::query()->where('loan_id', $loan->id)->count());
    }

    public function test_returning_overdue_loan_uses_the_configured_rate_automatically(): void
    {
        Setting::setDendaPerHari(2000);

        $petugas = User::factory()->petugas()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create([
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(4)->toDateString(),
        ]);

        $this->actingAs($petugas)
            ->patch('/admin/pengembalian/'.$loan->id)
            ->assertSessionHas('success');

        $this->assertSame(8000, (int) Fine::query()->where('loan_id', $loan->id)->value('nominal'));
    }

    public function test_member_denda_display_follows_the_configured_rate(): void
    {
        Setting::setDendaPerHari(1500);

        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(2)->toDateString(),
        ]);

        $this->actingAs($siswa)
            ->get('/siswa/pinjaman')
            ->assertOk()
            ->assertSee('3.000');
    }

    public function test_member_dashboard_denda_follows_the_configured_rate(): void
    {
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();
        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'batas_kembali' => now()->subDays(3)->toDateString(),
        ]);

        // Tarif 0 (default): dashboard menampilkan Rp 0, bukan angka hardcoded.
        $this->actingAs($siswa)
            ->get('/siswa/dashboard')
            ->assertOk()
            ->assertSee('Rp 0')
            ->assertDontSee('Rp 1.000');

        Setting::setDendaPerHari(2000);

        $this->actingAs($siswa)
            ->get('/siswa/dashboard')
            ->assertOk()
            ->assertSee('6.000');
    }

    public function test_staff_pengembalian_page_shows_the_configured_rate(): void
    {
        $petugas = User::factory()->petugas()->create();

        Setting::setDendaPerHari(2500);

        $this->actingAs($petugas)
            ->get('/admin/pengembalian')
            ->assertOk()
            ->assertSee('Rp 2.500/hari');

        Setting::setDendaPerHari(0);

        $this->actingAs($petugas)
            ->get('/admin/pengembalian')
            ->assertOk()
            ->assertSee('Denda keterlambatan sedang tidak diaktifkan.');
    }
}
