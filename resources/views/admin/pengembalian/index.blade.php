@extends('layouts.admin')

@section('title', 'Pengembalian — Bookself')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col gap-6">
    <x-breadcrumb :items="[['label' => 'Sirkulasi'], ['label' => 'Pengembalian']]" />
    <div class="flex flex-col gap-1">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Sirkulasi</span>
        <h1 class="font-headline-md text-headline-md text-on-surface font-semibold">Pengembalian Buku</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Proses pengembalian buku. {{ $dendaPerHari > 0 ? 'Denda keterlambatan Rp '.number_format($dendaPerHari, 0, ',', '.').'/hari dibuat otomatis bila telat.' : 'Denda keterlambatan sedang tidak diaktifkan.' }}</p>
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
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-center font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Status</th>
                        <th class="px-4 py-3 text-right font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-surface-container-highest">
                    @forelse ($loans as $row)
                        @php $l = $row['loan']; @endphp
                        <tr class="hover:bg-surface-container-low/60">
                            <td class="px-4 py-3 min-w-[150px]">
                                <span class="font-title-sm text-title-sm text-on-surface">{{ $l->user?->name ?? 'Anonim' }}</span>
                                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $l->user?->identifier ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3 font-body-md text-on-surface min-w-[180px]">{{ $l->book?->judul ?? 'Tidak diketahui' }}</td>
                            <td class="px-4 py-3 font-body-md font-mono text-[12px] whitespace-nowrap {{ $row['telat'] ? 'text-error font-bold' : 'text-on-surface-variant' }}">
                                {{ $l->batas_kembali?->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                @if ($row['telat'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-error-container text-on-error-container font-label-sm font-semibold">
                                        Telat {{ $row['telat'] }} hari • Denda Rp {{ number_format($row['denda'], 0, ',', '.') }}
                                    </span>
                                @elseif ($row['sisa_hari'] <= 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-[#FEF3C7] text-[#92400E] font-label-sm font-semibold">
                                        {{ $row['sisa_hari'] === 0 ? 'Jatuh tempo hari ini' : 'Jatuh tempo besok' }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm">
                                        Sisa {{ $row['sisa_hari'] }} hari
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('invoices.loan', $l->id) }}" target="_blank" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-flex items-center" title="Lihat Struk Pinjam">
                                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                    </a>
                                    <form method="POST" action="{{ route('admin.pengembalian.proses', $l->id) }}" onsubmit="return confirm('Proses pengembalian buku ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-container text-on-primary-container hover:bg-primary hover:text-on-primary font-title-sm text-label-sm transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                                            Proses Kembali
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center font-body-md text-on-surface-variant">Tidak ada peminjaman aktif yang menunggu pengembalian. 🎉</td>
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
@endsection
