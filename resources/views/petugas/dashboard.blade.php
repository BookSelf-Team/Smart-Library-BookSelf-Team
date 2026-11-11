@extends('layouts.admin')

@section('title', 'Dashboard Petugas — Bookself')

@section('content')
@php
    $rupiah = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
    $dipinjam = (int) ($member->dipinjam ?? 0);
    $pctKuota = min(100, round($dipinjam / 3 * 100));
    $today = \Illuminate\Support\Carbon::now()->locale('id');
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Dashboard Petugas']]" />

    {{-- ================= HEADER + PENCARIAN ANGGOTA ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    Meja Sirkulasi • Aktif
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Selamat Datang, <span class="text-primary italic">{{ auth()->user()->name ?? 'Petugas' }}</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Cari anggota untuk membuka kartu anggota digital, memeriksa buku yang belum kembali, dan memproses pengembalian dengan satu klik.
            </p>
        </div>

        <form method="GET" action="{{ route('petugas.dashboard') }}" class="flex items-center gap-2 w-full xl:w-auto">
            <div class="relative flex-1 xl:w-80">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">person_search</span>
                <input name="q" type="text" value="{{ $q }}" placeholder="Cari nama, identifier, atau email anggota..."
                       class="w-full h-11 bg-surface-container pl-10 pr-4 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all">
            </div>
            <button type="submit"
                    class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)] active:scale-[0.98] whitespace-nowrap">
                <span class="material-symbols-outlined text-[18px]">search</span>
                <span>Cari Anggota</span>
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="bg-primary-container text-on-primary-container px-4 py-3 rounded-lg font-body-md shadow-sm flex items-center gap-2 flash-in">
            <span class="material-symbols-outlined text-[20px]">check_circle</span>
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-body-md shadow-sm flex items-center gap-2 flash-in">
            <span class="material-symbols-outlined text-[20px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- ================= KARTU STATISTIK ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 stagger-in">

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Total Buku</span>
                    <p class="font-headline-lg text-headline-lg text-on-surface mt-1">{{ $stats['total_buku'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[22px]">menu_book</span>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Sedang Dipinjam</span>
                    <p class="font-headline-lg text-headline-lg text-on-surface mt-1">{{ $stats['buku_dipinjam'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[22px]">book</span>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Total Anggota</span>
                    <p class="font-headline-lg text-headline-lg text-on-surface mt-1">{{ $stats['total_anggota'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[22px]">school</span>
                </div>
            </div>
        </div>

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Terlambat</span>
                    <p class="font-headline-lg text-headline-lg {{ $stats['terlambat'] > 0 ? 'text-error' : 'text-on-surface' }} mt-1">{{ $stats['terlambat'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg {{ $stats['terlambat'] > 0 ? 'bg-error-container text-error' : 'bg-secondary-container text-on-secondary-container' }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">schedule</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= HASIL PENCARIAN ================= --}}
    @if ($results->isNotEmpty())
        <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-4 pop-in">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                    <div>
                        <h2 class="font-title-md text-title-md text-on-surface font-semibold">Hasil Pencarian "{{ $q }}"</h2>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Pilih anggota untuk membuka meja sirkulasi</p>
                    </div>
                </div>
                <span class="font-label-sm text-label-sm px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-semibold">{{ $results->count() }} anggota</span>
            </div>

            <div class="rounded-xl bg-surface-container-lowest divide-y divide-surface-container">
                @foreach ($results as $hasil)
                    <a href="{{ route('petugas.dashboard', ['member' => $hasil->getKey(), 'q' => $q]) }}"
                       class="flex items-center justify-between gap-4 px-4 py-3 hover:bg-surface-container/60 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                                <span class="font-title-md text-title-md">{{ $hasil->initials() }}</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-title-sm text-title-sm text-on-surface truncate">{{ $hasil->name }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant truncate font-mono">{{ $hasil->identifier ?? 'tanpa identifier' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 shrink-0">
                            <x-role-badge :role="$hasil->roleEnum()" />
                            <span class="font-label-sm text-label-sm {{ $hasil->dipinjam > 0 ? 'text-primary font-semibold' : 'text-on-surface-variant' }}">{{ $hasil->dipinjam }} buku dipinjam</span>
                            <span class="material-symbols-outlined text-on-surface-variant text-[20px]">chevron_right</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @elseif ($q !== '' && $member === null)
        <div class="p-10 rounded-2xl bg-surface-container-low flex flex-col items-center gap-2 text-center">
            <span class="material-symbols-outlined text-[40px] text-outline float-soft">person_off</span>
            <p class="font-title-md text-title-md text-on-surface">Tidak ada anggota yang cocok dengan "{{ $q }}"</p>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Coba nama, identifier (mis. SIS-000001), atau email anggota.</p>
        </div>
    @endif

    {{-- ================= ANGGOTA TERPILIH: KARTU + TABEL ================= --}}
    @if ($member !== null)
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

            {{-- ===== KIRI: KARTU ANGGOTA DIGITAL ===== --}}
            <div class="xl:col-span-4 flex flex-col gap-6">
                <div class="relative overflow-hidden rounded-2xl bg-primary text-on-primary shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)] p-5 flex flex-col gap-5 min-h-[280px] pop-in">
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
                        <p class="text-xl sm:text-headline-md leading-tight break-words">{{ $member->name }}</p>
                        <p class="font-label-md text-label-md text-on-primary/80">{{ $member->kelas ?? 'Anggota Perpustakaan' }} • {{ $member->roleEnum()->label() }}</p>
                    </div>

                    <div class="relative grid grid-cols-2 gap-4">
                        <div>
                            <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Identifier</p>
                            <p class="font-mono text-[15px] tracking-wider break-all">{{ $member->identifier ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Terdaftar Sejak</p>
                            <p class="font-mono text-[15px] tracking-wider">{{ $member->created_at?->translatedFormat('M Y') ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="relative flex items-end justify-between gap-6">
                        <div class="flex-1">
                            <div class="h-12 rounded bg-white/90" style="background-image: repeating-linear-gradient(90deg, #1F2915 0 2px, transparent 2px 5px, #1F2915 5px 6px, transparent 6px 11px);"></div>
                            <p class="font-mono text-[11px] tracking-[0.3em] text-on-primary/70 mt-1.5">{{ $member->identifier ?? 'AGT-'.str_pad((string) $member->id, 4, '0', STR_PAD_LEFT) }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Kuota Pinjam</p>
                            <p class="font-title-lg text-title-lg">{{ $dipinjam }} / 3</p>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan anggota --}}
                <div class="p-5 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">badge</span>
                            </div>
                            <h3 class="font-title-md text-title-md text-on-surface font-semibold">Data Keanggotaan</h3>
                        </div>
                        <a href="{{ route('petugas.dashboard') }}" class="font-label-sm text-label-sm text-primary font-semibold hover:underline">Tutup</a>
                    </div>
                    <div class="flex flex-col">
                        @foreach ([
                            ['Peran', fn () => $member->roleEnum()->label(), 'workspace_premium'],
                            ['Email', fn () => $member->email ?? '—', 'mail'],
                            ['Buku dipinjam', fn () => $dipinjam.' dari 3 kuota', 'book'],
                            ['Denda belum lunas', fn () => $rupiah($dendaBelumLunas), 'payments'],
                        ] as [$label, $valueFn, $icon])
                            <div class="flex items-center justify-between gap-4 py-3 {{ !$loop->first ? 'border-t border-surface-container' : '' }}">
                                <span class="font-body-sm text-body-sm text-on-surface-variant inline-flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] text-outline">{{ $icon }}</span>
                                    {{ $label }}
                                </span>
                                <span class="font-title-sm text-title-sm text-right max-w-[60%] break-words {{ $label === 'Denda belum lunas' && $dendaBelumLunas > 0 ? 'text-error' : 'text-on-surface' }}">{{ $valueFn() }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div>
                        <div class="h-2 rounded-full bg-surface-container-high">
                            <div class="h-2 rounded-full {{ $dipinjam >= 3 ? 'bg-error' : 'bg-primary' }}" style="width: {{ $pctKuota }}%"></div>
                        </div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant mt-1.5">Penggunaan kuota peminjaman</p>
                    </div>
                </div>
            </div>

            {{-- ===== KANAN: PINJAMAN AKTIF + RIWAYAT & DENDA ===== --}}
            <div class="xl:col-span-8 flex flex-col gap-6 stagger-in">

                {{-- Pinjaman aktif dengan tombol pengembalian satu klik --}}
                <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">assignment_return</span>
                            </div>
                            <div>
                                <h2 class="font-title-md text-title-md text-on-surface font-semibold">Buku Belum Dikembalikan</h2>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">Pinjaman berjalan beserta estimasi denda keterlambatan</p>
                            </div>
                        </div>
                        <span class="font-label-sm text-label-sm px-2.5 py-0.5 rounded-full {{ $pinjamanAktif->isEmpty() ? 'bg-secondary-container text-on-secondary-fixed-variant' : 'bg-[#FEF3C7] text-[#92400E]' }} font-semibold">
                            {{ $pinjamanAktif->count() }} judul
                        </span>
                    </div>

                    <div class="rounded-xl bg-surface-container-lowest overflow-x-auto">
                        <table class="w-full text-left font-body-sm text-body-sm">
                            <thead>
                                <tr class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                                    <th class="py-3 px-4">Buku</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Dipinjam</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Jatuh Tempo</th>
                                    <th class="py-3 px-4 text-center whitespace-nowrap">Status</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Denda</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container-high/40 text-on-surface">
                                @forelse ($pinjamanAktif as $p)
                                    <tr class="{{ $p['telat'] > 0 ? 'bg-error-container/30' : '' }}">
                                        <td class="py-3.5 px-4 min-w-[170px]">
                                            <p class="font-title-sm text-title-sm text-on-surface">{{ $p['judul'] }}</p>
                                            <p class="text-on-surface-variant font-mono text-[11px]">{{ $p['kode'] }}</p>
                                        </td>
                                        <td class="py-3.5 px-4 text-on-surface-variant whitespace-nowrap">{{ $p['tgl_pinjam']->translatedFormat('d M Y') }}</td>
                                        <td class="py-3.5 px-4 whitespace-nowrap {{ $p['telat'] > 0 ? 'text-error font-semibold' : 'text-on-surface' }}">
                                            {{ $p['jatuh_tempo']->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                            @if ($p['telat'] > 0)
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold bg-error-container text-on-error-container">Telat {{ $p['telat'] }} hari</span>
                                            @elseif ($p['sisa_hari'] <= 1)
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold bg-[#FEF3C7] text-[#92400E]">{{ $p['sisa_hari'] === 0 ? 'Kembali hari ini' : 'Besok jatuh tempo' }}</span>
                                            @else
                                                <span class="inline-flex px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold bg-secondary-container text-on-secondary-container">Sisa {{ $p['sisa_hari'] }} hari</span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[12px] {{ $p['denda'] > 0 ? 'text-error font-semibold' : 'text-on-surface-variant' }}">
                                            {{ $p['denda'] > 0 ? $rupiah($p['denda']) : '—' }}
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <form method="POST" action="{{ route('admin.pengembalian.proses', $p['id']) }}" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" data-loading-text="Memproses..."
                                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#F97316] text-white hover:bg-[#EA580C] active:bg-[#C2410C] active:scale-[0.98] font-title-sm text-label-sm font-semibold transition-all shadow-[0_2px_8px_-2px_rgba(249,115,22,0.45)]">
                                                    <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                                                    Kembalikan
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-on-surface-variant">
                                            <span class="material-symbols-outlined text-[36px] text-outline float-soft">task_alt</span>
                                            <p class="mt-1">Tidak ada buku yang sedang dipinjam.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Riwayat & denda --}}
                <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-lg bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                            </div>
                            <div>
                                <h2 class="font-title-md text-title-md text-on-surface font-semibold">Riwayat & Denda</h2>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">10 pengembalian terakhir beserta status dendanya</p>
                            </div>
                        </div>
                        <span class="font-label-sm text-label-sm px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-semibold">10 terakhir</span>
                    </div>

                    <div class="rounded-xl bg-surface-container-lowest overflow-x-auto">
                        <table class="w-full text-left font-body-sm text-body-sm">
                            <thead>
                                <tr class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                                    <th class="py-3 px-4">Buku</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Dipinjam</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Dikembalikan</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Denda</th>
                                    <th class="py-3 px-4 whitespace-nowrap">Status</th>
                                    <th class="py-3 px-4 text-right whitespace-nowrap">Dokumen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-surface-container-high/40 text-on-surface">
                                @forelse ($riwayat as $r)
                                    @php
                                        if ($r['denda'] > 0 && ! $r['denda_dibayar']) {
                                            $badge = ['bg-error-container text-on-error-container', 'Denda belum dibayar'];
                                        } elseif ($r['denda'] > 0 && $r['denda_dibayar']) {
                                            $badge = ['bg-secondary-container text-on-secondary-fixed-variant', 'Denda lunas'];
                                        } else {
                                            $badge = ['bg-secondary-container text-on-secondary-fixed-variant', 'Tepat waktu'];
                                        }
                                    @endphp
                                    <tr>
                                        <td class="py-3.5 px-4 min-w-[170px] font-title-sm text-title-sm text-on-surface">{{ $r['judul'] }}</td>
                                        <td class="py-3.5 px-4 text-on-surface-variant whitespace-nowrap">{{ $r['tgl_pinjam']->translatedFormat('d M Y') }}</td>
                                        <td class="py-3.5 px-4 text-on-surface-variant whitespace-nowrap">{{ $r['tgl_kembali']?->translatedFormat('d M Y') ?? '—' }}</td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap font-mono text-[12px] {{ $r['denda'] > 0 && ! $r['denda_dibayar'] ? 'text-error font-semibold' : 'text-on-surface-variant' }}">
                                            {{ $r['denda'] > 0 ? $rupiah($r['denda']) : '—' }}
                                        </td>
                                        <td class="py-3.5 px-4">
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold whitespace-nowrap {{ $badge[0] }}">{{ $badge[1] }}</span>
                                        </td>
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-1.5">
                                                <a href="{{ route('invoices.loan', $r['id']) }}" target="_blank" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-flex items-center" title="Cetak Bukti Pinjam">
                                                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                                </a>
                                                @if (! empty($r['fine_id']))
                                                    <a href="{{ route('invoices.fine', $r['fine_id']) }}" target="_blank" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-surface-container transition-colors inline-flex items-center" title="Cetak Kuitansi Denda">
                                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-10 text-center text-on-surface-variant">
                                            <span class="material-symbols-outlined text-[36px] text-outline float-soft">receipt_long</span>
                                            <p class="mt-1">Belum ada riwayat pengembalian.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @elseif ($q === '')
        {{-- Empty state saat belum ada anggota terpilih --}}
        <div class="p-12 rounded-2xl bg-surface-container-low flex flex-col items-center gap-3 text-center">
            <div class="w-14 h-14 rounded-2xl bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center">
                <span class="material-symbols-outlined text-[28px] float-soft">touch_app</span>
            </div>
            <p class="font-title-lg text-title-lg text-on-surface">Pilih anggota untuk mulai transaksi sirkulasi</p>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-md">
                Gunakan pencarian di atas dengan nama, identifier, atau email. Kartu anggota digital,
                daftar buku yang belum kembali, dan tombol pengembalian satu klik akan muncul di sini.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-2">
                <a href="{{ route('admin.peminjaman') }}" class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)]">
                    <span class="material-symbols-outlined text-[18px]">add_task</span>
                    Peminjaman
                </a>
                <a href="{{ route('admin.pengembalian') }}" class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">assignment_return</span>
                    Daftar Pengembalian
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
