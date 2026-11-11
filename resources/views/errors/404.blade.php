<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-blue-600">404</h1>
        </div>
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Maaf, halaman yang Anda cari tidak dapat ditemukan atau telah dipindahkan.
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
            @endif
        </div>
    </div>
</body>
</html>
