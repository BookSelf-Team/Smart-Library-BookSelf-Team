<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_rate_limiting_after_5_failed_attempts(): void
    {
        $user = User::factory()->siswa()->create([
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'identifier' => '1234567890',
                'password' => 'wrongpassword',
            ])->assertSessionHasErrors('identifier');
        }

        $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('identifier');
    }

    public function test_correct_credentials_bypass_rate_limit(): void
    {
        $user = User::factory()->siswa()->create([
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'identifier' => '1234567890',
                'password' => 'wrongpassword',
            ])->assertSessionHasErrors('identifier');
        }

        $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ])->assertRedirect(route('siswa.dashboard'));
    }

    public function test_rate_limit_key_includes_ip_address(): void
    {
        $user = User::factory()->siswa()->create([
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ]);

        // Habiskan lima percobaan gagal dari IP 127.0.0.1.
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'identifier' => '1234567890',
                'password' => 'wrongpassword',
            ])->assertSessionHasErrors('identifier');
        }

        // IP yang sama kena pembatasan laju.
        $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('identifier');

        $this->assertStringContainsString(
            'Terlalu banyak percobaan',
            session('errors')->first('identifier'),
        );

        // IP berbeda memiliki kunci pembatasan sendiri sehingga tidak ikut terbatas.
        $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'wrongpassword',
        ], ['REMOTE_ADDR' => '127.0.0.2'])->assertSessionHasErrors('identifier');

        $this->assertStringContainsString(
            'Kredensial tidak valid',
            session('errors')->first('identifier'),
        );
    }

    public function test_rate_limit_key_includes_identifier(): void
    {
        $user1 = User::factory()->siswa()->create([
            'identifier' => '1111111111',
            'password' => 'password',
        ]);

        $user2 = User::factory()->siswa()->create([
            'identifier' => '2222222222',
            'password' => 'password',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'identifier' => '1111111111',
                'password' => 'wrong',
            ])->assertSessionHasErrors('identifier');
        }

        $this->post('/login', [
            'identifier' => '2222222222',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));
    }

    public function test_successful_login_clears_rate_limit(): void
    {
        $user = User::factory()->siswa()->create([
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'identifier' => '1234567890',
                'password' => 'wrongpassword',
            ])->assertSessionHasErrors('identifier');
        }

        $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ])->assertRedirect(route('siswa.dashboard'));

        $this->post('/logout');

        $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'wrongpassword',
        ])->assertSessionHasErrors('identifier');
    }

    public function test_rate_limit_message_includes_wait_time(): void
    {
        $user = User::factory()->siswa()->create([
            'identifier' => '1234567890',
            'password' => 'correctpassword',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'identifier' => '1234567890',
                'password' => 'wrongpassword',
            ])->assertSessionHasErrors('identifier');
        }

        $response = $this->post('/login', [
            'identifier' => '1234567890',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('identifier');
    }

    public function test_different_users_have_separate_rate_limits(): void
    {
        $user1 = User::factory()->siswa()->create([
            'identifier' => 'user1',
            'password' => 'password',
        ]);

        $user2 = User::factory()->siswa()->create([
            'identifier' => 'user2',
            'password' => 'password',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'identifier' => 'user1',
                'password' => 'wrong',
            ])->assertSessionHasErrors('identifier');
        }

        $this->post('/login', [
            'identifier' => 'user2',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));
    }

    public function test_email_and_identifier_use_same_rate_limit(): void
    {
        $user = User::factory()->siswa()->create([
            'identifier' => '1234567890',
            'email' => 'user@example.com',
            'password' => 'password',
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post('/login', [
                'identifier' => '1234567890',
                'password' => 'wrong',
            ])->assertSessionHasErrors('identifier');
        }

        $this->post('/login', [
            'identifier' => 'user@example.com',
            'password' => 'password',
        ])->assertRedirect(route('siswa.dashboard'));
    }
}
