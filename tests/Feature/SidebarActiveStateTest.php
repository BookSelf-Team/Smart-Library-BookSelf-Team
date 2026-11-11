<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SidebarActiveStateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array{0: string, 1: string}  ...$pages  pasangan [path halaman, path menu yang harus aktif].
     */
    private function assertEachPageLightsExactlyOneMenu(User $user, array $pages): void
    {
        foreach ($pages as [$pagePath, $expectedActivePath]) {
            $response = $this->actingAs($user)->get($pagePath);

            $response->assertOk();
            $this->assertExactlyOneActiveNavItem($response, $expectedActivePath);
        }
    }

    /**
     * @return string[] daftar path dari seluruh tautan menu yang ditandai aktif.
     */
    private function activeNavPaths(TestResponse $response): array
    {
        preg_match_all('/<a\b([^>]*aria-current="page"[^>]*)>/i', $response->getContent(), $anchors);

        $paths = [];
        foreach ($anchors[1] as $attributes) {
            if (preg_match('/href="([^"]+)"/i', $attributes, $href)) {
                $paths[] = rtrim((string) parse_url($href[1], PHP_URL_PATH), '/');
            }
        }

        return $paths;
    }

    private function assertExactlyOneActiveNavItem(TestResponse $response, string $expectedActivePath): void
    {
        $paths = $this->activeNavPaths($response);
        $distinct = array_values(array_unique($paths));

        $this->assertNotEmpty($paths, 'Sidebar harus menandai satu menu aktif.');
        $this->assertCount(1, $distinct, sprintf(
            'Harus tepat satu menu yang aktif, tetapi yang aktif: [%s] (diharapkan hanya %s).',
            implode(', ', $distinct) ?: 'tidak ada',
            $expectedActivePath,
        ));
        $this->assertSame('/'.ltrim($expectedActivePath, '/'), $distinct[0]);
    }

    public function test_admin_sidebar_lights_exactly_one_menu_per_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertEachPageLightsExactlyOneMenu($admin, [
            ['admin/dashboard', 'admin/dashboard'],
            ['admin/buku', 'admin/buku'],
            ['admin/buku/tambah', 'admin/buku/tambah'],
            ['admin/peminjaman', 'admin/peminjaman'],
            ['admin/pengembalian', 'admin/pengembalian'],
            ['admin/users', 'admin/users'],
            ['admin/pengaturan', 'admin/pengaturan'],
            ['admin/petugas', 'admin/petugas'],
            ['katalog', 'katalog'],
        ]);
    }

    public function test_petugas_sidebar_lights_exactly_one_menu_per_page(): void
    {
        $petugas = User::factory()->petugas()->create();

        $this->assertEachPageLightsExactlyOneMenu($petugas, [
            ['petugas/dashboard', 'petugas/dashboard'],
            ['admin/buku', 'admin/buku'],
            ['admin/peminjaman', 'admin/peminjaman'],
            ['admin/pengembalian', 'admin/pengembalian'],
            ['katalog', 'katalog'],
        ]);
    }

    public function test_siswa_sidebar_lights_exactly_one_menu_per_page(): void
    {
        $siswa = User::factory()->siswa()->create();

        $this->assertEachPageLightsExactlyOneMenu($siswa, [
            ['siswa/dashboard', 'siswa/dashboard'],
            ['katalog', 'katalog'],
            ['siswa/pinjaman', 'siswa/pinjaman'],
            ['siswa/riwayat', 'siswa/riwayat'],
            ['siswa/kartu', 'siswa/kartu'],
            ['siswa/profil', 'siswa/profil'],
        ]);
    }

    public function test_tambah_buku_page_only_lights_tambah_buku_menu_not_data_buku(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.buku.tambah'));

        $response->assertOk();

        $paths = $this->activeNavPaths($response);

        $this->assertContains('/admin/buku/tambah', $paths);
        $this->assertNotContains('/admin/buku', $paths);
    }
}
