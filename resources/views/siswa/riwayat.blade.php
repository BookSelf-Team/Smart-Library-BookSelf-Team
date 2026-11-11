@extends('layouts.siswa')

@section('title', 'Riwayat & Denda — Bookself')

@section('content')
@php
    $today = \Illuminate\Support\Carbon::now()->locale('id');
    $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $filterUrl = fn ($f) => url('siswa/riwayat') . '?' . http_build_query(array_merge(request()->query(), ['filter' => $f]));
    $tabs = ['semua' => 'Semua Riwayat', 'aktif' => 'Sedang Dipinjam', 'terlambat' => 'Denda Belum Dibayar', 'lunas' => 'Denda Lunas'];
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Riwayat & Denda']]" />

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    {{ $stats['total'] }} transaksi tercatat • Riwayat Pribadi
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, j F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Riwayat <span class="text-primary italic">&amp; Denda</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Arsip seluruh peminjaman Anda beserta rincian denda keterlambatan yang perlu diselesaikan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url('siswa/pinjaman') }}"
               class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">bookmark_added</span>
                <span>Pinjaman Aktif</span>
            </a>
            <a href="{{ url('katalog') }}"
               class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)] active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">menu_book</span>
                <span>Jelajahi Katalog</span>
            </a>
        </div>
    </div>

    {{-- ================= KARTU STATISTIK ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Total Peminjaman</span>
                    <p class="font-headline-lg text-headline-lg text-on-surface mt-1">{{ $stats['total'] }}
                        <span class="font-body-sm text-body-sm text-on-surface-variant">transaksi</span></p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                    <span class="material-symbols-outlined text-[22px]">library_books</span>
                </div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $stats['selesai'] }} selesai • {{ $stats['aktif'] }} masih dipinjam</p>
        </div>

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Pernah Terlambat</span>
                    <p class="font-headline-lg text-headline-lg {{ $stats['terlambat'] > 0 ? 'text-error' : 'text-primary' }} mt-1">{{ $stats['terlambat'] }}
                        <span class="font-body-sm text-body-sm text-on-surface-variant">kali</span></p>
                </div>
                <div class="w-10 h-10 rounded-lg {{ $stats['terlambat'] > 0 ? 'bg-error-container text-error' : 'bg-secondary-container text-on-secondary-container' }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">schedule</span>
                </div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $dendaPerHari > 0 ? 'Denda '.$rupiah($dendaPerHari).' per hari per buku.' : 'Denda keterlambatan sedang tidak diaktifkan.' }}</p>
        </div>

        <div class="p-5 rounded-xl bg-surface-container-low flex flex-col gap-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">Denda Belum Dibayar</span>
                    <p class="font-headline-lg text-headline-lg {{ $stats['denda_belum'] > 0 ? 'text-error' : 'text-primary' }} mt-1">{{ $rupiah($stats['denda_belum']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg {{ $stats['denda_belum'] > 0 ? 'bg-error-container text-error' : 'bg-secondary-container text-on-secondary-container' }} flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">{{ $stats['denda_belum'] > 0 ? 'payments' : 'check_circle' }}</span>
                </div>
            </div>
            <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $stats['denda_belum'] > 0 ? 'Selesaikan pembayaran di meja pustakawan.' : 'Semua denda sudah lunas.' }}</p>
        </div>

        <div class="p-5 rounded-xl bg-primary text-on-primary flex flex-col gap-4 shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)]">
            <div class="flex items-start justify-between">
                <div>
                    <span class="font-label-sm text-label-sm uppercase tracking-wider text-on-primary/80">Total Denda Dibayar</span>
                    <p class="font-headline-lg text-headline-lg mt-1">{{ $rupiah($stats['denda_lunas']) }}</p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-white/15 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">verified</span>
                </div>
            </div>
            <p class="font-label-sm text-label-sm text-on-primary/80">Terima kasih telah menuntaskan kewajiban Anda.</p>
        </div>
    </div>

    {{-- ================= FILTER ================= --}}
    <div class="flex flex-wrap items-center gap-2">
        @foreach ($tabs as $key => $label)
            <a href="{{ $filterUrl($key) }}"
               class="h-9 px-3.5 rounded-lg font-title-sm text-title-sm flex items-center gap-1.5 transition-all
                      {{ $filter === $key
                            ? 'bg-primary-container text-on-primary-container font-semibold shadow-[0_2px_8px_-2px_rgba(35,45,19,0.12)]'
                            : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- ================= TABEL RIWAYAT + SIDEBAR ================= --}}
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        <div class="xl:col-span-8 p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">Arsip Peminjaman</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Menampilkan {{ $riwayat->count() }} dari {{ $stats['total'] }} transaksi.</p>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container font-label-sm text-label-sm uppercase tracking-wider text-on-surface-variant">
                            <th class="px-4 py-3">Judul Buku</th>
                            <th class="px-4 py-3 whitespace-nowrap">Dipinjam</th>
                            <th class="px-4 py-3 whitespace-nowrap">Dikembalikan</th>
                            <th class="px-4 py-3 whitespace-nowrap">Durasi</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Denda</th>
                            <th class="px-4 py-3 whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 text-right whitespace-nowrap">Dokumen</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-sm text-body-sm">
                        @forelse ($riwayat as $r)
                            @php
                                if ($r['status'] === 'dipinjam' && $r['denda'] > 0) {
                                    $badge = ['bg-error-container text-on-error-container', 'Terlambat'];
                                } elseif ($r['denda'] > 0 && ! $r['denda_dibayar']) {
                                    $badge = ['bg-error-container text-on-error-container', 'Denda belum dibayar'];
                                } elseif ($r['denda'] > 0 && $r['denda_dibayar']) {
                                    $badge = ['bg-secondary-container text-on-secondary-container', 'Denda lunas'];
                                } elseif ($r['status'] === 'dipinjam') {
                                    $badge = ['bg-[#FEF3C7] text-[#92400E]', 'Sedang dipinjam'];
                                } else {
                                    $badge = ['bg-secondary-container text-on-secondary-container', 'Tepat waktu'];
                                }
                            @endphp
                            <tr class="border-t border-surface-container">
                                <td class="px-4 py-3 min-w-[170px]">
                                    <p class="font-title-sm text-title-sm text-on-surface">{{ $r['title'] }}</p>
                                    <p class="text-on-surface-variant">{{ $r['author'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap">{{ $r['tgl_pinjam']->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap">
                                    {{ $r['tgl_kembali'] ? $r['tgl_kembali']->translatedFormat('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3 text-on-surface-variant whitespace-nowrap">{{ $r['hari'] }} hari</td>
                                <td class="px-4 py-3 text-right whitespace-nowrap font-mono text-[12px] {{ $r['denda'] > 0 && ! $r['denda_dibayar'] ? 'text-error font-semibold' : 'text-on-surface-variant' }}">
                                    {{ $r['denda'] > 0 ? $rupiah($r['denda']) : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold whitespace-nowrap {{ $badge[0] }}">{{ $badge[1] }}</span>
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('invoices.loan', $r['id']) }}" target="_blank" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-flex items-center" title="Cetak Bukti Pinjam">
                                            <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                        </a>
                                        @if (!empty($r['fine_id']))
                                            <a href="{{ route('invoices.fine', $r['fine_id']) }}" target="_blank" class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-surface-container transition-colors inline-flex items-center" title="Cetak Kuitansi Denda">
                                                <span class="material-symbols-outlined text-[18px]">payments</span>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-on-surface-variant">
                                    <span class="material-symbols-outlined text-[36px] text-outline">receipt_long</span>
                                    <p class="mt-1">Tidak ada transaksi pada filter ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="xl:col-span-4 flex flex-col gap-6">

            {{-- Ringkasan denda --}}
            <div class="p-5 sm:p-6 rounded-2xl bg-primary text-on-primary shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)] flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase tracking-widest text-on-primary/80">Ringkasan Denda</span>
                    <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                </div>
                <div>
                    <p class="font-label-sm text-label-sm text-on-primary/70 uppercase">Belum Dibayar</p>
                    <p class="font-headline-lg text-headline-lg">{{ $rupiah($stats['denda_belum']) }}</p>
                </div>
                <div class="h-px bg-white/20"></div>
                <div class="flex items-center justify-between font-body-sm text-body-sm">
                    <span class="text-on-primary/80">Sudah dibayar</span>
                    <span class="font-semibold">{{ $rupiah($stats['denda_lunas']) }}</span>
                </div>
                <div class="flex items-center justify-between font-body-sm text-body-sm">
                    <span class="text-on-primary/80">Total seluruh denda</span>
                    <span class="font-semibold">{{ $rupiah($stats['denda_total']) }}</span>
                </div>
                <p class="font-label-sm text-label-sm text-on-primary/80">Pembayaran denda dilayani tunai di meja sirkulasi pada jam layanan.</p>
            </div>

            {{-- Tarif denda --}}
            <div class="p-5 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-4">
                <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">sell</span> Ketentuan Denda
                </h3>
                <ul class="flex flex-col gap-3 font-body-sm text-body-sm text-on-surface-variant">
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] text-primary">payments</span>
                        <span>{{ $dendaPerHari > 0 ? 'Keterlambatan dikenakan '.$rupiah($dendaPerHari).' per hari per buku.' : 'Denda keterlambatan sedang tidak diaktifkan.' }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] text-primary">event_busy</span>
                        <span>Denda terhitung sejak hari pertama setelah jatuh tempo.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] text-primary">block</span>
                        <span>Denda yang belum lunas menahan kuota peminjaman berikutnya.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="material-symbols-outlined text-[18px] text-primary">inventory_2</span>
                        <span>Buku hilang wajib diganti judul yang sama atau nilai setara.</span>
                    </li>
                </ul>
                <a href="{{ url('siswa/pinjaman') }}"
                   class="mt-1 h-10 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center justify-center gap-2 transition-all">
                    <span class="material-symbols-outlined text-primary text-[18px]">bookmark_added</span>
                    <span>Cek Pinjaman Aktif</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
