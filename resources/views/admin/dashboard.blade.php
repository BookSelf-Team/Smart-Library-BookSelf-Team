@extends('layouts.admin')

@section('title', 'Admin Dashboard — Bookself')

@section('content')
<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Dashboard Admin']]" />

    <!-- Top Bar: Greeting -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 pb-2">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span> Operasional Buka • Shift Pagi
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Selamat Datang, <span class="text-primary italic">{{ auth()->user()->name ?? 'Admin Perpustakaan' }}</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Pusat kendali sirkulasi pintar, validasi RFID/QR siswa, dan kurasi katalog fisik perpustakaan.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('admin.peminjaman') }}" class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)]" id="btn-quick-loan">
                <span class="material-symbols-outlined text-[18px]">add_task</span> Transaksi Baru
            </a>
        </div>
    </div>

    <!-- 4 Key Operational Metrics (KPI Bento Grid) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5">
        <div class="bg-surface-container-low rounded-xl p-5 shadow-sm flex flex-col justify-between gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Inventaris Koleksi</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="font-headline-lg text-headline-lg font-bold text-on-surface">4.820</span>
                        <span class="font-label-md text-label-md text-on-surface-variant">Buku Fisik</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-lg bg-primary-container/20 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-low rounded-xl p-5 shadow-sm flex flex-col justify-between gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Sirkulasi Aktif</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="font-headline-lg text-headline-lg font-bold text-on-surface">142</span>
                        <span class="font-label-md text-label-md text-on-surface-variant">Dipinjam</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-lg bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">chrome_reader_mode</span>
                </div>
            </div>
        </div>

        <div class="bg-surface-container-low rounded-xl p-5 shadow-sm flex flex-col justify-between gap-4 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Perhatian Khusus</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="font-headline-lg text-headline-lg font-bold text-error">9</span>
                        <span class="font-label-md text-label-md text-error">Lewat Tempo</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-lg bg-error-container text-on-error-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">running_with_errors</span>
                </div>
            </div>
        </div>

        <div class="bg-primary-container text-on-primary-container rounded-xl p-5 shadow-md flex flex-col justify-between gap-4 relative overflow-hidden group">
            <div class="flex items-start justify-between">
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-primary-container/80 uppercase tracking-wider">Kas Denda Sirkulasi</span>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="font-headline-lg text-headline-lg font-bold text-on-primary-container">Rp 385.000</span>
                    </div>
                </div>
                <div class="w-11 h-11 rounded-lg bg-on-primary-container/15 text-on-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">payments</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Interactive Circulation Panel (Split View) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column (5 Cols): Meja Transaksi Cepat & Barcode Scanner -->
        <div class="lg:col-span-5 bg-surface-container-low rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col gap-6">
            <div class="flex items-center justify-between pb-1">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
                    </div>
                    <div class="flex flex-col">
                        <h2 class="font-title-md text-title-md text-on-surface font-semibold">Meja Transaksi Cepat</h2>
                        <span class="font-label-sm text-label-sm text-on-surface-variant">Validasi Sirkulasi Real-time</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 p-1 bg-surface-container rounded-lg gap-1">
                <a href="{{ route('admin.peminjaman') }}" class="py-2 rounded-md font-label-md text-label-md text-center transition-all bg-surface-container-lowest text-on-surface shadow-sm font-semibold flex items-center justify-center gap-1.5" id="tab-borrow">
                    <span class="material-symbols-outlined text-[18px] text-primary">arrow_circle_up</span> Pinjam Buku
                </a>
                <a href="{{ route('admin.pengembalian') }}" class="py-2 rounded-md font-label-md text-label-md text-center transition-all text-on-surface-variant hover:text-on-surface flex items-center justify-center gap-1.5" id="tab-return">
                    <span class="material-symbols-outlined text-[18px]">arrow_circle_down</span> Kembalikan Buku
                </a>
            </div>

            <div class="bg-surface-container rounded-xl p-4 flex flex-col gap-3 relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <label for="barcode-input" class="font-label-sm text-label-sm font-semibold text-on-surface flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px] text-primary">barcode_reader</span> Scan Kode Barcode / NISN
                    </label>
                </div>
                <div class="relative">
                    <input class="w-full bg-surface-container-lowest pl-11 pr-4 py-2.5 rounded-lg font-body-md text-body-md text-on-surface font-mono tracking-wide outline-none shadow-sm focus:bg-surface-bright transition-all" id="barcode-input" type="text" placeholder="Arahkan scanner kesini...">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-primary text-[20px]">filter_center_focus</span>
                </div>
            </div>

            <a href="{{ route('admin.peminjaman') }}" class="w-full h-12 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-title-sm text-title-sm font-semibold flex items-center justify-center gap-2 transition-all shadow-md active:scale-[0.99]" id="btn-submit-loan">
                <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                <span>Buka Layanan Sirkulasi</span>
            </a>
        </div>

        <!-- Right Column (7 Cols): Aktivitas Sirkulasi Terkini -->
        <div class="lg:col-span-7 bg-surface-container-low rounded-2xl p-5 sm:p-6 shadow-sm flex flex-col gap-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-secondary-container text-on-secondary-fixed-variant flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[20px]">history_edu</span>
                    </div>
                    <div>
                        <h2 class="font-title-md text-title-md text-on-surface font-semibold">Aktivitas Sirkulasi Terkini</h2>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">Log transaksi peminjaman & pengembalian</p>
                    </div>
                </div>
                <a href="{{ route('admin.peminjaman') }}" class="font-label-md text-label-md text-primary font-semibold hover:underline">Semua Log</a>
            </div>

            <div class="overflow-x-auto rounded-xl bg-surface-container-lowest shadow-sm">
                <table class="w-full text-left font-body-sm text-body-sm">
                    <thead>
                        <tr class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Judul Buku</th>
                            <th class="py-3 px-4">Tanggal Pinjam</th>
                            <th class="py-3 px-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-high/40 text-on-surface">
                        @forelse($loans as $loan)
                        <tr class="hover:bg-surface-container-high/30 transition-colors">
                            <td class="py-3.5 px-4"><span class="font-title-sm text-title-sm font-semibold">{{ $loan->user?->name ?? 'Anonim' }}</span></td>
                            <td class="py-3.5 px-4"><span class="font-semibold">{{ $loan->book?->judul ?? 'Tidak diketahui' }}</span></td>
                            <td class="py-3.5 px-4 font-mono text-[12px] text-on-surface-variant whitespace-nowrap">{{ $loan->tanggal_pinjam?->translatedFormat('d M Y') ?? $loan->tanggal_pinjam }}</td>
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if($loan->status == 'ACTIVE')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-label-sm text-label-sm font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Dipinjam
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm font-semibold">
                                        Dikembalikan
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-on-surface-variant">Belum ada data peminjaman masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
