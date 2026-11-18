@extends(auth()->user()?->roleEnum()->isStaff() ? 'layouts.admin' : 'layouts.siswa')

@section('title', $book->judul . ' — Bookself')

@section('content')
@php
    $today = \Illuminate\Support\Carbon::now()->locale('id');
    $habis = $book->jumlah_stok <= 0;
    $tipis = !$habis && $book->jumlah_stok <= 2;
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[
        ['label' => 'Katalog Buku', 'url' => url('katalog')],
        ['label' => $book->judul],
    ]" />

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm
                             {{ $habis ? 'bg-error-container text-on-error-container' : ($tipis ? 'bg-[#FEF3C7] text-[#92400E]' : 'bg-secondary-container text-on-secondary-fixed-variant') }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $habis ? 'bg-error' : ($tipis ? 'bg-[#D97706]' : 'bg-primary') }} {{ $habis ? '' : 'animate-ping' }}"></span>
                    {{ $habis ? 'Stok Habis' : ($tipis ? 'Sisa '.$book->jumlah_stok.' Eks' : 'Tersedia • '.$book->jumlah_stok.' Eks') }}
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, j F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                {{ $book->judul }}
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                oleh <span class="text-primary font-semibold">{{ $book->pengarang }}</span>
                @if ($book->tahun_terbit) • Terbit {{ $book->tahun_terbit }} @endif
                • Kode {{ $book->kode_buku ?? 'BKS-'.str_pad($book->id, 3, '0', STR_PAD_LEFT) }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url('katalog') }}"
               class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">menu_book</span>
                <span>Kembali ke Katalog</span>
            </a>
        </div>
    </div>

    {{-- ================= DETAIL BUKU ================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Sampul --}}
        <div class="lg:col-span-4 flex flex-col gap-4">
            <div class="rounded-2xl bg-surface-container-low p-6 flex items-center justify-center">
                <div class="relative w-full max-w-[260px] aspect-[4/5] rounded-lg overflow-hidden bg-surface-container-high shadow-[0_12px_32px_-16px_rgba(35,45,19,0.3)] flex items-center justify-center">
                    @if ($book->coverUrl())
                        <img src="{{ $book->coverUrl() }}" alt="Sampul {{ $book->judul }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center gap-2 text-outline">
                            <span class="material-symbols-outlined text-7xl">auto_stories</span>
                            <span class="font-label-sm text-label-sm uppercase tracking-widest">Bookself</span>
                        </div>
                    @endif
                    <div class="absolute top-2.5 right-2.5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold backdrop-blur-sm
                                     {{ $habis ? 'bg-error-container/90 text-on-error-container' : ($tipis ? 'bg-[#FEF3C7]/90 text-[#92400E]' : 'bg-secondary-container/90 text-on-secondary-container') }}">
                            {{ $habis ? 'Habis' : ($tipis ? 'Sisa '.$book->jumlah_stok.' Eks' : 'Tersedia') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5 rounded-2xl bg-surface-container-low flex flex-col gap-3">
                <h3 class="font-title-md text-title-md text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">inventory_2</span> Ketersediaan
                </h3>
                <div class="p-3 rounded-lg bg-surface-container-lowest flex items-center justify-between font-label-md text-label-md">
                    <span class="text-on-surface-variant">Stok tersedia:</span>
                    <span class="font-semibold {{ $habis ? 'text-error' : 'text-primary' }}">{{ $book->jumlah_stok }} eksemplar</span>
                </div>
                <div class="p-3 rounded-lg bg-surface-container-lowest flex items-center justify-between font-label-md text-label-md">
                    <span class="text-on-surface-variant">Kode buku:</span>
                    <span class="font-mono text-[12px]">{{ $book->kode_buku ?? 'BKS-'.str_pad($book->id, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
                @if ($book->tahun_terbit)
                    <div class="p-3 rounded-lg bg-surface-container-lowest flex items-center justify-between font-label-md text-label-md">
                        <span class="text-on-surface-variant">Tahun terbit:</span>
                        <span class="font-semibold">{{ $book->tahun_terbit }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sinopsis + aksi --}}
        <div class="lg:col-span-8 flex flex-col gap-6">
            <div class="p-5 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-4">
                <h2 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">auto_stories</span> Sinopsis
                </h2>
                <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                    {{ $book->sinopsis ?: 'Sinopsis untuk buku ini belum tersedia di perpustakaan. Silakan tanyakan kepada petugas untuk informasi lebih lanjut.' }}
                </p>
            </div>

            <div class="p-5 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container shrink-0">
                        <span class="material-symbols-outlined text-[22px]">pin_drop</span>
                    </div>
                    <div>
                        <p class="font-title-sm text-title-sm text-on-surface">Ajukan di meja sirkulasi</p>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Ajukan peminjaman di sini, lalu tunggu persetujuan petugas untuk mengambil buku di meja sirkulasi.</p>
                    </div>
                </div>

                @if (auth()->user()?->isMember())
                    <form action="{{ route('loans.store') }}" method="POST" class="shrink-0">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button type="submit" @disabled($habis)
                                class="h-11 px-6 rounded-lg font-title-sm text-title-sm flex items-center justify-center gap-1.5 transition-colors shadow-sm w-full sm:w-auto
                                       {{ $habis
                                            ? 'bg-surface-container text-outline cursor-not-allowed'
                                            : 'bg-primary-container text-on-primary-container hover:bg-primary hover:text-on-primary active:scale-[0.99]' }}">
                            <span class="material-symbols-outlined text-[18px]">{{ $habis ? 'block' : 'send' }}</span>
                            <span>{{ $habis ? 'Stok Habis' : 'Ajukan Pinjaman' }}</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
