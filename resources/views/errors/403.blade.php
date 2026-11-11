<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="mb-8">
            <svg class="w-24 h-24 mx-auto text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-red-600 mb-4">403</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Akses Ditolak</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda believe ini adalah kesalahan.
        </p>
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Kembali ke Beranda
            </a>
            @if(auth()->check())
                <br>
                <a href="{{ route('dashboard') }}" class="inline-block text-blue-600 hover:underline mt-4">
                    Ke Dashboard
                </a>
            @else
                <br>
                <a href="{{ route('login') }}" class="inline-block text-blue-600 hover:underline mt-4">
                    Login
                </a>
            @endif
        </div>
    </div>
</body>
</html>
