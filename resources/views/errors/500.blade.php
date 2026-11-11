<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terjadi Kesalahan - Smart Library</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center p-8">
        <div class="mb-8">
            <svg class="w-24 h-24 mx-auto text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-6xl font-bold text-orange-600 mb-4">500</h1>
        <h2 class="text-3xl font-semibold text-gray-800 mb-4">Terjadi Kesalahan Server</h2>
        <p class="text-gray-600 mb-8 max-w-md mx-auto">
            Maaf, terjadi kesalahan pada server kami. Tim teknis telah diberitahu dan sedang bekerja untuk memperbaikinya.
        </p>
        <div class="space-y-4">
            <a href="{{ route('home') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                Kembali ke Beranda
            </a>
            <br>
            <a href="javascript:history.back()" class="inline-block text-blue-600 hover:underline mt-4">
                Kembali ke Halaman Sebelumnya
            </a>
        </div>
    </div>
</body>
</html>
