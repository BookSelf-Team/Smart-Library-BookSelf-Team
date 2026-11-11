@extends('layouts.siswa')

@section('title', 'Dashboard Siswa — Bookself')

@section('content')
@php
    $today = \Illuminate\Support\Carbon::now()->locale('id');
    $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $pctKuota = $stats['kuota'] > 0 ? min(100, round($stats['aktif'] / $stats['kuota'] * 100)) : 0;
    $pctBaca  = $stats['target'] > 0 ? min(100, round($stats['dibaca'] / $stats['target'] * 100)) : 0;
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Dashboard Siswa']]" />

    {{-- ================= SAPAAN ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    Kelas {{ $siswa['kelas'] }} • NISN {{ $siswa['nisn'] }}
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, j F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Selamat Datang, <span class="text-primary italic">{{ $siswa['nama'] }}</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Pantau pinjaman, tenggat pengembalian, dan temukan bacaan berikutnya dari koleksi perpustakaan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url('katalog') }}"
               class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)] active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                <span>Jelajahi Katalog</span>
            </a>
            <a href="{{ url('siswa/kartu') }}"
               class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">badge</span>
                <span>Kartu Anggota</span>
            </a>
        </div>
    </div>

    {{-- ================= KARTU STATISTIK ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 stagger-in">

        {{-- Pinjaman aktif --}}
        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Pinjaman Aktif</span>
                    <p class="font-headline-lg text-headline-lg text-on-surface mt-1">{{ $stats['aktif'] }}
                        <span class="font-body-sm text-body-sm text-on-surface-variant">dari {{ $stats['kuota'] }} buku</span></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[22px]">bookmark_added</span>
                </div>
            </div>
            <div>
                <div class="h-1.5 rounded-full bg-surface-container-high"><div class="h-1.5 rounded-full bg-primary" style="width: {{ $pctKuota }}%"></div></div>
                <p class="font-label-sm text-label-sm text-on-surface-variant mt-1.5">Kuota pinjam terpakai {{ $pctKuota }}%</p>
            </div>
        </div>

        {{-- Jatuh tempo terdekat --}}
        @php
            $d = $stats['terdekat_hari'];
            $dueTone = $d === null ? 'text-on-surface' : ($d < 0 ? 'text-error' : ($d <= 1 ? 'text-[#92400E]' : 'text-on-surface'));
        @endphp
        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Jatuh Tempo Terdekat</span>
                    <p class="font-headline-lg text-headline-lg {{ $dueTone }} mt-1">
                        @if ($d === null) — @elseif ($d < 0) Terlambat {{ abs($d) }} hari @elseif ($d === 0) Hari ini @else H-{{ $d }} @endif
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[22px]">event_upcoming</span>
                </div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant line-clamp-1">{{ $stats['terdekat_judul'] ?? 'Tidak ada pinjaman aktif' }}</p>
        </div>

        {{-- Denda berjalan --}}
        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Denda Berjalan</span>
                    <p class="font-headline-lg text-headline-lg {{ $stats['denda'] > 0 ? 'text-error' : 'text-primary' }} mt-1">{{ $rupiah($stats['denda']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg {{ $stats['denda'] > 0 ? 'bg-error-container text-error' : 'bg-secondary-container text-on-secondary-container' }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">{{ $stats['denda'] > 0 ? 'payments' : 'check_circle' }}</span>
                </div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant">
                {{ $stats['denda'] > 0
                    ? $rupiah($dendaPerHari).' per hari per buku. Selesaikan di meja pustakawan.'
                    : ($dendaPerHari > 0
                        ? 'Tidak ada denda. Terima kasih sudah tepat waktu!'
                        : 'Denda keterlambatan sedang tidak diaktifkan.') }}
            </p>
        </div>

        {{-- Buku dibaca (kartu gelap) --}}
        <div class="p-5 rounded-xl bg-primary text-on-primary flex flex-col gap-4 shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)]">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-primary/80">Buku Dibaca Tahun Ini</span>
                    <p class="font-headline-lg text-headline-lg mt-1">{{ $stats['dibaca'] }}
                        <span class="font-body-sm text-body-sm text-on-primary/80">dari target {{ $stats['target'] }}</span></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                </div>
            </div>
            <div>
                <div class="h-1.5 rounded-full bg-white/20"><div class="h-1.5 rounded-full bg-white" style="width: {{ $pctBaca }}%"></div></div>
                <p class="font-label-sm text-label-sm text-on-primary/80 mt-1.5">Target literasi tercapai {{ $pctBaca }}%</p>
            </div>
        </div>
    </div>

    {{-- ================= PINJAMAN SAYA + KARTU ANGGOTA ================= --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        {{-- Tabel pinjaman --}}
        <div class="xl:col-span-8 p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-5">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Pinjaman Saya</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Buku yang sedang Anda pinjam beserta tenggat pengembaliannya.</p>
                </div>
                <a href="{{ url('siswa/riwayat') }}" class="font-label-md text-label-md text-primary font-semibold hover:underline">Lihat riwayat</a>
            </div>

            <div class="rounded-xl bg-surface-container-lowest overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                            <th class="px-4 py-3">Judul Buku</th>
                            <th class="px-4 py-3">Dipinjam</th>
                            <th class="px-4 py-3">Jatuh Tempo</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-sm text-body-sm">
                        @forelse ($loans as $loan)
                            @php
                                $sisa = (int) \Illuminate\Support\Carbon::today()->diffInDays($loan['jatuh_tempo']->copy()->startOfDay(), false);
                                if ($sisa < 0)      { $badge = ['bg-error-container text-on-error-container', 'Terlambat '.abs($sisa).' hari']; }
                                elseif ($sisa <= 1) { $badge = ['bg-[#FEF3C7] text-[#92400E]', $sisa === 0 ? 'Jatuh tempo hari ini' : 'H-1']; }
                                else                { $badge = ['bg-secondary-container text-on-secondary-container', 'Aman • H-'.$sisa]; }
                            @endphp
                            <tr class="border-t border-surface-container">
                                <td class="px-4 py-3">
                                    <p class="font-title-sm text-title-sm text-on-surface">{{ $loan['title'] }}</p>
                                    <p class="text-on-surface-variant">{{ $loan['author'] }} • ISBN {{ $loan['isbn'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap">{{ $loan['tgl_pinjam']->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap font-mono text-[12px] {{ $sisa < 0 ? 'text-error font-semibold' : 'text-on-surface' }}">{{ $loan['jatuh_tempo']->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold whitespace-nowrap {{ $badge[0] }}">{{ $badge[1] }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if ($sisa < 0)
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">Kembalikan ke petugas</span>
                                    @else
                                        <form method="POST" action="{{ url('siswa/pinjaman/'.$loan['id'].'/perpanjang') }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 rounded-lg border border-outline-variant text-on-surface font-label-md text-label-md hover:bg-surface-container transition-colors whitespace-nowrap">Perpanjang +7 Hari</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[36px] text-outline">auto_stories</span>
                                    <p class="mt-1">Belum ada pinjaman aktif. Yuk, cari buku di katalog!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Kartu anggota + pengingat --}}
        <div class="xl:col-span-4 flex flex-col gap-6">
            <div class="p-5 sm:p-6 rounded-2xl bg-primary text-on-primary shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)] flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase tracking-widest text-on-primary/80">Kartu Anggota Digital</span>
                    <span class="material-symbols-outlined text-[22px]">contactless</span>
                </div>
                <div>
                    <p class="font-headline-sm text-headline-sm">{{ $siswa['nama'] }}</p>
                    <p class="font-label-md text-label-md text-on-primary/80">Kelas {{ $siswa['kelas'] }}</p>
                </div>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">NISN</p>
                        <p class="font-mono text-[15px] tracking-wider">{{ $siswa['nisn'] }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Kuota Pinjam</p>
                        <p class="font-title-sm text-title-sm">{{ $stats['aktif'] }} / {{ $stats['kuota'] }}</p>
                    </div>
                </div>
                <div class="h-10 rounded bg-white/90" style="background-image: repeating-linear-gradient(90deg, #1F2915 0 2px, transparent 2px 5px, #1F2915 5px 6px, transparent 6px 10px);"></div>
                <p class="font-label-sm text-label-sm text-on-primary/80 text-center">Tunjukkan kartu ini ke petugas atau tempelkan di pembaca RFID.</p>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-low flex flex-col gap-3">
                <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">notifications_active</span> Pengingat
                </h3>
                @forelse ($notifications as $n)
                    <div class="flex items-start gap-3 p-3 rounded-lg bg-surface-container-lowest">
                        <span class="material-symbols-outlined text-[20px] {{ $n['tone'] === 'danger' ? 'text-error' : ($n['tone'] === 'warn' ? 'text-[#92400E]' : 'text-primary') }}">{{ $n['icon'] }}</span>
                        <div>
                            <p class="font-title-sm text-title-sm text-on-surface">{{ $n['title'] }}</p>
                            <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $n['text'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Tidak ada pengingat saat ini.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ================= REKOMENDASI ================= --}}
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">local_fire_department</span> Rekomendasi & Koleksi Populer
                </h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">Buku yang sering dipinjam teman-teman sekolahmu.</p>
            </div>
            <a href="{{ url('katalog') }}" class="font-label-md text-label-md text-primary font-semibold hover:underline flex items-center gap-1">
                Lihat Semua Katalog <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 stagger-in">
            @foreach ($recommendations as $b)
                @php
                    $habis = $b['stok'] <= 0;
                    $tipis = !$habis && $b['stok'] <= 2;
                @endphp
                <a href="{{ url('katalog/'.$b['id']) }}" class="group p-4 rounded-xl bg-surface-container-low hover:bg-surface-container flex gap-4 transition-colors">
                    <div class="w-20 h-28 shrink-0 rounded-md overflow-hidden bg-surface-container-high shadow-sm">
                        @if (!empty($b['cover']))
                            <img src="{{ $b['cover'] }}" alt="Sampul {{ $b['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-outline"><span class="material-symbols-outlined">menu_book</span></div>
                        @endif
                    </div>
                    <div class="flex flex-col justify-between min-w-0">
                        <div>
                            <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">{{ $b['kategori'] }}</span>
                            <h3 class="font-title-md text-title-md text-on-surface line-clamp-2 leading-snug">{{ $b['title'] }}</h3>
                            <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1">{{ $b['author'] }}</p>
                        </div>
                        <span class="inline-flex w-fit px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold
                                     {{ $habis ? 'bg-error-container text-on-error-container' : ($tipis ? 'bg-[#FEF3C7] text-[#92400E]' : 'bg-secondary-container text-on-secondary-container') }}">
                            {{ $habis ? 'Habis' : ($tipis ? 'Sisa '.$b['stok'].' Eks' : 'Tersedia • '.$b['stok'].' Eks') }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
