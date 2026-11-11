<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use App\Services\FileUploadSanitizer;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SecurityAndPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_performance_indexes_exist(): void
    {
        $loanIndexes = collect(Schema::getIndexes('loans'))->pluck('name')->all();
        $this->assertContains('loans_book_id_index', $loanIndexes);
        $this->assertContains('loans_user_id_index', $loanIndexes);
        $this->assertContains('loans_user_id_status_index', $loanIndexes);
        $this->assertContains('loans_status_batas_kembali_index', $loanIndexes);
        $this->assertContains('loans_tanggal_dikembalikan_index', $loanIndexes);

        $fineIndexes = collect(Schema::getIndexes('fines'))->pluck('name')->all();
        $this->assertContains('fines_status_pembayaran_index', $fineIndexes);

        $bookIndexes = collect(Schema::getIndexes('books'))->pluck('name')->all();
        $this->assertContains('books_judul_index', $bookIndexes);
        $this->assertContains('books_pengarang_index', $bookIndexes);
        $this->assertContains('books_jumlah_stok_index', $bookIndexes);

        $userIndexes = collect(Schema::getIndexes('users'))->pluck('name')->all();
        $this->assertContains('users_role_index', $userIndexes);
    }

    public function test_sql_injection_payloads_in_search_are_parameterized_and_safe(): void
    {
        $admin = User::factory()->admin()->create();
        Book::factory()->create([
            'judul' => 'Pemrograman Modern dengan PHP',
            'pengarang' => 'Budi Santoso',
            'kode_buku' => 'BKS-9001',
        ]);

        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE books; --",
            "admin'--",
            "' UNION SELECT 1, 'injected', 'author', 2024, 5, 'sinopsis', null, null, null --",
        ];

        foreach ($payloads as $payload) {
            $response = $this->actingAs($admin)->get(route('admin.buku.index', ['q' => $payload]));

            $response->assertOk();
            $this->assertTrue(Schema::hasTable('books'));
            $response->assertSee('Data Buku');
        }

        // Verifikasi pada endpoint katalog siswa
        $siswa = User::factory()->siswa()->create();
        foreach ($payloads as $payload) {
            $response = $this->actingAs($siswa)->get(route('siswa.katalog', ['q' => $payload]));
            $response->assertOk();
        }
    }

    public function test_xss_payloads_in_user_and_book_data_are_properly_escaped(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => '<script>alert("admin-xss")</script>',
        ]);

        $book = Book::factory()->create([
            'judul' => '<script>alert("book-xss")</script>',
            'pengarang' => '<img src=x onerror=alert(1)>',
            'sinopsis' => '<b>Sinopsis Tebal</b>',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.buku.index'));
        $response->assertOk();

        // Harus di-escape oleh Blade: &lt;script&gt; bukan <script>
        $response->assertSee('&lt;script&gt;alert(&quot;book-xss&quot;)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert("book-xss")</script>', false);
        $response->assertSee('&lt;img src=x onerror=alert(1)&gt;', false);
        $response->assertDontSee('<img src=x onerror=alert(1)>', false);

        // Edit button data attributes harus safe
        $response->assertSee('data-judul="&lt;script&gt;alert(&quot;book-xss&quot;)&lt;/script&gt;"', false);
    }

    public function test_csrf_token_directive_is_present_on_all_modifying_forms(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        // Form Login
        $loginRes = $this->get(route('login'));
        $loginRes->assertOk();
        $loginRes->assertSee('name="_token"', false);

        // Form Tambah Buku
        $tambahBukuRes = $this->actingAs($admin)->get(route('admin.buku.tambah'));
        $tambahBukuRes->assertOk();
        $tambahBukuRes->assertSee('name="_token"', false);

        // Form Profil Siswa
        $profilRes = $this->actingAs($siswa)->get(route('siswa.profil'));
        $profilRes->assertOk();
        $profilRes->assertSee('name="_token"', false);
    }

    public function test_file_upload_sanitizer_accepts_valid_image_and_generates_random_hash_filename(): void
    {
        Storage::fake('public');
        $sanitizer = app(FileUploadSanitizer::class);

        $fakeImage = UploadedFile::fake()->image('my_avatar_photo.png', 100, 100);

        $storedPath = $sanitizer->sanitizeAndStoreImage($fakeImage, 'avatars', 'public');

        $this->assertStringStartsWith('avatars/', $storedPath);
        $this->assertStringEndsWith('.png', $storedPath);
        $this->assertStringNotContainsString('my_avatar_photo', $storedPath);
        Storage::disk('public')->assertExists($storedPath);
    }

    public function test_file_upload_sanitizer_blocks_dangerous_executable_extensions(): void
    {
        $sanitizer = app(FileUploadSanitizer::class);

        $badFiles = [
            UploadedFile::fake()->create('shell.php', 10, 'application/x-php'),
            UploadedFile::fake()->create('hack.phtml', 10, 'text/plain'),
            UploadedFile::fake()->create('payload.exe', 50, 'application/octet-stream'),
            UploadedFile::fake()->create('script.sh', 10, 'application/x-sh'),
            UploadedFile::fake()->create('vector.svg', 10, 'image/svg+xml'),
        ];

        foreach ($badFiles as $badFile) {
            try {
                $sanitizer->sanitizeAndStoreImage($badFile);
                $this->fail("File {$badFile->getClientOriginalName()} seharusnya ditolak oleh sanitizer.");
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
        }
    }

    public function test_file_upload_sanitizer_blocks_polyglot_and_embedded_php_code(): void
    {
        $sanitizer = app(FileUploadSanitizer::class);

        // Buat file fake dengan header gambar tapi disusupi payload PHP
        $tempPath = tempnam(sys_get_temp_dir(), 'test_img');
        file_put_contents($tempPath, "\xFF\xD8\xFF\xE0\x00\x10JFIF\x00<?php system(\$_GET['cmd']); ?>");

        $polyglot = new UploadedFile(
            $tempPath,
            'innocent.jpg',
            'image/jpeg',
            null,
            true
        );

        $this->expectException(ValidationException::class);
        $sanitizer->sanitizeAndStoreImage($polyglot);

        @unlink($tempPath);
    }

    public function test_file_upload_sanitizer_neutralizes_directory_traversal_filenames(): void
    {
        $sanitizer = app(FileUploadSanitizer::class);

        $dirty1 = '../../etc/passwd';
        $clean1 = $sanitizer->sanitizeOriginalFilename($dirty1);
        $this->assertEquals('passwd', $clean1);

        $dirty2 = "..\\..\\windows\\system32\\config.sys\0.jpg";
        $clean2 = $sanitizer->sanitizeOriginalFilename($dirty2);
        $this->assertStringNotContainsString('..', $clean2);
        $this->assertStringNotContainsString("\0", $clean2);
        $this->assertStringNotContainsString('/', $clean2);
        $this->assertStringNotContainsString('\\', $clean2);
    }

    public function test_breadcrumbs_render_on_admin_and_siswa_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        // Admin: Data Buku
        $resBuku = $this->actingAs($admin)->get(route('admin.buku.index'));
        $resBuku->assertOk();
        $resBuku->assertSee('aria-label="Breadcrumb"', false);
        $resBuku->assertSee('Koleksi Buku');

        // Admin: Tambah Buku
        $resTambah = $this->actingAs($admin)->get(route('admin.buku.tambah'));
        $resTambah->assertOk();
        $resTambah->assertSee('aria-label="Breadcrumb"', false);
        $resTambah->assertSee('Tambah Buku');

        // Admin: Peminjaman
        $resPinjam = $this->actingAs($admin)->get(route('admin.peminjaman'));
        $resPinjam->assertOk();
        $resPinjam->assertSee('aria-label="Breadcrumb"', false);
        $resPinjam->assertSee('Peminjaman');

        // Admin: Pengembalian
        $resKembali = $this->actingAs($admin)->get(route('admin.pengembalian'));
        $resKembali->assertOk();
        $resKembali->assertSee('aria-label="Breadcrumb"', false);
        $resKembali->assertSee('Pengembalian');

        // Siswa: Dashboard
        $resSiswaDash = $this->actingAs($siswa)->get(route('siswa.dashboard'));
        $resSiswaDash->assertOk();
        $resSiswaDash->assertSee('aria-label="Breadcrumb"', false);
        $resSiswaDash->assertSee('Dashboard Siswa');

        // Siswa: Riwayat
        $resSiswaRiw = $this->actingAs($siswa)->get(route('siswa.riwayat'));
        $resSiswaRiw->assertOk();
        $resSiswaRiw->assertSee('aria-label="Breadcrumb"', false);
        $resSiswaRiw->assertSee('Riwayat &amp; Denda', false);
    }

    public function test_loading_indicator_script_is_included_in_layouts(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();

        $resAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $resAdmin->assertOk();
        $resAdmin->assertSee('animate-spin', false);
        $resAdmin->assertSee('data-loading-text', false);

        $resSiswa = $this->actingAs($siswa)->get(route('siswa.dashboard'));
        $resSiswa->assertOk();
        $resSiswa->assertSee('animate-spin', false);
        $resSiswa->assertSee('data-loading-text', false);
    }

    public function test_no_lazy_loading_violations_occur_on_circulation_and_dashboard_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $siswa = User::factory()->siswa()->create();
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $siswa->id,
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10),
            'batas_kembali' => Carbon::today()->subDays(3),
            'tanggal_dikembalikan' => null,
            'status' => 'ACTIVE',
        ]);

        Fine::factory()->create([
            'loan_id' => $loan->id,
            'nominal' => 3000,
            'status_pembayaran' => 'BELUM',
        ]);

        // Kunjungi halaman yang membaca relasi loans, books, fines
        $this->actingAs($admin)->get(route('admin.peminjaman'))->assertOk();
        $this->actingAs($admin)->get(route('admin.pengembalian'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($siswa)->get(route('siswa.dashboard'))->assertOk();
        $this->actingAs($siswa)->get(route('siswa.pinjaman'))->assertOk();
        $this->actingAs($siswa)->get(route('siswa.riwayat'))->assertOk();
    }
}
