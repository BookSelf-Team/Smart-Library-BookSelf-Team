@extends('layouts.admin')

@section('title', 'Peminjaman — Bookself')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col gap-6">
    <x-breadcrumb :items="[['label' => 'Sirkulasi'], ['label' => 'Peminjaman']]" />
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Sirkulasi</span>
            <h1 class="font-headline-md text-headline-md text-on-surface font-semibold">Peminjaman Buku</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Proses peminjaman buku untuk anggota dan pantau seluruh pinjaman aktif.</p>
        </div>
        <button onclick="openModal()" type="button" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-[0_2px_8px_-2px_rgba(35,45,19,0.25)] transition-colors">
            <span class="material-symbols-outlined text-[20px]">add_task</span>
            <span class="font-title-sm text-title-sm">Pinjamkan Buku</span>
        </button>
    </div>

    @if (session('success'))
        <div class="bg-primary-container text-on-primary-container px-4 py-3 rounded-lg font-body-md shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-body-md shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-surface-container-low shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-surface-container-highest">
                <thead class="bg-surface-container">
                    <tr>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Peminjam</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant">Buku</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Tgl Pinjam</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-center font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-right font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Struk</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-surface-container-highest">
                    @forelse ($loans as $l)
                        @php
                            $aktif = $l->tanggal_dikembalikan === null;
                            $telat = $aktif && $l->batas_kembali?->isPast();
                        @endphp
                        <tr class="hover:bg-surface-container-low/60">
                            <td class="px-4 py-3 min-w-[150px]">
                                <span class="font-title-sm text-title-sm text-on-surface">{{ $l->user?->name ?? 'Anonim' }}</span>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $l->user?->identifier ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 font-body-md text-on-surface min-w-[180px]">{{ $l->book?->judul ?? 'Tidak diketahui' }}</td>
                            <td class="px-4 py-3 font-body-md text-on-surface-variant font-mono text-[12px] whitespace-nowrap">{{ $l->tanggal_pinjam?->translatedFormat('d M Y') }}</td>
                            <td class="px-4 py-3 font-body-md font-mono text-[12px] whitespace-nowrap {{ $telat ? 'text-error font-bold' : 'text-on-surface-variant' }}">
                                {{ $l->batas_kembali?->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if (! $aktif)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm">Selesai</span>
                                @elseif ($telat)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm font-semibold">Terlambat</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-primary-fixed text-on-primary-fixed-variant font-label-sm font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-primary"></span> Dipinjam
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('invoices.loan', $l->id) }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-surface-container text-on-surface hover:text-primary hover:bg-surface-container-high transition-colors" title="Cetak Bukti Peminjaman">
                                    <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                                    <span>Struk</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center font-body-md text-on-surface-variant">Belum ada data peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($loans->hasPages())
            <div class="px-4 py-3 border-t border-surface-container-highest">{{ $loans->links() }}</div>
        @endif
    </div>
</div>

<div id="loanModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg bg-surface rounded-lg shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between shrink-0">
            <h2 class="font-title-lg text-title-lg text-on-surface">Pinjamkan Buku</h2>
            <button onclick="closeModal()" class="p-2 rounded-lg hover:bg-surface-container"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" action="{{ route('admin.peminjaman.store') }}" class="p-4 sm:p-6 flex flex-col gap-4 overflow-y-auto">
            @csrf
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Anggota <span class="text-error">*</span></label>
                <select name="user_id" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Pilih anggota...</option>
                    @foreach ($members as $s)
                        <option value="{{ $s->id }}" @selected(old('user_id') == $s->id)>{{ $s->name }} ({{ $s->identifier ?? $s->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Buku <span class="text-error">*</span></label>
                <select name="book_id" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">Pilih buku...</option>
                    @foreach ($books as $b)
                        <option value="{{ $b->id }}" @selected(old('book_id') == $b->id)>{{ $b->judul }} — stok {{ $b->jumlah_stok }}</option>
                    @endforeach
                </select>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Lama pinjam 7 hari. Kuota maksimal 3 buku aktif per anggota.</p>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90">Proses Pinjam</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(){
    const m=document.getElementById('loanModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}
function closeModal(){
    const m=document.getElementById('loanModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}
</script>
@endpush
@endsection
