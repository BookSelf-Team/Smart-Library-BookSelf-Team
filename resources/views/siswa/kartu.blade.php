@extends('layouts.siswa')

@section('title', 'Kartu Anggota — Bookself')

@push('head')
<style>
    @media print {
        aside, header { display: none !important; }
        .pl-72 { padding-left: 0 !important; }
        main { padding-top: 0 !important; background: #ffffff !important; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
@php
    $today = \Illuminate\Support\Carbon::now()->locale('id');
    $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $pctKuota = $anggota['kuota'] > 0 ? min(100, round($anggota['dipinjam'] / $anggota['kuota'] * 100)) : 0;
    $inisial = collect(explode(' ', $siswa['nama']))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Kartu Anggota Digital']]" />

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6 no-print">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    {{ $anggota['status'] }} • Anggota Perpustakaan
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, j F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Kartu <span class="text-primary italic">Anggota</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Kartu anggota digital Anda. Tunjukkan ke petugas atau tempelkan pada pembaca RFID saat meminjam dan mengembalikan buku.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="window.print()"
                    class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">print</span>
                <span>Cetak Kartu</span>
            </button>
            <a href="{{ url('siswa/profil') }}"
               class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)] active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">person</span>
                <span>Profil Saya</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        {{-- ================= KARTU DIGITAL ================= --}}
        <div class="xl:col-span-7 flex flex-col gap-5">
            <div class="relative overflow-hidden rounded-2xl bg-primary text-on-primary shadow-[0_8px_24px_-8px_rgba(35,45,19,0.45)] p-5 sm:p-7 flex flex-col gap-5 sm:gap-7 min-h-[280px] sm:min-h-[300px]">
                {{-- Ornamen --}}
                <div class="pointer-events-none absolute -top-16 -right-10 w-56 h-56 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-24 -left-12 w-64 h-64 rounded-full bg-white/5"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-white/15 flex items-center justify-center">
                            <span class="material-symbols-outlined text-[22px]">local_library</span>
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span class="font-headline-sm text-headline-sm font-bold tracking-tight">Bookself</span>
                            <span class="font-label-sm text-label-sm text-on-primary/70 uppercase tracking-widest">Smart Library</span>
                        </div>
                    </div>
                    <span class="material-symbols-outlined text-[26px] text-on-primary/80">contactless</span>
                </div>

                <div class="relative">
                    <p class="font-label-sm text-label-sm uppercase tracking-widest text-on-primary/70">Pemustaka</p>
                    <p class="text-xl sm:text-headline-md leading-tight break-words">{{ $siswa['nama'] }}</p>
                    <p class="font-label-md text-label-md text-on-primary/80">Kelas {{ $siswa['kelas'] }} • NISN {{ $siswa['nisn'] }}</p>
                </div>

                <div class="relative grid grid-cols-2 gap-4">
                    <div>
                        <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Nomor Anggota</p>
                        <p class="font-mono text-[16px] tracking-wider">{{ $anggota['nomor'] }}</p>
                    </div>
                    <div>
                        <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Berlaku Sampai</p>
                        <p class="font-mono text-[16px] tracking-wider">{{ $anggota['berlaku_sampai']->translatedFormat('M Y') }}</p>
                    </div>
                </div>

                <div class="relative flex items-end justify-between gap-6">
                    <div class="flex-1">
                        <div class="h-12 rounded bg-white/90" style="background-image: repeating-linear-gradient(90deg, #1F2915 0 2px, transparent 2px 5px, #1F2915 5px 6px, transparent 6px 11px);"></div>
                        <p class="font-mono text-[11px] tracking-[0.3em] text-on-primary/70 mt-1.5">{{ $anggota['nomor'] }}</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Kuota Pinjam</p>
                        <p class="font-title-lg text-title-lg">{{ $anggota['dipinjam'] }} / {{ $anggota['kuota'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Progres kuota --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-low flex flex-col gap-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">donut_small</span> Penggunaan Kuota
                    </h3>
                    <span class="font-label-md text-label-md text-on-surface-variant">{{ $anggota['dipinjam'] }}/{{ $anggota['kuota'] }} buku</span>
                </div>
                <div class="h-2 rounded-full bg-surface-container-high">
                    <div class="h-2 rounded-full bg-primary" style="width: {{ $pctKuota }}%"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="p-2 sm:p-0">
                        <p class="font-headline-sm text-headline-sm text-on-surface">{{ $statistik['total_pinjam'] }}</p>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Total pinjam</p>
                    </div>
                    <div class="p-2 sm:p-0">
                        <p class="font-headline-sm text-headline-sm text-on-surface">{{ $statistik['dibaca'] }}</p>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Buku dibaca</p>
                    </div>
                    <div class="p-2 sm:p-0">
                        <p class="font-headline-sm text-headline-sm {{ $statistik['denda_belum'] > 0 ? 'text-error' : 'text-primary' }}">{{ $rupiah($statistik['denda_belum']) }}</p>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Denda belum lunas</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= SIDEBAR INFORMASI ================= --}}
        <div class="xl:col-span-5 flex flex-col gap-6">

            {{-- Data keanggotaan --}}
            <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-4">
                <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">badge</span> Data Keanggotaan
                </h3>
                <div class="flex flex-col">
                    @foreach ([
                        ['Nama lengkap', $siswa['nama'], 'person'],
                        ['Nomor anggota', $anggota['nomor'], 'tag'],
                        ['NISN', $siswa['nisn'], 'pin'],
                        ['Kelas', $siswa['kelas'], 'school'],
                        ['Terdaftar sejak', $anggota['bergabung']->translatedFormat('d F Y'), 'event_available'],
                        ['Status', $anggota['status'], 'verified'],
                    ] as [$label, $value, $icon])
                        <div class="flex items-center justify-between gap-4 py-3 {{ !$loop->first ? 'border-t border-surface-container' : '' }}">
                            <span class="font-body-sm text-body-sm text-on-surface-variant inline-flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-outline">{{ $icon }}</span>
                                {{ $label }}
                            </span>
                            <span class="font-title-sm text-title-sm text-on-surface text-right max-w-[60%] break-words">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Cara menggunakan --}}
            <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-4">
                <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">help</span> Cara Menggunakan
                </h3>
                <ol class="flex flex-col gap-3 font-body-sm text-body-sm text-on-surface-variant">
                    @foreach ([
                        'Tunjukkan kartu digital ini kepada petugas perpustakaan.',
                        'Untuk peminjaman mandiri, tempelkan kartu pada pembaca RFID.',
                        'Petugas akan memindai buku yang Anda bawa untuk dicatat.',
                        'Simpan kartu ini dengan baik; jangan bagikan nomor anggota Anda.',
                    ] as $step)
                        <li class="flex items-start gap-3">
                            <span class="w-5 h-5 shrink-0 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm flex items-center justify-center">{{ $loop->iteration }}</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Layanan --}}
            <div class="p-4 sm:p-6 rounded-2xl bg-primary text-on-primary shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)] flex flex-col gap-4">
                <h3 class="font-title-md text-title-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">workspace_premium</span> Hak Anggota
                </h3>
                <ul class="flex flex-col gap-2.5 font-body-sm text-body-sm text-on-primary/85">
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">check</span> Meminjam hingga {{ $anggota['kuota'] }} buku sekaligus.</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">check</span> Akses ruang baca dan koleksi referensi.</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">check</span> Reservasi buku melalui katalog daring.</li>
                    <li class="flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">check</span> Mengikuti kegiatan literasi perpustakaan.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
