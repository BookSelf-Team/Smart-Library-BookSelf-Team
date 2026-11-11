@extends('layouts.admin')

@section('title', 'Pengaturan Sistem — Bookself')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col gap-6 max-w-3xl">
    <x-breadcrumb :items="[['label' => 'Pengaturan Sistem']]" />
    <div class="flex flex-col gap-1">
        <span class="font-label-sm text-label-sm text-on-surface-variant">Pengaturan</span>
        <h1 class="font-headline-md text-headline-md text-on-surface font-semibold">Pengaturan Sistem</h1>
        <p class="font-body-md text-body-md text-on-surface-variant">Atur parameter operasional perpustakaan yang diterapkan otomatis oleh sistem</p>
    </div>

    @if (session('success'))
        <div class="bg-primary-container text-on-primary-container px-4 py-3 rounded-lg font-body-md shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-body-md shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-surface-container-low shadow-sm rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('admin.pengaturan.update') }}" class="p-4 sm:p-6 flex flex-col gap-5">
            @csrf
            @method('PUT')

            <div class="flex flex-col gap-1">
                <h2 class="font-title-lg text-title-lg text-on-surface">Tarif Denda Keterlambatan</h2>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Denda dihitung otomatis: jumlah hari terlambat × tarif per hari. Nilai
                    <span class="font-semibold">0</span> berarti denda dimatikan — tampilan denda di seluruh halaman akan bernilai Rp 0.
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <label for="denda_per_hari" class="font-label-md text-on-surface">Denda per hari (Rp)</label>
                <div class="flex items-center gap-2">
                    <span class="font-title-md text-title-md text-on-surface-variant px-3 py-2 rounded-lg bg-surface-container border border-outline/20">Rp</span>
                    <input id="denda_per_hari" name="denda_per_hari" type="number" min="0" max="1000000" step="100" required
                           value="{{ old('denda_per_hari', $dendaPerHari) }}"
                           class="w-full px-3 py-2 rounded-lg bg-surface-container border border-outline/20 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                @error('denda_per_hari')
                    <p class="font-body-sm text-body-sm text-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="bg-surface-container-highest rounded-lg px-4 py-3 flex items-start gap-2">
                <span class="material-symbols-outlined text-primary text-[20px] mt-0.5" aria-hidden="true">info</span>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Perubahan berlaku otomatis untuk semua pengembalian berikutnya dan tampilan denda di halaman petugas maupun anggota.
                </p>
            </div>

            <div class="flex items-center justify-end gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-on-primary hover:bg-primary/90 transition-colors font-title-sm text-title-sm">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
