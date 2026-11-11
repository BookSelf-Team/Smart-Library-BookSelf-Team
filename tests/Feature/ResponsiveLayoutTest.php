<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsiveLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_responsive_viewport_and_mobile_classes(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('name="viewport"', false);
        $response->assertSee('width=device-width, initial-scale=1.0', false);
        $response->assertSee('px-4 sm:px-6 lg:px-margin', false);
        $response->assertSee('flex flex-wrap items-center', false);
    }

    public function test_admin_pages_render_responsive_mobile_drawer_and_desktop_sidebar(): void
    {
        $admin = User::factory()->admin()->create();

        $routes = [
            route('admin.dashboard'),
            route('admin.buku.index'),
            route('admin.buku.tambah'),
            route('admin.peminjaman'),
            route('admin.pengembalian'),
            route('admin.users.index'),
            route('admin.petugas'),
            route('admin.pengaturan'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($admin)->get($url);

            $response->assertOk();
            $response->assertSee('name="viewport"', false);
            $response->assertSee('width=device-width, initial-scale=1.0', false);
            $response->assertSee('adminMobileMenuBtn', false);
            $response->assertSee('adminMobileSidebar', false);
            $response->assertSee('hidden lg:flex', false);
            $response->assertSee('lg:pl-72', false);
        }
    }

    public function test_siswa_pages_render_responsive_mobile_drawer_and_desktop_sidebar(): void
    {
        $siswa = User::factory()->siswa()->create();

        $routes = [
            route('siswa.dashboard'),
            route('siswa.katalog'),
            route('siswa.pinjaman'),
            route('siswa.riwayat'),
            route('siswa.kartu'),
            route('siswa.profil'),
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($siswa)->get($url);

            $response->assertOk();
            $response->assertSee('name="viewport"', false);
            $response->assertSee('width=device-width, initial-scale=1.0', false);
            $response->assertSee('mobileMenuBtn', false);
            $response->assertSee('mobileSidebar', false);
            $response->assertSee('hidden lg:flex', false);
            $response->assertSee('lg:pl-72', false);
        }
    }

    public function test_table_views_have_overflow_x_auto_for_mobile_and_tablet_scrolling(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => now()->subDays(2),
            'batas_kembali' => now()->addDays(5),
            'status' => 'ACTIVE',
        ]);

        // Admin table pages
        $adminTableUrls = [
            route('admin.buku.index'),
            route('admin.peminjaman'),
            route('admin.pengembalian'),
            route('admin.users.index'),
            route('admin.dashboard'),
        ];

        foreach ($adminTableUrls as $url) {
            $response = $this->actingAs($admin)->get($url);
            $response->assertOk();
            $response->assertSee('overflow-x-auto', false);
        }

        // Siswa table pages
        $siswaTableUrls = [
            route('siswa.dashboard'),
            route('siswa.pinjaman'),
            route('siswa.riwayat'),
        ];

        foreach ($siswaTableUrls as $url) {
            $response = $this->actingAs($siswa)->get($url);
            $response->assertOk();
            $response->assertSee('overflow-x-auto', false);
        }
    }

    public function test_staff_sees_admin_sidebar_and_no_member_links_on_katalog_page(): void
    {
        $admin = User::factory()->admin()->create();
        $petugas = User::factory()->petugas()->create();
        Book::factory()->create(['jumlah_stok' => 3]);

        foreach ([$admin, $petugas] as $user) {
            $response = $this->actingAs($user)->get(route('siswa.katalog'));

            $response->assertOk();
            $response->assertSee('adminMobileMenuBtn', false);
            $response->assertSee('href="'.url('admin/buku').'"', false);
            // Menu siswa tidak boleh muncul bagi staf: tautannya hanya 403.
            $response->assertDontSee('Pinjaman Saya', false);
            $response->assertDontSee('href="'.url('siswa/dashboard').'"', false);
            $response->assertDontSee('Pinjamkan Kilat', false);
        }
    }

    public function test_member_sees_siswa_sidebar_and_quick_borrow_on_katalog_page(): void
    {
        $siswa = User::factory()->siswa()->create();
        Book::factory()->create(['jumlah_stok' => 3]);

        $response = $this->actingAs($siswa)->get(route('siswa.katalog'));

        $response->assertOk();
        $response->assertSee('mobileMenuBtn', false);
        $response->assertDontSee('adminMobileMenuBtn', false);
        $response->assertSee('href="'.url('siswa/pinjaman').'"', false);
        $response->assertSee('Pinjamkan Kilat', false);
    }

    public function test_pages_render_motion_animations_with_reduced_motion_support(): void
    {
        $petugas = User::factory()->petugas()->create();
        $siswa = User::factory()->siswa()->create();
        Book::factory()->create(['jumlah_stok' => 3]);

        // Semua layout memuat partial animasi dan menghormati preferensi
        // pengguna yang meminta pengurangan gerak (prefers-reduced-motion).
        $response = $this->actingAs($siswa)->get(route('siswa.katalog'));

        $response->assertOk();
        $response->assertSee('prefers-reduced-motion: no-preference', false);
        $response->assertSee('@keyframes page-in', false);

        // Meja sirkulasi petugas: statistik berurutan, kartu anggota pop-in,
        // notifikasi flash meluncur masuk, ikon empty state mengambang.
        $response = $this->actingAs($petugas)->withSession([
            'success' => 'Buku berhasil dikembalikan.',
        ])->get(route('petugas.dashboard', ['member' => $siswa->id]));

        $response->assertOk();
        $response->assertSee('xl:grid-cols-4 gap-4 sm:gap-5 stagger-in', false);
        $response->assertSee('min-h-[280px] pop-in', false);
        $response->assertSee('gap-2 flash-in', false);
        $response->assertSee('text-outline float-soft', false);

        // Katalog: kartu buku muncul berurutan dan terangkat saat hover.
        $response = $this->actingAs($siswa)->get(route('siswa.katalog'));

        $response->assertOk();
        $response->assertSee('2xl:grid-cols-4 gap-4 sm:gap-5 stagger-in', false);
        $response->assertSee('hover:-translate-y-1', false);
    }
}
