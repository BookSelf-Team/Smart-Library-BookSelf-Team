<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — Bookself Smart Archival &amp; Library</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logo-bookself.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,400..700;1,6..72,400..700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        @@layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main > :first-child { margin-top: 0 !important; }
            main > :last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
    </style>

    {{-- Tailwind CDN (untuk produksi, ganti dengan Vite: @vite(['resources/css/app.css','resources/js/app.js'])) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-tertiary-container": "#d6e9ab", "tertiary-fixed-dim": "#bbce92", "primary-fixed-dim": "#b6d088",
                        "secondary-fixed": "#dbe8c0", "surface-dim": "#dddad0", "on-error": "#ffffff",
                        "on-surface-variant": "#45483c", "on-tertiary-fixed": "#141f00", "on-secondary-fixed-variant": "#404b2e",
                        "on-primary-container": "#d0eba1", "outline": "#75796b", "surface-container-highest": "#e5e2d9",
                        "tertiary": "#425223", "surface-container-high": "#ebe8de", "inverse-surface": "#31312a",
                        "on-background": "#1c1c16", "on-secondary": "#ffffff", "on-error-container": "#93000a",
                        "secondary": "#576244", "background": "#fcf9ef", "tertiary-container": "#596a38",
                        "error": "#ba1a1a", "error-container": "#ffdad6", "surface": "#fcf9ef",
                        "on-primary-fixed-variant": "#394d14", "outline-variant": "#c5c8b8", "secondary-fixed-dim": "#bfcba6",
                        "on-primary-fixed": "#131f00", "primary": "#3e5219", "inverse-primary": "#b6d088",
                        "on-secondary-container": "#5d6849", "surface-container": "#f1eee4", "inverse-on-surface": "#f4f1e7",
                        "surface-tint": "#50652a", "secondary-container": "#dbe8c0", "surface-bright": "#fcf9ef",
                        "tertiary-fixed": "#d7eaac", "surface-variant": "#e5e2d9", "on-tertiary-fixed-variant": "#3d4c1e",
                        "on-surface": "#1c1c16", "on-primary": "#ffffff", "primary-fixed": "#d2eca2",
                        "surface-container-low": "#f6f4ea", "primary-container": "#556b2f", "on-tertiary": "#ffffff",
                        "surface-container-lowest": "#ffffff"
                    },
                    borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                    spacing: {
                        "space-2xl": "4rem", "space-lg": "1.5rem", "space-xs": "0.25rem", "margin-mobile": "1rem",
                        "space-xl": "2.5rem", "gutter": "1.5rem", "gutter-mobile": "0.75rem", "space-md": "1rem",
                        "space-sm": "0.5rem", "margin": "2.5rem"
                    },
                    fontFamily: {
                        "headline-lg": ["Newsreader"], "body-md": ["Plus Jakarta Sans"], "headline-sm": ["Newsreader"],
                        "headline-lg-mobile": ["Newsreader"], "display-lg-mobile": ["Newsreader"], "headline-md": ["Newsreader"],
                        "label-md": ["Plus Jakarta Sans"], "title-md": ["Plus Jakarta Sans"], "body-sm": ["Plus Jakarta Sans"],
                        "title-sm": ["Plus Jakarta Sans"], "display-lg": ["Newsreader"], "label-lg": ["Plus Jakarta Sans"],
                        "title-lg": ["Plus Jakarta Sans"], "label-sm": ["Plus Jakarta Sans"], "body-lg": ["Plus Jakarta Sans"]
                    },
                    fontSize: {
                        "headline-lg": ["36px", { lineHeight: "44px", letterSpacing: "-0.015em", fontWeight: "600" }],
                        "body-md": ["14px", { lineHeight: "22px", fontWeight: "400" }],
                        "headline-sm": ["22px", { lineHeight: "30px", fontWeight: "600" }],
                        "headline-lg-mobile": ["28px", { lineHeight: "34px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "display-lg-mobile": ["38px", { lineHeight: "44px", letterSpacing: "-0.01em", fontWeight: "500" }],
                        "headline-md": ["28px", { lineHeight: "36px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "label-md": ["12px", { lineHeight: "16px", letterSpacing: "0.02em", fontWeight: "500" }],
                        "title-md": ["16px", { lineHeight: "24px", fontWeight: "600" }],
                        "body-sm": ["12px", { lineHeight: "18px", fontWeight: "400" }],
                        "title-sm": ["14px", { lineHeight: "20px", fontWeight: "600" }],
                        "display-lg": ["56px", { lineHeight: "64px", letterSpacing: "-0.02em", fontWeight: "500" }],
                        "label-lg": ["14px", { lineHeight: "20px", letterSpacing: "0.01em", fontWeight: "500" }],
                        "title-lg": ["20px", { lineHeight: "28px", letterSpacing: "-0.01em", fontWeight: "600" }],
                        "label-sm": ["11px", { lineHeight: "14px", letterSpacing: "0.04em", fontWeight: "600" }],
                        "body-lg": ["16px", { lineHeight: "26px", fontWeight: "400" }]
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-background font-body-md text-on-surface antialiased min-h-screen flex flex-col justify-between">

    {{-- ================= HEADER ================= --}}
    <header class="fixed top-0 w-full z-50 bg-surface/80 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="h-20 w-full px-4 sm:px-6 lg:px-margin flex items-center justify-between">
            <div class="flex items-center gap-space-sm sm:gap-space-md">
                <img alt="Logo Bookself" class="h-8 w-auto object-contain"
                     src="{{ asset('images/logo-bookself.png') }}" />
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary leading-none">Bookself</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant tracking-wider uppercase hidden sm:block">Smart Archival &amp; Library</span>
                </div>
                <div class="hidden lg:flex items-center ml-space-md pl-space-md bg-surface-container-high rounded-full px-space-md py-space-xs gap-space-xs">
                    <span class="material-symbols-outlined text-primary text-[16px]">verified_user</span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">SSL 256-bit • SSO Enforced</span>
                </div>
            </div>

            <nav class="hidden md:flex items-center gap-space-md">
                <a aria-current="page" href="{{ route('login') }}"
                   class="transition-colors bg-primary-container text-on-primary-container font-title-sm text-title-sm px-space-md py-space-xs rounded-lg">Sign In</a>
                <a href="#" class="text-on-surface-variant hover:text-on-surface transition-colors font-title-sm text-title-sm">Recover Account</a>
                <a href="#" class="text-on-surface-variant hover:text-on-surface transition-colors font-title-sm text-title-sm">Security Token</a>
            </nav>

            <div class="flex items-center gap-2 sm:gap-space-md">
                <div class="flex items-center bg-surface-container rounded-lg p-1">
                    <button type="button" class="px-space-sm py-0.5 rounded font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface transition-colors">EN</button>
                    <button type="button" class="px-space-sm py-0.5 rounded font-label-sm text-label-sm text-on-surface bg-surface shadow-[0_1px_4px_rgba(0,0,0,0.05)]">ID</button>
                </div>
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                </div>
            </div>
        </div>
    </header>

    {{-- ================= MAIN ================= --}}
    <main class="w-full pt-20 flex-1 flex flex-col items-center justify-center bg-surface">
        <div class="flex flex-col w-full">
            <div class="w-full max-w-7xl mx-auto px-margin-mobile md:px-gutter lg:px-margin py-space-lg lg:py-space-xl">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-stretch">

                    {{-- ===== Sisi Kiri: Showcase ===== --}}
                    <div class="lg:col-span-5 flex flex-col justify-between bg-surface-container-low rounded-xl p-4 sm:p-space-lg lg:p-space-xl relative overflow-hidden shadow-sm">
                        <div class="absolute -top-16 -right-16 w-56 h-56 bg-primary/5 rounded-full blur-2xl pointer-events-none"></div>
                        <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-secondary-container/40 rounded-full blur-3xl pointer-events-none"></div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between gap-space-sm mb-space-lg">
                                <div class="inline-flex items-center gap-space-xs bg-surface-container-high px-space-sm py-space-xs rounded-full">
                                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                                    <span class="font-label-sm text-label-sm text-primary font-semibold tracking-wider">CORE DAEMON v2.4</span>
                                </div>
                                <div class="flex items-center gap-1.5 bg-surface-container px-space-sm py-space-xs rounded-full">
                                    <span class="material-symbols-outlined text-secondary text-[16px]">sensors</span>
                                    <span class="font-label-sm text-label-sm text-secondary font-medium">RFID 13.56 MHz Active</span>
                                </div>
                            </div>

                            <div class="mb-space-lg">
                                <span class="font-label-sm text-label-sm text-secondary uppercase tracking-widest font-semibold block mb-space-xs">Preservasi &amp; Pengetahuan</span>
                                <blockquote class="text-xl sm:text-headline-lg lg:text-display-lg-mobile text-on-surface leading-snug sm:leading-tight font-medium italic">
                                    “Perpustakaan adalah tempat pikiran kita bertumbuh dan peradaban terjaga.”
                                </blockquote>
                                <p class="font-body-md text-body-md text-on-surface-variant mt-space-sm">
                                    Koleksi literatur terkurasi dengan sistem automasi berkas, pelacakan sensorik pintar, dan tata kelola berbasis standar internasional.
                                </p>
                            </div>

                            {{-- Gambar arsip --}}
                            <div class="relative rounded-lg overflow-hidden shadow-sm mb-space-lg group">
                                <div class="w-full h-44 bg-cover bg-center transition-transform duration-700 group-hover:scale-105"
                                     style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBgVJLVaBkj5ItiR5Ooej4euDhgIptsu7fVfP-miPyzMDsFFaNs43Dz1B7MxTU821RKnM7H-B_sFQ6sIqCtog774LytHwKC9zGr721V7cNblSDBmCUUg9NYMe0IlRHFt6EYIreAsNCYPTftQLLTfObZmSa_XUcWVmcuHlPcatkT36b_6TuEqOjLL2zyCPEbv1o4lLv0vNHnfp97iDsSaQhOHcXDK3DcIpL2WyC7A_zITFmKMB7q04ix')"></div>
                                <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/85 via-inverse-surface/30 to-transparent flex flex-col justify-end p-space-md">
                                    <span class="font-label-sm text-label-sm text-inverse-on-surface/80 uppercase tracking-wider font-semibold">Ruang Preservasi Naskah</span>
                                    <span class="font-title-sm text-title-sm text-inverse-on-surface">Depot Utama Koleksi Khusus &amp; Arsip Daerah</span>
                                </div>
                            </div>

                            {{-- Statistik --}}
                            <div class="grid grid-cols-2 gap-space-sm">
                                <div class="bg-surface-container p-space-sm rounded-lg">
                                    <span class="font-headline-sm text-headline-sm text-primary font-semibold block">14.820+</span>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Judul Terakreditasi A</span>
                                </div>
                                <div class="bg-surface-container p-space-sm rounded-lg">
                                    <span class="font-headline-sm text-headline-sm text-primary font-semibold block">1.285</span>
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">Pemustaka Terverifikasi</span>
                                </div>
                            </div>
                        </div>

                        {{-- Spesifikasi keamanan --}}
                        <div class="relative z-10 pt-space-md mt-space-md bg-surface-container-high/60 rounded-lg p-space-sm flex items-center justify-between gap-space-sm">
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
                                <span class="font-label-sm text-label-sm text-on-surface font-semibold">ISO 15693 / ICODE SLIX</span>
                            </div>
                            <div class="flex items-center gap-space-xs">
                                <span class="material-symbols-outlined text-secondary text-[18px]">lock</span>
                                <span class="font-label-sm text-label-sm text-on-surface-variant">AES-256 GCM Secure</span>
                            </div>
                        </div>
                    </div>

                    {{-- ===== Sisi Kanan: Form Login ===== --}}
                    <div class="lg:col-span-7 flex flex-col justify-center">
                        <div class="w-full bg-surface-container-lowest rounded-xl p-5 sm:p-space-lg lg:p-space-xl shadow-[0_4px_24px_-6px_rgba(35,45,19,0.08)]">

                            {{-- Header kartu --}}
                            <div class="mb-space-lg">
                                <div class="inline-flex items-center gap-1.5 text-primary font-label-md text-label-md mb-space-xs">
                                    <span class="material-symbols-outlined text-[16px]">local_library</span>
                                    <span>Portal Otorisasi Terpusat</span>
                                </div>
                                <h1 class="text-2xl sm:text-headline-lg text-on-surface font-semibold">Selamat Datang di Bookself</h1>
                                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Masuk ke portal otomasi perpustakaan &amp; preservasi literatur.</p>
                            </div>

                            {{-- Status session (mis. setelah reset password) --}}
                            @if (session('status'))
                                <div class="mb-space-md p-space-sm rounded-lg bg-secondary-container/60 text-on-surface font-body-sm text-body-sm">
                                    {{ session('status') }}
                                </div>
                            @endif

                            {{-- Error validasi --}}
                            @if ($errors->any())
                                <div class="mb-space-md p-space-sm rounded-lg bg-error-container text-on-error-container flex items-start gap-space-xs" role="alert">
                                    <span class="material-symbols-outlined text-[18px] shrink-0 mt-0.5">error</span>
                                    <div class="font-body-sm text-body-sm">
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            
                            {{-- Form --}}
                            <form method="POST" action="{{ route('login') }}" class="space-y-space-md" id="loginForm">
                                @csrf

                                {{-- Identifier --}}
                                <div>
                                    <label class="block font-title-sm text-title-sm text-on-surface mb-1" for="userIdentifier">
                                        <span id="labelIdentifier">Email / Username / Identifier</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <span class="material-symbols-outlined text-on-surface-variant/70 absolute left-space-sm text-[20px] pointer-events-none">badge</span>
                                        <input id="userIdentifier" name="identifier" type="text" required autofocus
                                               autocomplete="username"
                                               value="{{ old('identifier') }}"
                                               placeholder="Masukkan email atau username"
                                               class="w-full bg-surface-container-low focus:bg-surface-container-lowest text-on-surface font-body-md text-body-md pl-10 pr-space-md py-2.5 rounded-lg outline-none transition-colors duration-200 @error('identifier') ring-1 ring-error @enderror" />
                                    </div>
                                </div>

                                {{-- Password --}}
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block font-title-sm text-title-sm text-on-surface" for="userPassword">Kata Sandi</label>
                                    </div>
                                    <div class="relative flex items-center">
                                        <span class="material-symbols-outlined text-on-surface-variant/70 absolute left-space-sm text-[20px] pointer-events-none">lock</span>
                                        <input id="userPassword" name="password" type="password" required
                                               autocomplete="current-password"
                                               placeholder="Masukkan kombinasi kata sandi"
                                               class="w-full bg-surface-container-low focus:bg-surface-container-lowest text-on-surface font-body-md text-body-md pl-10 pr-10 py-2.5 rounded-lg outline-none transition-colors duration-200 @error('password') ring-1 ring-error @enderror" />
                                        <button type="button" id="togglePassword" aria-label="Tampilkan atau sembunyikan kata sandi"
                                                class="absolute right-space-sm text-on-surface-variant hover:text-on-surface transition-colors focus:outline-none">
                                            <span class="material-symbols-outlined text-[20px]" id="passwordToggleIcon">visibility</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Remember & lupa sandi --}}
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs pt-1">
                                    <label class="inline-flex items-center gap-space-xs cursor-pointer select-none">
                                        <input type="checkbox" name="remember" id="rememberSession" value="1" @checked(old('remember'))
                                               class="w-4 h-4 rounded text-primary accent-primary cursor-pointer" />
                                        <span class="font-body-sm text-body-sm text-on-surface-variant">Ingat sesi perangkat ini (30 hari)</span>
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="font-title-sm text-title-sm text-primary hover:underline self-start sm:self-auto transition-all">Lupa Kata Sandi?</a>
                                    @else
                                        <a href="#" class="font-title-sm text-title-sm text-primary hover:underline self-start sm:self-auto transition-all">Lupa Kata Sandi?</a>
                                    @endif
                                </div>

                                {{-- Tombol submit --}}
                                <button type="submit" id="submitBtn"
                                        class="w-full bg-primary-container hover:bg-primary text-on-primary-container font-title-md text-title-md py-3 px-space-md rounded-lg flex items-center justify-center gap-space-xs transition-all duration-200 shadow-sm hover:shadow active:scale-[0.99] disabled:opacity-80 disabled:cursor-not-allowed">
                                    <span>Masuk ke Sistem</span>
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </button>
                            </form>

                            {{-- Divider --}}
                            <div class="relative my-space-lg flex items-center justify-center">
                                <div class="w-full h-px bg-surface-container-high"></div>
                                <span class="absolute bg-surface-container-lowest px-space-sm font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Atau Masuk Cepat Dengan</span>
                            </div>

                            {{-- Metode alternatif --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-sm">
                                <button type="button" id="rfidScanBtn"
                                        class="group relative flex items-center justify-start gap-space-sm p-space-sm bg-surface-container-low hover:bg-surface-container rounded-lg text-left transition-all">
                                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary group-hover:scale-110 transition-transform relative">
                                        <span class="material-symbols-outlined text-[22px]">contactless</span>
                                        <span class="absolute inset-0 rounded-full bg-primary/20 animate-ping opacity-75"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-title-sm text-title-sm text-on-surface">Tap Kartu RFID</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">Tempel Kartu Anggota</span>
                                    </div>
                                </button>

                                {{-- Ganti href dengan route SSO Anda, mis. route('auth.belajarid') --}}
                                <a href="#"
                                   class="group flex items-center justify-start gap-space-sm p-space-sm bg-surface-container-low hover:bg-surface-container rounded-lg text-left transition-all">
                                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-secondary group-hover:scale-110 transition-transform">
                                        <span class="material-symbols-outlined text-[22px]">domain</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-title-sm text-title-sm text-on-surface">Akun Belajar.id</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant">SSO Kemendikbud • Google</span>
                                    </div>
                                </a>
                            </div>

                            {{-- Notifikasi RFID --}}
                            <div id="rfidNotification"
                                 class="hidden mt-space-md p-space-sm bg-secondary-container/60 rounded-lg items-center justify-between gap-space-sm transition-all duration-300">
                                <div class="flex items-center gap-space-xs">
                                    <span class="material-symbols-outlined text-primary text-[20px] animate-pulse">drive_image</span>
                                    <span class="font-body-sm text-body-sm text-on-surface font-medium">Sensor RFID Siap. Dekatkan kartu ke scanner USB reader...</span>
                                </div>
                                <button type="button" id="cancelRfidBtn" class="font-label-sm text-label-sm text-on-surface-variant hover:text-on-surface underline">Batal</button>
                            </div>

                            {{-- Catatan keamanan --}}
                            <div class="mt-space-lg pt-space-md bg-surface-container-low/50 rounded-lg p-space-sm flex items-start gap-space-xs">
                                <span class="material-symbols-outlined text-secondary text-[18px] shrink-0 mt-0.5">policy</span>
                                <p class="font-body-sm text-body-sm text-on-surface-variant leading-relaxed">
                                    <strong>Catatan Keamanan:</strong> Seluruh aktivitas sesi diawasi otomatis oleh sistem audit integritas ISO 27001. Harap jaga kerahasiaan kata sandi dan kartu kendali Anda.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script>
            (function(){const t=document.getElementById("userPassword"),i=document.getElementById("passwordToggleIcon"),p=document.getElementById("togglePassword"),r=document.getElementById("rfidScanBtn"),n=document.getElementById("rfidNotification"),c=document.getElementById("cancelRfidBtn"),f=document.getElementById("loginForm"),b=document.getElementById("submitBtn");p&&p.addEventListener("click",()=>{const o=t.getAttribute("type")==="password";t.setAttribute("type",o?"text":"password");i.textContent=o?"visibility_off":"visibility"});r&&r.addEventListener("click",()=>{n.classList.remove("hidden");n.classList.add("flex")});c&&c.addEventListener("click",()=>{n.classList.add("hidden");n.classList.remove("flex")});f&&f.addEventListener("submit",()=>{b.disabled=true;b.innerHTML='<span class="w-4 h-4 border-2 border-on-primary-container border-t-transparent rounded-full animate-spin inline-block"></span><span>Memverifikasi...</span>'})})();
        </script>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer class="w-full bg-surface-container-low mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-margin py-space-md sm:py-space-lg flex flex-col md:flex-row items-center justify-between gap-space-md">
            <div class="flex flex-col sm:flex-row items-center gap-space-sm sm:gap-space-md text-center sm:text-left">
                <span class="font-label-md text-label-md text-on-surface-variant">© {{ date('Y') }} Bookself Smart Archival Platform. All rights reserved.</span>
                <span class="hidden sm:inline text-outline-variant">•</span>
                <div class="flex items-center gap-space-xs bg-surface-container px-space-sm py-space-xs rounded-full">
                    <span class="material-symbols-outlined text-secondary text-[16px]">workspace_premium</span>
                    <span class="font-label-sm text-label-sm text-secondary font-semibold">Akreditasi A Perpustakaan Nasional RI</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-center md:justify-end gap-x-6 gap-y-2 text-on-surface-variant font-label-md text-label-md text-center md:text-right">
                <a href="#" class="hover:text-primary transition-colors flex items-center gap-space-xs">
                    <span class="material-symbols-outlined text-[16px]">support_agent</span>
                    <span>Support Desk (Ext. 402)</span>
                </a>
                <a href="#" class="hover:text-primary transition-colors">Privacy &amp; Archival Protocol</a>
                <a href="#" class="hover:text-primary transition-colors">System Status: Operational</a>
            </div>
        </div>
    </footer>

</body>
</html>
