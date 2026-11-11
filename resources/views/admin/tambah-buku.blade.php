@extends('layouts.admin')

@section('title', 'Tambah Buku — Bookself')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex flex-col gap-6">
    <x-breadcrumb :items="[['label' => 'Koleksi Buku', 'url' => route('admin.buku.index')], ['label' => 'Tambah Buku']]" />
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant">Koleksi</span>
            <h1 class="font-headline-md text-headline-md text-on-surface font-semibold">Tambah Buku Baru</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Input judul baru ke katalog perpustakaan</p>
        </div>
        <a href="{{ route('admin.buku.index') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-container text-on-surface hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            <span class="font-title-sm text-title-sm">Kembali ke Data Buku</span>
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-error-container text-on-error-container px-4 py-3 rounded-lg font-body-md shadow-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.buku.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8 max-w-5xl">
        @csrf

        <!-- Kolom Kiri: Ilustrasi -->
        <div class="lg:col-span-1 flex flex-col gap-4">
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-surface-container flex flex-col items-center justify-center text-center">
                <div class="w-48 h-64 bg-surface-container rounded-lg border-2 border-dashed border-outline/50 flex flex-col items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-4xl text-outline mb-2">auto_stories</span>
                    <span class="text-sm font-semibold text-on-surface-variant">Kartu Koleksi</span>
                    <span class="text-xs text-on-surface-variant/70 mt-1">Kode dibuat otomatis</span>
                </div>
                <h3 class="font-bold text-on-surface">Visual Koleksi</h3>
                <p class="text-xs text-on-surface-variant mt-1">Nomor koleksi (kode buku) dibuat otomatis oleh sistem saat disimpan.</p>
            </div>
        </div>

        <!-- Kolom Kanan: Form Input Data -->
        <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl p-5 sm:p-8 shadow-sm border border-surface-container">
            <h2 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined">menu_book</span> Informasi Pustaka
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Judul Buku (Full Width) -->
                <div class="md:col-span-2 flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface">Judul Buku <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" maxlength="150" required
                           placeholder="Contoh: Bumi Manusia"
                           class="w-full bg-surface-container pl-4 pr-4 py-2.5 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/50 border border-transparent focus:border-primary transition-all">
                </div>

                <!-- Pengarang -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface">Pengarang <span class="text-red-500">*</span></label>
                    <input type="text" name="pengarang" value="{{ old('pengarang') }}" maxlength="100" required
                           placeholder="Contoh: Pramoedya Ananta Toer"
                           class="w-full bg-surface-container pl-4 pr-4 py-2.5 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/50 border border-transparent focus:border-primary transition-all">
                </div>

                <!-- Tahun Terbit -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" value="{{ old('tahun_terbit') }}" min="1000" max="2100" required
                           placeholder="Contoh: 2019"
                           class="w-full bg-surface-container pl-4 pr-4 py-2.5 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/50 border border-transparent focus:border-primary transition-all">
                </div>

                <!-- Jumlah Stok -->
                <div class="md:col-span-2 flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface">Jumlah Stok <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_stok" value="{{ old('jumlah_stok', 1) }}" min="0" required
                           placeholder="0"
                           class="w-full bg-surface-container pl-4 pr-4 py-2.5 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/50 border border-transparent focus:border-primary transition-all">
                </div>

                <!-- Sinopsis (Full Width) -->
                <div class="md:col-span-2 flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface">Sinopsis</label>
                    <textarea name="sinopsis" rows="4"
                              placeholder="Ringkasan singkat isi buku..."
                              class="w-full bg-surface-container pl-4 pr-4 py-2.5 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary/50 border border-transparent focus:border-primary transition-all resize-y">{{ old('sinopsis') }}</textarea>
                </div>

                <!-- Unggah Sampul Buku (Full Width) -->
                <div class="md:col-span-2 flex flex-col gap-1.5">
                    <label class="text-sm font-semibold text-on-surface">Unggah Sampul Buku <span class="text-xs text-on-surface-variant font-normal">(Opsional)</span></label>
                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp"
                           class="w-full bg-surface-container px-4 py-2 rounded-lg text-sm outline-none border border-transparent file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-semibold file:bg-primary file:text-on-primary hover:file:bg-primary/90 cursor-pointer">
                    <span class="text-xs text-on-surface-variant">Format aman yang didukung: JPG, PNG, WebP (maksimal 2MB).</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-surface-container my-8"></div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="{{ route('admin.buku.index') }}" class="px-6 py-2.5 rounded-lg text-sm font-semibold text-center text-on-surface-variant hover:bg-surface-container transition-colors">
                    Batal
                </a>
                <button type="submit" data-loading-text="Menyimpan Katalog..." class="px-6 py-2.5 rounded-lg text-sm font-semibold bg-primary text-on-primary hover:bg-primary/90 shadow-md flex items-center justify-center gap-2 transition-all active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">save</span> Simpan Katalog
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
