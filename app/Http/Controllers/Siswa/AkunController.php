<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Fine;
use App\Models\Loan;
use App\Services\FileUploadSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkunController extends Controller
{
    private const KUOTA_PINJAM = 3;

    /**
     * Halaman "Kartu Anggota" — kartu digital pemustaka.
     */
    public function kartu(Request $request): View
    {
        $this->authorize('is-member');

        $user = $request->user();

        $siswa = [
            'nama' => $user->name,
            'nisn' => $user->identifier,
            'kelas' => $user->kelas ?? 'XI - MIPA 2',
        ];

        $dipinjam = Loan::query()
            ->where('user_id', $user->getKey())
            ->whereNull('tanggal_dikembalikan')
            ->count();

        $totalPinjam = Loan::query()->where('user_id', $user->getKey())->count();

        $dibaca = Loan::query()
            ->where('user_id', $user->getKey())
            ->whereNotNull('tanggal_dikembalikan')
            ->count();

        $dendaBelum = Fine::query()
            ->whereHas('loan', fn ($query) => $query->where('user_id', $user->getKey()))
            ->whereIn('status_pembayaran', ['BELUM', 'UNPAID'])
            ->sum('nominal');

        // Nomor anggota + masa berlaku masih contoh, ganti sesuai data sekolah.
        $anggota = [
            'nomor' => 'AGT-'.str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
            'bergabung' => Carbon::now()->subYear()->startOfYear(),
            'berlaku_sampai' => Carbon::now()->addYear()->endOfYear(),
            'status' => 'Aktif',
            'kuota' => self::KUOTA_PINJAM,
            'dipinjam' => $dipinjam,
        ];

        $statistik = [
            'total_pinjam' => $totalPinjam,
            'dibaca' => $dibaca,
            'denda_belum' => $dendaBelum,
        ];

        return view('siswa.kartu', compact('siswa', 'anggota', 'statistik'));
    }

    /**
     * Halaman "Profil Saya" — data diri, statistik, dan keamanan akun.
     */
    public function profil(Request $request): View
    {
        $this->authorize('is-member');

        $user = $request->user();

        $siswa = [
            'nama' => $user->name,
            'nisn' => $user->identifier,
            'kelas' => $user->kelas ?? 'XI - MIPA 2',
        ];

        $statistik = [
            'aktif' => Loan::query()
                ->where('user_id', $user->getKey())
                ->whereNull('tanggal_dikembalikan')
                ->count(),
            'total_pinjam' => Loan::query()->where('user_id', $user->getKey())->count(),
            'dibaca' => Loan::query()
                ->where('user_id', $user->getKey())
                ->whereNotNull('tanggal_dikembalikan')
                ->count(),
            'target' => 24,
        ];

        return view('siswa.profil', compact('siswa', 'statistik'));
    }

    /**
     * Simpan perubahan data diri.
     */
    public function update(Request $request, FileUploadSanitizer $sanitizer): RedirectResponse
    {
        $this->authorize('is-member');

        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $sanitizer->sanitizeAndStoreImage($request->file('avatar'), 'avatars');
        }

        $user->update($validated);

        return back()->with('success', 'Data diri berhasil diperbarui.');
    }

    /**
     * Ubah kata sandi akun.
     */
    public function updateSandi(Request $request)
    {
        $this->authorize('is-member');

        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
