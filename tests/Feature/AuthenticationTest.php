<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_identifier(): void
    {
        $siswa = User::factory()->siswa()->create([
            'identifier' => '0072938491',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'identifier' => '0072938491',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticatedAs($siswa);
    }

    public function test_user_can_login_with_email(): void
    {
        $siswa = User::factory()->siswa()->create([
            'email' => 'siswa@example.com',
            'password' => 'password',
        ]);

        $this->post('/login', [
            'identifier' => 'siswa@example.com',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticatedAs($siswa);
    }

    public function test_client_supplied_role_is_ignored_and_actual_role_is_used(): void
    {
        $siswa = User::factory()->siswa()->create(['password' => 'password']);

        $this->post('/login', [
            'identifier' => $siswa->identifier,
            'password' => 'password',
            'role' => 'admin',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->assertAuthenticatedAs($siswa);
        $this->assertSame('siswa', $siswa->fresh()->roleEnum()->value);
    }

    public function test_each_staff_role_is_redirected_to_its_own_dashboard(): void
    {
        $admin = User::factory()->admin()->create(['password' => 'password']);
        $pustakawan = User::factory()->pustakawan()->create(['password' => 'password']);
        $petugas = User::factory()->petugas()->create(['password' => 'password']);
        $guru = User::factory()->guru()->create(['password' => 'password']);

        $this->post('/login', ['identifier' => $admin->identifier, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->post('/logout');

        $this->post('/login', ['identifier' => $pustakawan->identifier, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));
        $this->post('/logout');

        $this->post('/login', ['identifier' => $petugas->identifier, 'password' => 'password'])
            ->assertRedirect(route('petugas.dashboard'));
        $this->post('/logout');

        $this->post('/login', ['identifier' => $guru->identifier, 'password' => 'password'])
            ->assertRedirect(route('siswa.dashboard'));
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        User::factory()->siswa()->create(['identifier' => '0072938491', 'password' => 'password']);

        $this->post('/login', [
            'identifier' => '0072938491',
            'password' => 'salah-password',
        ])->assertSessionHasErrors('identifier');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->actingAs($siswa)->post('/logout')->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
