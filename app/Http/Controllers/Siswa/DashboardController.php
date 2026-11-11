<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        // Hanya akun anggota/pemustaka yang boleh membuka halaman ini.
        abort_unless($user->isMember(), 403, 'Halaman ini khusus anggota perpustakaan.');

        $siswa = [
            'nama' => $user->name,
            'nisn' => $user->identifier,
            'kelas' => $user->kelas ?? 'XI - MIPA 2',   // ganti jika ada kolom/relasi kelas
        ];

        $loans = Loan::query()
            ->with('book')
            ->where('user_id', $user->getKey())
            ->whereNull('tanggal_dikembalikan')
            ->orderBy('batas_kembali')
            ->get()
            ->map(fn (Loan $loan): array => [
                'id' => $loan->getKey(),
                'title' => $loan->book?->judul ?? 'Buku tidak diketahui',
                'author' => $loan->book?->pengarang ?? '-',
                'isbn' => $loan->book?->kode_buku ?? '-',
                'tgl_pinjam' => $loan->tanggal_pinjam,
                'jatuh_tempo' => $loan->batas_kembali,
            ]);

        $dibaca = Loan::query()
            ->where('user_id', $user->getKey())
            ->whereNotNull('tanggal_dikembalikan')
            ->count();

        // Hitung ringkasan dari data pinjaman
        $today = Carbon::today();
        $sisaHari = fn ($l) => (int) $today->diffInDays($l['jatuh_tempo']->copy()->startOfDay(), false);
        $terdekat = $loans->sortBy(fn ($l) => $sisaHari($l))->first();

        $dendaPerHari = Setting::dendaPerHari();
        $denda = $loans->sum(fn ($l) => max(0, -$sisaHari($l)) * $dendaPerHari);

        $stats = [
            'aktif' => $loans->count(),
            'kuota' => 3,
            'terdekat_hari' => $terdekat ? $sisaHari($terdekat) : null,
            'terdekat_judul' => $terdekat['title'] ?? null,
            'denda' => $denda,
            'dibaca' => $dibaca,
            'target' => 24,
        ];

        $notifications = [];
        foreach ($loans as $l) {
            $d = $sisaHari($l);
            if ($d < 0) {
                $notifications[] = ['icon' => 'error', 'tone' => 'danger', 'title' => 'Buku terlambat',
                    'text' => "{$l['title']} terlambat ".abs($d).' hari. Segera kembalikan.'];
            } elseif ($d <= 1) {
                $notifications[] = ['icon' => 'schedule', 'tone' => 'warn', 'title' => 'Segera jatuh tempo',
                    'text' => "{$l['title']} harus dikembalikan ".($d === 0 ? 'hari ini' : 'besok').'.'];
            }
        }

        $recommendations = [
            ['id' => 1, 'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuD84yGF0R1QsoYfxz0mYgRXEXcGd_8EqyXASfN6K90zXJ5Z-R_BLkAaUXavB0GqJV1QoWZrWCVEBsgv10yzupt4WV4gKX51JFOMRy1gsw1RhffK5sdYeOKRhtZIp7pWQN1FY9qfDld9jEMVjN8NzEYr9R0A8zd0hq1mM4_U64-sBd9Iq0Ar6jKYccQ1s_rY0_EFr1zcDx7vn87gH9Oz4kuRfonz8W6cmeaucoKvn256zkjB8-QHt0sC', 'title' => 'Pulang',                   'author' => 'Tere Liye',      'kategori' => 'Fiksi Sastra', 'stok' => 6],
            ['id' => 2, 'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDVxOSNSMMi7YU6FPqkSo0cF5KqINyuXigImtsLXUrGFMOIIBDLPt9Lvub5YoECX0w6dxZIn9MbKoF1_2pMMyS-1kgjoAw2IVTfXrDhLvzOFF5Z3AVOKl4_4GqYFq0BOXt20TQV_XpfUrSDB3npYsjbFN-9-6SQhfyiYehFZy5lDpu3lgxRGVMs2j-TaruFwBo9DRq69KbJlZR7xd6Ww9h1yR3wmhCqBDZ484mBOUIH6rIJtbW2lHLx', 'title' => 'Kosmos',                   'author' => 'Carl Sagan',     'kategori' => 'Sains & Fisika', 'stok' => 1],
            ['id' => 3, 'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBG60bbIeATraRGMZj5UAVUVjyx1JefgeIj1IGEHfyzH7_8BEUlQE3modfl36UN23q1Sjg7uSaAoLiJ4Yc9B_5HguttW69HM23RLsMG-0Hht6bFkHtblHG0Ad-4tMpXGjsA2_mFU3XK5OZE1OQv1Wv6RtfdtI5F4QBCm3NBb1LZxBejyu7QJZCZoSKHyxL9vSpWFQwzKq8AnADFBMgmnNZJCshwKSVs46cfax_gd5yeCgnWX3nM1osD', 'title' => 'Nusantara: Sejarah Indonesia', 'author' => 'Bernard H. M. Vlekke', 'kategori' => 'Sejarah', 'stok' => 4],
            ['id' => 4, 'cover' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB02E1m4HK_VbHwVVwE-saAPyXrFVOlLIPovKJ9cXV2m45WSMroxk-f71hO3FCplm0CZzHDEC8FseXzQjvzLwFL9cmQImAsjPJEDXfLYgQUqlARR1wgvkL7mwqpM8pxOuy4LOMv2Wrz2g5fuOChOR3VeptZrByTM_vIXw4yn04eWbrz5Kp0712j3YD3b0EdTJIXdXVI6gYJLpKNcgYRf4iwaviNBVkBSTdK7wO7pgzf8RU32JcyJukz', 'title' => 'Kecerdasan Buatan',        'author' => 'Stuart Russell', 'kategori' => 'Teknologi', 'stok' => 2],
        ];

        return view('siswa.dashboard', compact('siswa', 'loans', 'stats', 'notifications', 'recommendations', 'dendaPerHari'));
    }
}
