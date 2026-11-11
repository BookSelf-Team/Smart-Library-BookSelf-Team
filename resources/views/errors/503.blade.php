<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Tidak Tersedia - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="mb-8">
            <svg class="w-24 h-24 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-yellow-600 mb-4">503</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Layanan Sedang Dalam Pemeliharaan</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Smart Library sedang dalam pemeliharaan. Silakan coba lagi beberapa saat.
        </p>
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Coba Lagi
            </a>
        </div>
    </div>
</body>
</html>
