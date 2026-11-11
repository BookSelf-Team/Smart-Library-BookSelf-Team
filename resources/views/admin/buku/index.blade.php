@extends('layouts.admin')

@section('title', 'Data Buku — Bookself')

@section('content')
@php($canManageBooks = auth()->user()->canManageBooks())
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col gap-6">
    <x-breadcrumb :items="[['label' => 'Koleksi Buku']]" />
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Koleksi</span>
            <h1 class="font-headline-md text-headline-md text-on-surface font-semibold">Data Buku</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Kelola seluruh koleksi buku perpustakaan ({{ $books->total() }} judul terdaftar)</p>
        </div>
        @if ($canManageBooks)
            <a href="{{ route('admin.buku.tambah') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90 shadow-[0_2px_8px_-2px_rgba(35,45,19,0.25)] transition-colors">
                <span class="material-symbols-outlined text-[20px]">add_box</span>
                <span class="font-title-sm text-title-sm">Tambah Buku</span>
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="bg-primary-container text-on-primary-container px-4 py-3 rounded-lg font-body-md shadow-sm flash-in">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-body-md shadow-sm flash-in">{{ session('error') }}</div>
    @endif

    <form method="GET" action="{{ route('admin.buku.index') }}" class="relative max-w-md">
        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
        <input name="q" type="text" value="{{ $q }}"
               class="w-full bg-surface-container pl-10 pr-4 py-2 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all"
               placeholder="Cari judul, pengarang, atau kode buku...">
    </form>

    <div class="bg-surface-container-low shadow-sm rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-surface-container-highest">
                <thead class="bg-surface-container">
                    <tr>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Kode</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant">Judul</th>
                        <th class="px-4 py-3 text-left font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant">Pengarang</th>
                        <th class="px-4 py-3 text-center font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Tahun</th>
                        <th class="px-4 py-3 text-center font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Stok</th>
                        <th class="px-4 py-3 text-right font-label-sm text-label-sm uppercase tracking-wide text-on-surface-variant whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-surface-container-highest">
                    @forelse ($books as $b)
                        <tr class="hover:bg-surface-container-low/60">
                            <td class="px-4 py-3 font-body-md text-on-surface-variant font-mono text-[12px] whitespace-nowrap">{{ $b->kode_buku }}</td>
                            <td class="px-4 py-3 min-w-[180px]">
                                <span class="font-title-sm text-title-sm text-on-surface">{{ $b->judul }}</span>
                                @if ($b->sinopsis)
                                    <p class="font-body-sm text-body-sm text-on-surface-variant line-clamp-1 max-w-md">{{ $b->sinopsis }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-body-md text-on-surface-variant min-w-[130px]">{{ $b->pengarang }}</td>
                            <td class="px-4 py-3 font-body-md text-on-surface-variant whitespace-nowrap text-center">{{ $b->tahun_terbit ?? '-' }}</td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm
                                    {{ $b->jumlah_stok > 0 ? 'bg-secondary-container text-on-secondary-fixed-variant' : 'bg-error-container text-on-error-container' }}">
                                    {{ $b->jumlah_stok }} eks
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                @if ($canManageBooks)
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                data-id="{{ $b->id }}"
                                                data-judul="{{ $b->judul }}"
                                                data-pengarang="{{ $b->pengarang }}"
                                                data-tahun_terbit="{{ $b->tahun_terbit }}"
                                                data-jumlah_stok="{{ $b->jumlah_stok }}"
                                                data-sinopsis="{{ $b->sinopsis }}"
                                                onclick="openEditFromBtn(this)"
                                                class="p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors" title="Edit">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                        <form method="POST" action="{{ route('admin.buku.destroy', $b->id) }}" onsubmit="return confirm('Yakin hapus buku ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/40 transition-colors" title="Hapus">
                                                <span class="material-symbols-outlined text-[20px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="font-body-sm text-on-surface-variant">Lihat</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center font-body-md text-on-surface-variant">
                                @if ($q)
                                    Tidak ada buku yang cocok dengan "{{ $q }}".
                                @elseif ($canManageBooks)
                                    Belum ada buku. <a href="{{ route('admin.buku.tambah') }}" class="text-primary font-semibold hover:underline">Tambah buku pertama</a>.
                                @else
                                    Belum ada buku.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($books->hasPages())
            <div class="px-4 py-3 border-t border-surface-container-highest">{{ $books->links() }}</div>
        @endif
    </div>
</div>

<div id="editModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg bg-surface rounded-lg shadow-xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-6 py-4 border-b border-surface-container-highest flex items-center justify-between shrink-0">
            <h2 class="font-title-lg text-title-lg text-on-surface">Edit Buku</h2>
            <button onclick="closeModal()" class="p-2 rounded-lg hover:bg-surface-container"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form id="editForm" method="POST" class="p-4 sm:p-6 flex flex-col gap-4 overflow-y-auto">
            @csrf
            @method('PUT')
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Judul <span class="text-error">*</span></label>
                <input name="judul" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Pengarang <span class="text-error">*</span></label>
                    <input name="pengarang" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="flex flex-col gap-2">
                    <label class="font-label-md text-on-surface">Tahun Terbit</label>
                    <input name="tahun_terbit" type="number" min="1000" max="2100" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Jumlah Stok <span class="text-error">*</span></label>
                <input name="jumlah_stok" type="number" min="0" required class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div class="flex flex-col gap-2">
                <label class="font-label-md text-on-surface">Sinopsis</label>
                <textarea name="sinopsis" rows="3" class="px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openEditFromBtn(btn){
    const d = btn.dataset;
    openEdit(d.id, d);
}
function openEdit(id, b){
    const form=document.getElementById('editForm');
    form.action='{{ url('admin/buku') }}/'+id;
    form.querySelector('[name=judul]').value=b.judul||'';
    form.querySelector('[name=pengarang]').value=b.pengarang||'';
    form.querySelector('[name=tahun_terbit]').value=b.tahun_terbit||'';
    form.querySelector('[name=jumlah_stok]').value=b.jumlah_stok??0;
    form.querySelector('[name=sinopsis]').value=b.sinopsis||'';
    const m=document.getElementById('editModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}
function closeModal(){
    const m=document.getElementById('editModal');
    m.classList.add('hidden'); m.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}
</script>
@endpush
@endsection
