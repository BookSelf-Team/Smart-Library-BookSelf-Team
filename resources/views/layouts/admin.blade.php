<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"><meta content="width=device-width, initial-scale=1.0" name="viewport"><meta content="web_dashboard" name="shell-type"><link href="https://fonts.googleapis.com" rel="preconnect"><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"><link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"><link rel="icon" type="image/png" href="{{ asset('images/logo-bookself.png') }}"><style>@@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config={darkMode:"class",theme:{extend:{colors:{"on-secondary":"#ffffff","secondary-container":"#dbe8c0","on-primary-fixed":"#131f00","inverse-surface":"#31312a","inverse-primary":"#b6d088","on-error-container":"#93000a","on-secondary-fixed-variant":"#404b2e","on-secondary-fixed":"#151e06","inverse-on-surface":"#f4f1e7","outline-variant":"#c5c8b8","error-container":"#ffdad6","tertiary-fixed":"#d7eaac","on-primary":"#ffffff","surface-container":"#f1eee4","surface-variant":"#e5e2d9","on-background":"#1c1c16","background":"#fcf9ef","on-surface":"#1c1c16","on-tertiary-container":"#d6e9ab","secondary":"#576244","on-secondary-container":"#5d6849","primary-fixed-dim":"#b6d088","outline":"#75796b","secondary-fixed-dim":"#bfcba6","surface-container-lowest":"#ffffff","surface-bright":"#fcf9ef","surface-container-high":"#ebe8de","on-error":"#ffffff","on-tertiary":"#ffffff","primary-container":"#556b2f","primary-fixed":"#d2eca2","tertiary-container":"#596a38","surface-container-low":"#f6f4ea","on-tertiary-fixed-variant":"#3d4c1e","on-primary-container":"#d0eba1","error":"#ba1a1a","on-tertiary-fixed":"#141f00","on-primary-fixed-variant":"#394d14","surface-tint":"#50652a","on-surface-variant":"#45483c","tertiary-fixed-dim":"#bbce92","secondary-fixed":"#dbe8c0","tertiary":"#425223","primary":"#3e5219","surface":"#fcf9ef","surface-container-highest":"#e5e2d9","surface-dim":"#dddad0"},borderRadius:{"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},spacing:{"gutter-mobile":"0.75rem","gutter":"1.5rem","space-2xl":"4rem","space-md":"1rem","space-lg":"1.5rem","space-xl":"2.5rem","margin":"2.5rem","space-sm":"0.5rem","margin-mobile":"1rem","space-xs":"0.25rem"},fontFamily:{"label-md":["Plus Jakarta Sans"],"body-sm":["Plus Jakarta Sans"],"display-lg-mobile":["Newsreader"],"body-md":["Plus Jakarta Sans"],"body-lg":["Plus Jakarta Sans"],"headline-lg-mobile":["Newsreader"],"headline-md":["Newsreader"],"title-md":["Plus Jakarta Sans"],"title-lg":["Plus Jakarta Sans"],"label-sm":["Plus Jakarta Sans"],"headline-sm":["Newsreader"],"display-lg":["Newsreader"],"title-sm":["Plus Jakarta Sans"],"label-lg":["Plus Jakarta Sans"],"headline-lg":["Newsreader"]},fontSize:{"label-md":["12px",{"lineHeight":"16px","letterSpacing":"0.02em","fontWeight":"500"}],"body-sm":["12px",{"lineHeight":"18px","fontWeight":"400"}],"display-lg-mobile":["38px",{"lineHeight":"44px","letterSpacing":"-0.01em","fontWeight":"500"}],"body-md":["14px",{"lineHeight":"22px","fontWeight":"400"}],"body-lg":["16px",{"lineHeight":"26px","fontWeight":"400"}],"headline-lg-mobile":["28px",{"lineHeight":"34px","letterSpacing":"-0.01em","fontWeight":"600"}],"headline-md":["28px",{"lineHeight":"36px","letterSpacing":"-0.01em","fontWeight":"600"}],"title-md":["16px",{"lineHeight":"24px","fontWeight":"600"}],"title-lg":["20px",{"lineHeight":"28px","letterSpacing":"-0.01em","fontWeight":"600"}],"label-sm":["11px",{"lineHeight":"14px","letterSpacing":"0.04em","fontWeight":"600"}],"headline-sm":["22px",{"lineHeight":"30px","fontWeight":"600"}],"display-lg":["56px",{"lineHeight":"64px","letterSpacing":"-0.02em","fontWeight":"500"}],"title-sm":["14px",{"lineHeight":"20px","fontWeight":"600"}],"label-lg":["14px",{"lineHeight":"20px","letterSpacing":"0.01em","fontWeight":"500"}],"headline-lg":["36px",{"lineHeight":"44px","letterSpacing":"-0.015em","fontWeight":"600"}]}}}}</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Dashboard Admin — Bookself')</title>
@stack('head')
@include('partials.animations')
</head>
<body class="bg-surface font-body-md text-body-md text-on-surface antialiased">

@php
    $authUser = auth()->user();
    $userName = $authUser->name ?? 'Admin';
    $roleLabel = $authUser?->roleLabel() ?? 'Administrator';

    $isPetugas = $authUser?->roleEnum() === \App\Enums\Role::Petugas;

    $navItems = array_values(array_filter([
        ['label' => 'Dashboard',      'icon' => 'space_dashboard', 'path' => $isPetugas ? 'petugas/dashboard' : 'admin/dashboard', 'match' => $isPetugas ? 'petugas/dashboard*' : 'admin/dashboard*', 'roles' => ['admin', 'pustakawan', 'petugas']],
        ['label' => 'Data Buku',      'icon' => 'menu_book',       'path' => 'admin/buku',      'match' => 'admin/buku',        'roles' => ['admin', 'pustakawan', 'petugas']],
        ['label' => 'Tambah Buku',    'icon' => 'add_box',         'path' => 'admin/buku/tambah', 'match' => 'admin/buku/tambah', 'roles' => ['admin', 'pustakawan']],
        ['label' => 'Peminjaman',     'icon' => 'book',            'path' => 'admin/peminjaman', 'match' => 'admin/peminjaman*', 'roles' => ['admin', 'pustakawan', 'petugas']],
        ['label' => 'Pengembalian',   'icon' => 'assignment_return', 'path' => 'admin/pengembalian', 'match' => 'admin/pengembalian*', 'roles' => ['admin', 'pustakawan', 'petugas']],
        ['label' => 'Kelola Pengguna','icon' => 'group',           'path' => 'admin/users',       'match' => 'admin/users*',     'roles' => ['admin']],
        ['label' => 'Pengaturan',     'icon' => 'tune',            'path' => 'admin/pengaturan',  'match' => 'admin/pengaturan*', 'roles' => ['admin']],
        ['label' => 'Petugas',        'icon' => 'badge',           'path' => 'admin/petugas',     'match' => 'admin/petugas*',   'roles' => ['admin', 'pustakawan']],
        ['label' => 'Katalog',        'icon' => 'library_books',   'path' => 'katalog',           'match' => 'katalog*',         'roles' => ['admin', 'pustakawan', 'petugas', 'guru', 'siswa', 'user']],
    ], fn (array $item): bool => $authUser?->hasAnyRole($item['roles']) ?? false));
@endphp

{{-- ================= MOBILE HEADER ================= --}}
<div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-surface/95 backdrop-blur-xl shadow-sm z-50 flex items-center justify-between px-4">
    <div class="flex items-center gap-2">
        <button id="adminMobileMenuBtn" type="button" aria-label="Buka Menu" class="p-2 rounded-lg text-on-surface hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        <div class="flex items-center gap-2">
            <img alt="Logo Bookself" class="h-6 w-auto object-contain" src="{{ asset('images/logo-bookself.png') }}">
            <span class="font-headline-sm text-headline-sm font-bold text-primary">Bookself</span>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-2 px-2 py-1 rounded-full bg-surface-container">
            <div class="w-7 h-7 rounded-full bg-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-on-primary text-[16px]">person</span>
            </div>
            <span class="font-label-sm text-label-sm font-semibold text-on-surface max-w-[100px] truncate sm:max-w-[150px]">{{ $userName }}</span>
        </div>
    </div>
</div>

{{-- ================= MOBILE SIDEBAR ================= --}}
<aside id="adminMobileSidebar" class="lg:hidden fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50" id="adminMobileOverlay"></div>
    <div class="absolute left-0 top-0 h-full w-72 max-w-[85vw] bg-surface-container-low shadow-2xl flex flex-col justify-between pt-5 pb-6 overflow-y-auto transform transition-transform duration-300">
        <div class="flex flex-col gap-6">
            <div class="px-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img alt="Logo Bookself" class="h-8 w-auto object-contain" src="{{ asset('images/logo-bookself.png') }}">
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Bookself</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Smart Library</span>
                    </div>
                </div>
                <button id="closeAdminMobileMenu" type="button" aria-label="Tutup Menu" class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            <div class="px-4">
                <div class="bg-surface-container rounded-lg p-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                        <span class="font-label-sm text-label-sm text-on-surface font-medium">Portal Admin</span>
                    </div>
                    <span class="font-label-sm text-label-sm px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant">Aktif</span>
                </div>
            </div>

            <nav class="flex flex-col gap-1 px-4">
                @foreach ($navItems as $item)
                    @php($isActive = request()->is($item['match']))
                    <a href="{{ url($item['path']) }}" @if ($isActive) aria-current="page" @endif
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all
                              {{ $isActive
                                    ? 'bg-primary-container text-on-primary-container font-semibold shadow-[0_2px_8px_-2px_rgba(35,45,19,0.12)]'
                                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                        <span class="font-title-sm text-title-sm">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        <div class="px-4 flex flex-col gap-3 mt-6">
            <div class="bg-surface-container-highest p-3 rounded-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">schedule</span>
                <div class="flex flex-col">
                    <span class="font-label-sm text-label-sm text-on-surface font-semibold">Jam Layanan</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Senin–Jumat • 07.30 – 15.00 WIB</span>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-lg bg-surface-container text-on-surface-variant hover:bg-error-container hover:text-on-error-container font-title-sm text-title-sm transition-colors">
                    <span class="material-symbols-outlined text-[20px]">logout</span>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </div>
</aside>

{{-- ================= DESKTOP SIDEBAR ================= --}}
<aside class="hidden lg:flex fixed left-0 top-0 h-full w-72 bg-surface-container-low shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex-col justify-between pt-5 pb-6">
    <div class="flex flex-col gap-6">
        <div class="px-6 flex items-center gap-3">
            <img alt="Logo Bookself" class="h-8 w-auto object-contain" src="{{ asset('images/logo-bookself.png') }}">
            <div class="flex flex-col">
                <span class="font-headline-sm text-headline-sm font-bold text-primary tracking-tight">Bookself</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Smart Library</span>
            </div>
        </div>

        <div class="px-4">
            <div class="bg-surface-container rounded-lg p-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                    <span class="font-label-sm text-label-sm text-on-surface font-medium">Portal Admin</span>
                </div>
                <span class="font-label-sm text-label-sm px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-fixed-variant">Aktif</span>
            </div>
        </div>

        <nav class="flex flex-col gap-1 px-4">
            @foreach ($navItems as $item)
                @php($isActive = request()->is($item['match']))
                <a href="{{ url($item['path']) }}" @if ($isActive) aria-current="page" @endif
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg transition-all
                          {{ $isActive
                                ? 'bg-primary-container text-on-primary-container font-semibold shadow-[0_2px_8px_-2px_rgba(35,45,19,0.12)]'
                                : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                    <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                    <span class="font-title-sm text-title-sm">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <div class="px-4 flex flex-col gap-3">
        <div class="bg-surface-container-highest p-3 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-[20px]">schedule</span>
            <div class="flex flex-col">
                <span class="font-label-sm text-label-sm text-on-surface font-semibold">Jam Layanan</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Senin–Jumat • 07.30 – 15.00 WIB</span>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-3.5 py-2.5 rounded-lg bg-surface-container text-on-surface-variant hover:bg-error-container hover:text-on-error-container font-title-sm text-title-sm transition-colors">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

<div class="lg:pl-72 pt-16 lg:pt-0">
    {{-- ================= TOPBAR ================= --}}
    <header class="hidden lg:flex fixed top-0 left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40 items-center justify-between px-6 xl:px-8">
        <form method="GET" action="{{ url('katalog') }}" class="relative w-full max-w-lg">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-[20px]">search</span>
            <input name="q" type="text" value="{{ request('q') }}"
                   class="w-full bg-surface-container pl-10 pr-4 py-2 rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-on-surface-variant outline-none focus:bg-surface-container-lowest focus:ring-2 focus:ring-primary transition-all"
                   placeholder="Cari judul, pengarang, atau ISBN buku...">
        </form>

        <div class="flex items-center gap-3">
            <button type="button" class="relative p-2 rounded-lg text-on-surface-variant hover:text-on-surface hover:bg-surface-container transition-colors">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                @if (!empty($notifications))
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error"></span>
                @endif
            </button>

            <div class="flex items-center gap-2.5 pl-2 py-1 pr-4 rounded-full bg-surface-container">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-label-sm text-label-sm font-semibold text-on-surface leading-tight max-w-[150px] truncate">{{ $userName }}</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant text-[10px] leading-tight">{{ $roleLabel }}</span>
                </div>
            </div>
        </div>
    </header>

    <main class="w-full lg:pt-16 bg-surface min-h-screen">
        @yield('content')
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adminMobileMenuBtn = document.getElementById('adminMobileMenuBtn');
        const closeAdminMobileMenu = document.getElementById('closeAdminMobileMenu');
        const adminMobileSidebar = document.getElementById('adminMobileSidebar');
        const adminMobileOverlay = document.getElementById('adminMobileOverlay');

        function openAdminMenu() {
            if (adminMobileSidebar) {
                adminMobileSidebar.classList.remove('hidden');
            }
        }

        function closeAdminMenu() {
            if (adminMobileSidebar) {
                adminMobileSidebar.classList.add('hidden');
            }
        }

        if (adminMobileMenuBtn) {
            adminMobileMenuBtn.addEventListener('click', openAdminMenu);
        }
        if (closeAdminMobileMenu) {
            closeAdminMobileMenu.addEventListener('click', closeAdminMenu);
        }
        if (adminMobileOverlay) {
            adminMobileOverlay.addEventListener('click', closeAdminMenu);
        }
    });
</script>

@include('partials.loading-script')
@stack('scripts')
</body>
</html>
