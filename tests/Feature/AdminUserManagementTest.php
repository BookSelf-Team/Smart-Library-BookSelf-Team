<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function roleBadgeColorProvider(): array
    {
        return [
            'admin' => ['admin', 'bg-blue-100'],
            'pustakawan' => ['pustakawan', 'bg-violet-100'],
            'petugas' => ['petugas', 'bg-amber-100'],
            'guru' => ['guru', 'bg-teal-100'],
            'siswa' => ['siswa', 'bg-rose-100'],
        ];
    }

    #[DataProvider('roleBadgeColorProvider')]
    public function test_user_management_table_renders_a_distinct_badge_color_for_each_role(string $role, string $badgeColor): void
    {
        $admin = User::factory()->admin()->create();

        User::factory()->{$role}()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee($badgeColor, false);
    }

    public function test_admin_sees_edit_and_delete_actions_for_other_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->siswa()->create(['name' => 'Rani Melati']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('aria-label="Edit pengguna Rani Melati"', false)
            ->assertSee('aria-label="Hapus pengguna Rani Melati"', false);
    }

    public function test_last_admin_row_renders_as_protected_without_edit_or_delete_actions(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Tunggal']);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Dilindungi', false)
            ->assertDontSee('aria-label="Edit pengguna Admin Tunggal"', false)
            ->assertDontSee('aria-label="Hapus pengguna Admin Tunggal"', false);
    }

    public function test_user_row_renders_identifier_below_the_name(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->siswa()->create([
            'name' => 'Rani Melati',
            'identifier' => '2021094',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('2021094');
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function roleIdentifierPrefixProvider(): array
    {
        return [
            'admin' => ['admin', 'ADM'],
            'pustakawan' => ['pustakawan', 'PST'],
            'petugas' => ['petugas', 'PTG'],
            'guru' => ['guru', 'GRU'],
            'siswa' => ['siswa', 'SIS'],
        ];
    }

    #[DataProvider('roleIdentifierPrefixProvider')]
    public function test_identifier_is_generated_automatically_with_the_role_prefix(string $role, string $prefix): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Pengguna Baru',
            'email' => "baru-{$role}@example.com",
            'role' => $role,
            'password' => 'password123',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'email' => "baru-{$role}@example.com",
            'identifier' => $prefix.'-000001',
        ]);
    }

    public function test_generated_identifiers_are_sequential_per_role(): void
    {
        $admin = User::factory()->admin()->create();

        collect(['pertama', 'kedua'])->each(function (string $suffix) use ($admin): void {
            $this->actingAs($admin)->post(route('admin.users.store'), [
                'name' => "Siswa {$suffix}",
                'email' => "siswa-{$suffix}@example.com",
                'role' => 'siswa',
                'password' => 'password123',
            ])->assertRedirect(route('admin.users.index'));
        });

        $this->assertDatabaseHas('users', ['email' => 'siswa-pertama@example.com', 'identifier' => 'SIS-000001']);
        $this->assertDatabaseHas('users', ['email' => 'siswa-kedua@example.com', 'identifier' => 'SIS-000002']);
    }

    public function test_update_with_blank_identifier_keeps_the_current_identifier(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create(['identifier' => 'SIS-000123']);

        $this->actingAs($admin)->put(route('admin.users.update', $siswa), [
            'name' => $siswa->name,
            'identifier' => '',
            'email' => $siswa->email,
            'role' => 'siswa',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertSame('SIS-000123', $siswa->fresh()->identifier);
    }

    public function test_updating_legacy_user_without_identifier_generates_one(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create(['identifier' => null]);

        $this->actingAs($admin)->put(route('admin.users.update', $siswa), [
            'name' => $siswa->name,
            'identifier' => '',
            'email' => $siswa->email,
            'role' => 'siswa',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertMatchesRegularExpression('/^SIS-\d{6}$/', (string) $siswa->fresh()->identifier);
    }
}
