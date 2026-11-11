<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8"/><meta content="width=device-width, initial-scale=1.0" name="viewport"/><meta content="web_dashboard" name="shell-type"/><link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/><link href="https://fonts.googleapis.com" rel="preconnect"/><link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/><link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&amp;family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/><link rel="icon" type="image/png" href="{{ asset('images/logo-bookself.png') }}"/><style>@@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style><script src="https://cdn.tailwindcss.com"></script><script id="tailwind-config">tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        "colors": {
          "on-surface": "#1c1c16",
          "surface-container": "#f1eee4",
          "inverse-surface": "#31312a",
          "on-error-container": "#93000a",
          "on-tertiary": "#ffffff",
          "surface-dim": "#dddad0",
          "surface": "#fcf9ef",
          "error": "#ba1a1a",
          "surface-container-high": "#ebe8de",
          "on-tertiary-container": "#d6e9ab",
          "secondary-fixed": "#dbe8c0",
          "on-primary-fixed-variant": "#394d14",
          "on-background": "#1c1c16",
          "outline": "#75796b",
          "on-surface-variant": "#45483c",
          "error-container": "#ffdad6",
          "on-secondary-fixed": "#151e06",
          "on-secondary-container": "#5d6849",
          "surface-tint": "#50652a",
          "secondary-fixed-dim": "#bfcba6",
          "tertiary": "#425223",
          "primary-fixed": "#d2eca2",
          "inverse-primary": "#b6d088",
          "on-tertiary-fixed-variant": "#3d4c1e",
          "inverse-on-surface": "#f4f1e7",
          "on-secondary": "#ffffff",
          "on-primary": "#ffffff",
          "on-tertiary-fixed": "#141f00",
          "outline-variant": "#c5c8b8",
          "surface-bright": "#fcf9ef",
          "surface-container-lowest": "#ffffff",
          "secondary-container": "#dbe8c0",
          "surface-container-low": "#f6f4ea",
          "on-primary-fixed": "#131f00",
          "primary-fixed-dim": "#b6d088",
          "on-primary-container": "#d0eba1",
          "primary": "#3e5219",
          "on-secondary-fixed-variant": "#404b2e",
          "surface-container-highest": "#e5e2d9",
          "tertiary-fixed-dim": "#bbce92",
          "tertiary-container": "#596a38",
          "surface-variant": "#e5e2d9",
          "on-error": "#ffffff",
          "primary-container": "#556b2f",
          "tertiary-fixed": "#d7eaac",
          "secondary": "#576244",
          "background": "#fcf9ef"
        },
        "borderRadius": {
          "DEFAULT": "0.25rem",
          "lg": "0.5rem",
          "xl": "0.75rem",
          "full": "9999px"
        },
        "spacing": {
          "space-2xl": "4rem",
          "space-xl": "2.5rem",
          "space-md": "1rem",
          "space-sm": "0.5rem",
          "gutter-mobile": "0.75rem",
          "space-lg": "1.5rem",
          "margin": "2.5rem",
          "gutter": "1.5rem",
          "space-xs": "0.25rem",
          "margin-mobile": "1rem"
        },
        "fontFamily": {
          "headline-md": ["Newsreader"],
          "title-md": ["Plus Jakarta Sans"],
          "display-lg-mobile": ["Newsreader"],
          "body-md": ["Plus Jakarta Sans"],
          "title-sm": ["Plus Jakarta Sans"],
          "headline-lg-mobile": ["Newsreader"],
          "body-sm": ["Plus Jakarta Sans"],
          "headline-lg": ["Newsreader"],
          "display-lg": ["Newsreader"],
          "label-sm": ["Plus Jakarta Sans"],
          "headline-sm": ["Newsreader"],
          "label-lg": ["Plus Jakarta Sans"],
          "body-lg": ["Plus Jakarta Sans"],
          "label-md": ["Plus Jakarta Sans"],
          "title-lg": ["Plus Jakarta Sans"]
        },
        "fontSize": {
          "headline-md": ["28px", {"lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
          "title-md": ["16px", {"lineHeight": "24px", "fontWeight": "600"}],
          "display-lg-mobile": ["38px", {"lineHeight": "44px", "letterSpacing": "-0.01em", "fontWeight": "500"}],
          "body-md": ["14px", {"lineHeight": "22px", "fontWeight": "400"}],
          "title-sm": ["14px", {"lineHeight": "20px", "fontWeight": "600"}],
          "headline-lg-mobile": ["28px", {"lineHeight": "34px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
          "body-sm": ["12px", {"lineHeight": "18px", "fontWeight": "400"}],
          "headline-lg": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.015em", "fontWeight": "600"}],
          "display-lg": ["56px", {"lineHeight": "64px", "letterSpacing": "-0.02em", "fontWeight": "500"}],
          "label-sm": ["11px", {"lineHeight": "14px", "letterSpacing": "0.04em", "fontWeight": "600"}],
          "headline-sm": ["22px", {"lineHeight": "30px", "fontWeight": "600"}],
          "label-lg": ["14px", {"lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "500"}],
          "body-lg": ["16px", {"lineHeight": "26px", "fontWeight": "400"}],
          "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "500"}],
          "title-lg": ["20px", {"lineHeight": "28px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
        }
      }
    }
  }</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Bookself — Smart Archival')</title>
@stack('head')
@include('partials.animations')
</head>
<body class="bg-background font-body-md text-body-md text-on-surface antialiased selection:bg-secondary-container selection:text-on-secondary-container">

@php
    // Menu sidebar. Ganti 'path' dengan route milik Anda bila perlu.
    $navItems = [
        ['label' => 'Dashboard Ringkasan',    'icon' => 'auto_stories',    'path' => 'dashboard', 'match' => 'dashboard*'],
        ['label' => 'Sirkulasi & Peminjaman', 'icon' => 'sync_alt',        'path' => 'sirkulasi', 'match' => 'sirkulasi*'],
        ['label' => 'Katalog Buku',           'icon' => 'local_library',   'path' => 'katalog',   'match' => 'katalog*'],
        ['label' => 'Rekap Laporan & Denda',  'icon' => 'receipt_long',    'path' => 'laporan',   'match' => 'laporan*'],
        ['label' => 'Data Anggota & Pengguna','icon' => 'badge',           'path' => 'anggota',   'match' => 'anggota*'],
        ['label' => 'Pengaturan',             'icon' => 'settings_suggest','path' => 'pengaturan','match' => 'pengaturan*'],
    ];

    $authUser  = auth()->user();
    $userName  = $authUser->name ?? 'Nurul Hidayati';
    $roleLabel = $authUser?->roleLabel() ?? 'Pengguna';
@endphp

{{-- ================= MOBILE HEADER ================= --}}
<div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-surface/95 backdrop-blur-xl shadow-sm z-50 flex items-center justify-between px-4">
    <div class="flex items-center gap-2">
        <button id="appMobileMenuBtn" type="button" aria-label="Buka Menu" class="p-2 rounded-lg text-on-surface hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
        <div class="flex items-center gap-2">
            <img alt="Logo Bookself" class="h-6 w-auto object-contain" src="{{ asset('images/logo-bookself.png') }}"/>
            <span class="font-headline-sm text-headline-sm text-primary tracking-tight">Bookself</span>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
            <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
        </div>
    </div>
</div>

{{-- ================= MOBILE SIDEBAR ================= --}}
<aside id="appMobileSidebar" class="lg:hidden fixed inset-0 z-50 hidden" aria-modal="true" role="dialog">
    <div class="absolute inset-0 bg-black/50" id="appMobileOverlay"></div>
    <div class="absolute left-0 top-0 h-full w-72 max-w-[85vw] bg-surface-container-low shadow-2xl flex flex-col justify-between pt-5 pb-6 overflow-y-auto transform transition-transform duration-300">
        <div class="flex flex-col">
            <div class="px-6 flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <img alt="Logo Bookself" class="h-8 w-auto object-contain" src="{{ asset('images/logo-bookself.png') }}"/>
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm text-primary tracking-tight">Bookself</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest">Smart Archival</span>
                    </div>
                </div>
                <button id="closeAppMobileMenu" type="button" aria-label="Tutup Menu" class="p-2 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                    <span class="material-symbols-outlined text-[24px]">close</span>
                </button>
            </div>

            <div class="px-4 py-2">
                <p class="px-3 mb-2 font-label-sm text-label-sm uppercase tracking-wider text-outline">Sistem Utama</p>
                <nav class="flex flex-col gap-1">
                    @foreach ($navItems as $item)
                        @php($isActive = request()->is($item['match']))
                        <a href="{{ url($item['path']) }}"
                           @if ($isActive) aria-current="page" @endif
                           class="flex items-center gap-3 px-3.5 py-2.5 transition-all duration-150 font-title-sm text-title-sm rounded-lg
                                  {{ $isActive
                                        ? 'bg-primary-container text-on-primary-container shadow-sm'
                                        : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                            <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>{{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <div class="p-4 mx-4 mb-5 rounded-xl bg-surface-container flex flex-col gap-2 mt-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <span class="font-label-md text-label-md text-on-surface font-semibold">RFID Reader Act</span>
                </div>
                <span class="material-symbols-outlined text-[18px] text-primary">sensors</span>
            </div>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Port USB-3 terhubung. Sensor pemindai rak siap.</p>
        </div>
    </div>
</aside>

{{-- ================= DESKTOP SIDEBAR ================= --}}
<aside class="hidden lg:flex fixed left-0 top-0 h-screen w-72 bg-surface-container-low z-50 flex-col justify-between shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
    <div class="flex flex-col">
        <div class="h-16 px-6 flex items-center gap-3">
            <img alt="Logo Bookself" class="h-8 w-auto object-contain" src="{{ asset('images/logo-bookself.png') }}"/>
            <div class="flex flex-col">
                <span class="font-headline-sm text-headline-sm text-primary tracking-tight">Bookself</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest">Smart Archival</span>
            </div>
        </div>

        <div class="px-4 py-2">
            <p class="px-3 mb-2 font-label-sm text-label-sm uppercase tracking-wider text-outline">Sistem Utama</p>
            <nav class="flex flex-col gap-1">
                @foreach ($navItems as $item)
                    @php($isActive = request()->is($item['match']))
                    <a href="{{ url($item['path']) }}"
                       @if ($isActive) aria-current="page" @endif
                       class="flex items-center gap-3 px-3.5 py-2.5 transition-all duration-150 font-title-sm text-title-sm rounded-lg
                              {{ $isActive
                                    ? 'bg-primary-container text-on-primary-container shadow-sm'
                                    : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>{{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="p-4 mx-4 mb-5 rounded-xl bg-surface-container flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                </span>
                <span class="font-label-md text-label-md text-on-surface font-semibold">RFID Reader Act</span>
            </div>
            <span class="material-symbols-outlined text-[18px] text-primary">sensors</span>
        </div>
        <p class="font-body-sm text-body-sm text-on-surface-variant">Port USB-3 terhubung. Sensor pemindai rak siap.</p>
    </div>
</aside>

<div class="lg:pl-72 pt-16 lg:pt-0">
    {{-- ================= TOPBAR ================= --}}
    <header class="hidden lg:block fixed top-0 left-72 right-0 h-16 bg-surface/85 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-40">
        <div class="h-16 w-full px-6 xl:px-8 flex items-center justify-between gap-6">
            <div class="flex-1 max-w-lg">
                <form method="GET" action="{{ url('katalog') }}" class="relative flex items-center">
                    <span class="material-symbols-outlined absolute left-3 text-[20px] text-outline pointer-events-none">search</span>
                    <input name="q" value="{{ request('q') }}" type="text"
                           class="w-full pl-10 pr-12 py-2 rounded-lg bg-surface-container font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/60 focus:bg-surface-container-lowest focus:outline-none transition-all"
                           placeholder="Cari judul buku, nomor panggil, atau ID pustaka..."/>
                    <kbd class="absolute right-3 px-1.5 py-0.5 rounded bg-surface-container-high font-label-sm text-label-sm text-outline select-none">⌘K</kbd>
                </form>
            </div>

            <div class="flex items-center gap-4">
                <button type="button" id="btnScanRfid"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-lg bg-primary-container text-on-primary font-label-lg text-label-lg hover:bg-primary transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">barcode_scanner</span>
                    <span>Scan Cepat RFID</span>
                </button>

                <button type="button" class="relative p-2 rounded-lg text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-error"></span>
                </button>

                <div class="h-6 w-[1px] bg-outline-variant"></div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:flex flex-col">
                        <span class="font-title-sm text-title-sm text-on-surface leading-tight max-w-[150px] truncate">{{ $userName }}</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant leading-tight">{{ $roleLabel }}</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- ================= KONTEN ================= --}}
    <main class="w-full lg:pt-16 bg-background min-h-screen">
        <div class="flex flex-col w-full">
            @yield('content')
        </div>
    </main>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const appMobileMenuBtn = document.getElementById('appMobileMenuBtn');
        const closeAppMobileMenu = document.getElementById('closeAppMobileMenu');
        const appMobileSidebar = document.getElementById('appMobileSidebar');
        const appMobileOverlay = document.getElementById('appMobileOverlay');

        function openAppMenu() {
            if (appMobileSidebar) {
                appMobileSidebar.classList.remove('hidden');
            }
        }

        function closeAppMenu() {
            if (appMobileSidebar) {
                appMobileSidebar.classList.add('hidden');
            }
        }

        if (appMobileMenuBtn) {
            appMobileMenuBtn.addEventListener('click', openAppMenu);
        }
        if (closeAppMobileMenu) {
            closeAppMobileMenu.addEventListener('click', closeAppMenu);
        }
        if (appMobileOverlay) {
            appMobileOverlay.addEventListener('click', closeAppMenu);
        }
    });
</script>

@include('partials.loading-script')
@stack('scripts')
</body>
</html>
