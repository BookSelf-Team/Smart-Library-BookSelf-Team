@extends('layouts.siswa')

@section('title', 'Profil Saya — Bookself')

@section('content')
@php
    $user = auth()->user();
    $today = \Illuminate\Support\Carbon::now()->locale('id');
    $pctBaca = $statistik['target'] > 0 ? min(100, round($statistik['dibaca'] / $statistik['target'] * 100)) : 0;
    $inisial = collect(explode(' ', $siswa['nama']))->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('');
    $roleLabel = $user->roleLabel();
@endphp

<div class="px-4 sm:px-6 lg:px-10 py-6 sm:py-8 flex flex-col gap-6 sm:gap-9 max-w-[1560px] mx-auto w-full">
    <x-breadcrumb :items="[['label' => 'Profil Pengguna']]" />

    {{-- ================= NOTIFIKASI ================= --}}
    @if (session('success'))
        <div class="flex items-start gap-3 p-4 rounded-xl bg-secondary-container text-on-secondary-fixed-variant flash-in">
            <span class="material-symbols-outlined text-[22px]">check_circle</span>
            <div>
                <p class="font-title-sm text-title-sm">Berhasil</p>
                <p class="font-body-sm text-body-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col xl:flex-row xl:items-end justify-between gap-6">
        <div class="flex flex-col gap-1.5">
            <div class="flex items-center gap-3 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                    {{ $roleLabel }} • Akun Aktif
                </span>
                <span class="font-label-sm text-label-sm text-on-surface-variant flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                    {{ $today->translatedFormat('l, j F Y') }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-headline-md lg:text-headline-lg text-on-surface tracking-tight font-semibold">
                Profil <span class="text-primary italic">Saya</span>
            </h1>
            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
                Kelola data diri dan keamanan akun Anda. Pastikan informasi selalu terbaru agar peminjaman berjalan lancar.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ url('siswa/kartu') }}"
               class="h-11 px-4 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                <span class="material-symbols-outlined text-primary text-[20px]">badge</span>
                <span>Kartu Anggota</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="h-11 px-4 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)] active:scale-[0.98]">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">

        {{-- ================= KARTU PROFIL ================= --}}
        <div class="xl:col-span-4 flex flex-col gap-6">
            <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col items-center text-center gap-4">
                @if ($user->avatar)
                    <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $siswa['nama'] }}" class="w-24 h-24 rounded-full object-cover shadow-[0_8px_24px_-8px_rgba(35,45,19,0.45)]" />
                @else
                    <div class="w-24 h-24 rounded-full bg-primary flex items-center justify-center shadow-[0_8px_24px_-8px_rgba(35,45,19,0.45)]">
                        <span class="font-headline-lg text-headline-lg text-on-primary">{{ $inisial }}</span>
                    </div>
                @endif
                <div class="flex flex-col gap-1">
                    <h2 class="font-headline-sm text-headline-sm text-on-surface">{{ $siswa['nama'] }}</h2>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $user->email ?? 'Belum ada email terdaftar' }}</p>
                    <span class="mt-1 inline-flex self-center px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant font-label-sm text-label-sm">
                        {{ $roleLabel }} • Kelas {{ $siswa['kelas'] }}
                    </span>
                </div>
                <div class="w-full h-px bg-surface-container"></div>
                <div class="w-full flex flex-col">
                    <div class="flex items-center justify-between py-2">
                        <span class="font-body-sm text-body-sm text-on-surface-variant inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-outline">pin</span> NISN
                        </span>
                        <span class="font-title-sm text-title-sm font-mono text-on-surface">{{ $siswa['nisn'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-t border-surface-container">
                        <span class="font-body-sm text-body-sm text-on-surface-variant inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px] text-outline">school</span> Kelas
                        </span>
                        <span class="font-title-sm text-title-sm text-on-surface">{{ $siswa['kelas'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Statistik --}}
            <div class="p-4 sm:p-6 rounded-2xl bg-primary text-on-primary shadow-[0_8px_24px_-8px_rgba(35,45,19,0.35)] flex flex-col gap-5">
                <div class="flex items-center justify-between">
                    <span class="font-label-sm text-label-sm uppercase tracking-widest text-on-primary/80">Statistik Literasi</span>
                    <span class="material-symbols-outlined text-[22px]">auto_stories</span>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <p class="font-headline-sm text-headline-sm">{{ $statistik['aktif'] }}</p>
                        <p class="font-label-sm text-label-sm text-on-primary/75">Aktif</p>
                    </div>
                    <div>
                        <p class="font-headline-sm text-headline-sm">{{ $statistik['total_pinjam'] }}</p>
                        <p class="font-label-sm text-label-sm text-on-primary/75">Total pinjam</p>
                    </div>
                    <div>
                        <p class="font-headline-sm text-headline-sm">{{ $statistik['dibaca'] }}</p>
                        <p class="font-label-sm text-label-sm text-on-primary/75">Dibaca</p>
                    </div>
                </div>
                <div>
                    <div class="h-1.5 rounded-full bg-white/20"><div class="h-1.5 rounded-full bg-white" style="width: {{ $pctBaca }}%"></div></div>
                    <p class="font-label-sm text-label-sm text-on-primary/80 mt-1.5">Target literasi tahun ini {{ $pctBaca }}% tercapai</p>
                </div>
            </div>
        </div>

        {{-- ================= FORM ================= --}}
        <div class="xl:col-span-8 flex flex-col gap-6">

            {{-- Data diri --}}
            <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                        <span class="material-symbols-outlined text-[22px]">badge</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Data Diri</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Perbarui nama tampilan dan email akun Anda.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('siswa.profil.update') }}" enctype="multipart/form-data" class="flex flex-col gap-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label for="name" class="font-label-md text-label-md text-on-surface-variant">Nama Lengkap</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $siswa['nama']) }}" required
                                   class="w-full px-4 py-2.5 rounded-lg bg-surface-container-lowest font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary transition-all
                                          @error('name') ring-2 ring-error @enderror"/>
                            @error('name')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="email" class="font-label-md text-label-md text-on-surface-variant">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" placeholder="nama@sekolah.sch.id"
                                   class="w-full px-4 py-2.5 rounded-lg bg-surface-container-lowest font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary transition-all
                                          @error('email') ring-2 ring-error @enderror"/>
                            @error('email')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="nisn" class="font-label-md text-label-md text-on-surface-variant">NISN</label>
                            <input id="nisn" type="text" value="{{ $siswa['nisn'] }}" disabled
                                   class="w-full px-4 py-2.5 rounded-lg bg-surface-container font-body-md text-body-md text-on-surface-variant cursor-not-allowed"/>
                            <span class="font-label-sm text-label-sm text-outline">Hubungi pustakawan untuk perubahan NISN.</span>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="kelas" class="font-label-md text-label-md text-on-surface-variant">Kelas</label>
                            <input id="kelas" type="text" value="{{ $siswa['kelas'] }}" disabled
                                   class="w-full px-4 py-2.5 rounded-lg bg-surface-container font-body-md text-body-md text-on-surface-variant cursor-not-allowed"/>
                            <span class="font-label-sm text-label-sm text-outline">Dikelola oleh bagian akademik.</span>
                        </div>

                        <div class="md:col-span-2 flex flex-col gap-1.5">
                            <label for="avatar" class="font-label-md text-label-md text-on-surface-variant">Foto Profil <span class="text-xs text-on-surface-variant font-normal">(Opsional: JPG, PNG, WebP maks 2MB)</span></label>
                            <input id="avatar" name="avatar" type="file" accept="image/jpeg,image/png,image/webp"
                                   class="w-full px-4 py-2 rounded-lg bg-surface-container-lowest font-body-sm text-body-sm text-on-surface file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:font-label-md file:bg-primary file:text-on-primary hover:file:bg-primary/90 cursor-pointer border border-surface-container-high"/>
                            @error('avatar')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" data-loading-text="Menyimpan..."
                                class="h-11 px-5 rounded-lg bg-primary-container hover:bg-tertiary-container text-on-primary-container font-label-md text-label-md flex items-center gap-2 transition-all shadow-[0_2px_8px_-2px_rgba(35,45,19,0.14)] active:scale-[0.98]">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Keamanan --}}
            <div class="p-4 sm:p-6 rounded-2xl bg-surface-container-low flex flex-col gap-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-secondary-container flex items-center justify-center text-on-secondary-container">
                        <span class="material-symbols-outlined text-[22px]">lock</span>
                    </div>
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface">Keamanan Akun</h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">Ganti kata sandi secara berkala demi keamanan akun.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('siswa.profil.sandi') }}" class="flex flex-col gap-4">
                    @csrf
                    <div class="flex flex-col gap-1.5">
                        <label for="current_password" class="font-label-md text-label-md text-on-surface-variant">Kata Sandi Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" placeholder="••••••••" required
                               class="w-full px-4 py-2.5 rounded-lg bg-surface-container-lowest font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary transition-all
                                      @error('current_password') ring-2 ring-error @enderror"/>
                        @error('current_password')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label for="password" class="font-label-md text-label-md text-on-surface-variant">Kata Sandi Baru</label>
                            <input id="password" name="password" type="password" placeholder="Minimal 8 karakter" required
                                   class="w-full px-4 py-2.5 rounded-lg bg-surface-container-lowest font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary transition-all
                                          @error('password') ring-2 ring-error @enderror"/>
                            @error('password')<span class="font-label-sm text-label-sm text-error">{{ $message }}</span>@enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="password_confirmation" class="font-label-md text-label-md text-on-surface-variant">Konfirmasi Kata Sandi</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi kata sandi baru" required
                                   class="w-full px-4 py-2.5 rounded-lg bg-surface-container-lowest font-body-md text-body-md text-on-surface outline-none focus:ring-2 focus:ring-primary transition-all"/>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 flex-wrap">
                        <p class="font-label-sm text-label-sm text-on-surface-variant inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-[16px] text-outline">info</span>
                            Gunakan kombinasi huruf, angka, dan simbol.
                        </p>
                        <button type="submit"
                                class="h-11 px-5 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-label-md text-label-md flex items-center gap-2 transition-all shadow-sm">
                            <span class="material-symbols-outlined text-primary text-[18px]">key</span>
                            <span>Perbarui Kata Sandi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
