@extends(auth()->user()?->roleEnum()->isStaff() ? 'layouts.admin' : 'layouts.siswa')

@section('title', 'Katalog Buku — Bookself')

@section('content')
@php
    $today = \Illuminate\Support\Carbon::now()->locale('id');
    $status = request('status', 'all');
    $filterUrl = fn ($s) => url('katalog') . '?' . http_build_query(array_merge(request()->query(), ['status' => $s]));
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Katalog Buku']]" />

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    {{ $books->total() }} judul terdaftar • Koleksi Perpustakaan
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, j F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Jelajahi <span class="text-primary italic">Katalog Buku</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Cari judul atau pengarang melalui kolom pencarian di atas, lalu pinjam langsung dari kartu buku yang tersedia.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">space_dashboard</span>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>
    </div>

    {{-- ================= FILTER STATUS ================= --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap items-center gap-2">
            @foreach (['all' => 'Semua Buku', 'ready' => 'Tersedia', 'loan' => 'Dipinjam'] as $key => $label)
                <a href="{{ $filterUrl($key) }}"
                   class="h-9 px-3.5 rounded-lg font-title-sm text-title-sm flex items-center gap-1.5 transition-all
                          {{ $status === $key
                                ? 'bg-primary-container text-on-primary-container font-semibold shadow-[0_2px_8px_-2px_rgba(35,45,19,0.12)]'
                                : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if (request('q'))
            <a href="{{ url('katalog') }}" class="font-label-md text-label-md text-primary font-semibold hover:underline flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">close</span>
                Hapus pencarian "{{ request('q') }}"
            </a>
        @endif
    </div>

    {{-- ================= GRID KARTU BUKU ================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-4 sm:gap-5 stagger-in">
        @forelse($books as $book)
            @php
                $habis = $book->jumlah_stok <= 0;
                $tipis = !$habis && $book->jumlah_stok <= 2;
            @endphp
            <div class="group p-4 rounded-xl bg-surface-container-low hover:bg-surface-container transition-all duration-300 hover:-translate-y-1 hover:shadow-[0_12px_32px_-16px_rgba(35,45,19,0.3)] flex flex-col gap-3">
                <div class="relative w-full aspect-[4/5] rounded-lg overflow-hidden bg-surface-container-high shadow-sm flex items-center justify-center">
                    <span class="material-symbols-outlined text-5xl text-outline">auto_stories</span>
                    <div class="absolute top-2.5 right-2.5">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold
                                     {{ $habis ? 'bg-error-container text-on-error-container' : ($tipis ? 'bg-[#FEF3C7] text-[#92400E]' : 'bg-secondary-container text-on-secondary-container') }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $habis ? 'bg-error' : ($tipis ? 'bg-[#D97706]' : 'bg-primary') }}"></span>
                            {{ $habis ? 'Habis' : ($tipis ? 'Sisa ' . $book->jumlah_stok . ' Eks' : 'Tersedia') }}
                        </span>
                    </div>
                </div>

                <div class="flex flex-col gap-1 min-w-0">
                    <span class="font-label-sm text-label-sm text-outline">ID: BKS-{{ str_pad($book->id, 3, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="font-title-md text-title-md text-on-surface line-clamp-2 leading-snug">{{ $book->judul }}</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1">{{ $book->pengarang }}</p>
                </div>

                <div class="mt-auto p-2.5 rounded-lg bg-surface-container-lowest flex items-center justify-between font-label-md text-label-md">
                    <span class="text-on-surface-variant">Stok tersedia:</span>
                    <span class="font-semibold {{ $habis ? 'text-error' : 'text-primary' }}">{{ $book->jumlah_stok }} eksemplar</span>
                </div>

                @if (auth()->user()?->isMember())
                    <form action="{{ route('loans.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                        <button type="submit" @disabled($habis)
                                class="w-full h-10 rounded-lg font-title-sm text-title-sm flex items-center justify-center gap-1.5 transition-colors shadow-sm
                                       {{ $habis
                                            ? 'bg-surface-container text-outline cursor-not-allowed'
                                            : 'bg-primary-container text-on-primary-container hover:bg-primary hover:text-on-primary active:scale-[0.99]' }}">
                            <span class="material-symbols-outlined text-[17px]">{{ $habis ? 'block' : 'sync_alt' }}</span>
                            <span>{{ $habis ? 'Stok Habis' : 'Pinjamkan Kilat' }}</span>
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="col-span-full text-center py-16 rounded-xl bg-surface-container-low">
                <span class="material-symbols-outlined text-6xl text-outline float-soft">menu_book</span>
                <p class="mt-3 font-title-md text-title-md text-on-surface-variant">
                    Belum ada buku yang cocok dengan pencarian atau filter Anda.
                </p>
                @if (request('q') || $status !== 'all')
                    <a href="{{ url('katalog') }}" class="mt-2 inline-block font-label-md text-label-md text-primary font-semibold hover:underline">
                        Tampilkan semua buku
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- ================= PAGINASI ================= --}}
    <div class="mt-2">
        {{ $books->links() }}
    </div>
</div>
@endsection
