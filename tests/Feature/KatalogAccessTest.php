<?php

namespace Tests\Feature;

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
}
