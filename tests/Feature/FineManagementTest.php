<?php

namespace Tests\Feature;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FineManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_late_return_creates_fine_automatically(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create([
            'batas_kembali' => now()->subDays(5)->toDateString(),
        ]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertDatabaseHas('fines', [
            'loan_id' => $loan->id,
            'nominal' => 5000,
            'status_pembayaran' => 'BELUM',
        ]);
    }

    public function test_on_time_return_does_not_create_fine(): void
    {
        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create([
            'batas_kembali' => now()->addDays(3)->toDateString(),
        ]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('fines', ['loan_id' => $loan->id]);
    }

    public function test_member_can_view_own_fines(): void
    {
        $member = User::factory()->siswa()->create();
        $loan = Loan::factory()->returned()->create([
            'user_id' => $member->id,
            'batas_kembali' => now()->subDays(5)->toDateString(),
        ]);
        Fine::create([
            'loan_id' => $loan->id,
            'nominal' => 5000,
            'status_pembayaran' => 'BELUM',
        ]);

        $this->actingAs($member)->get('/siswa/riwayat')
            ->assertOk();

        $this->assertDatabaseHas('fines', ['loan_id' => $loan->id]);
    }

    public function test_member_cannot_view_other_members_fines(): void
    {
        $member = User::factory()->siswa()->create();
        $other = User::factory()->siswa()->create();
        $otherLoan = Loan::factory()->create(['user_id' => $other->id]);
        Fine::factory()->create(['loan_id' => $otherLoan->id, 'nominal' => 5000]);

        $this->actingAs($member)->get('/siswa/riwayat')
            ->assertOk()
            ->assertDontSee('5000');
    }

    public function test_staff_can_view_all_fines(): void
    {
        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create();
        Fine::factory()->create(['loan_id' => $loan->id, 'nominal' => 10000]);

        $this->actingAs($petugas)->get('/admin/pengembalian')
            ->assertOk();
    }

    public function test_fine_amount_is_calculated_correctly_per_day(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create([
            'batas_kembali' => now()->subDays(10)->toDateString(),
        ]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertDatabaseHas('fines', [
            'loan_id' => $loan->id,
            'nominal' => 10000,
        ]);
    }

    public function test_member_cannot_access_fine_management(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->get('/admin/pengembalian')
            ->assertForbidden();
    }

    public function test_fine_is_associated_with_correct_loan(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create([
            'batas_kembali' => now()->subDays(2)->toDateString(),
        ]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $fine = Fine::where('loan_id', $loan->id)->first();
        $this->assertNotNull($fine);
        $this->assertSame($loan->id, $fine->loan_id);
    }

    public function test_return_on_due_date_does_not_create_fine(): void
    {
        $petugas = User::factory()->petugas()->create();
        $loan = Loan::factory()->create([
            'batas_kembali' => now()->toDateString(),
        ]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('fines', ['loan_id' => $loan->id]);
    }

    public function test_multiple_loans_with_fines(): void
    {
        Setting::setDendaPerHari(1000);

        $petugas = User::factory()->petugas()->create();
        $loan1 = Loan::factory()->create([
            'batas_kembali' => now()->subDays(3)->toDateString(),
        ]);
        $loan2 = Loan::factory()->create([
            'batas_kembali' => now()->subDays(5)->toDateString(),
        ]);

        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan1->id);
        $this->actingAs($petugas)->patch('/admin/pengembalian/'.$loan2->id);

        $this->assertCount(2, Fine::all());
    }
}
