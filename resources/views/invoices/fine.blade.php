<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Denda Perpustakaan — {{ $invoiceNumber }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Newsreader:ital,wght@0,600;1,600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    @vite(['resources/css/app.css'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Newsreader', serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background-color: #ffffff !important; color: #111827 !important; }
            .no-print { display: none !important; }
            .print-container { box-shadow: none !important; border: 1px solid #e5e7eb !important; padding: 0 !important; max-width: 100% !important; margin: 0 !important; }
            @page { size: A4; margin: 12mm; }
        }
    </style>
</head>
<body class="bg-surface-container-low min-h-screen py-6 sm:py-10 px-4 text-on-surface antialiased flex flex-col items-center">

    {{-- Action Bar --}}
    <div class="max-w-3xl w-full flex items-center justify-between mb-6 no-print">
        <a href="{{ url()->previous() ?? route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-container hover:bg-surface-container-high text-on-surface font-title-sm text-sm transition-all shadow-sm">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali</span>
        </a>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-primary hover:bg-primary/90 text-on-primary font-title-sm text-sm transition-all shadow-md active:scale-95">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    {{-- Invoice Paper Container --}}
    <div class="max-w-3xl w-full bg-surface rounded-2xl shadow-lg border border-surface-container p-6 sm:p-10 print-container flex flex-col gap-6">

        {{-- Kop Surat / Header --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b-2 border-primary/20 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-primary flex items-center justify-center text-on-primary shadow-sm shrink-0">
                    <span class="material-symbols-outlined text-[28px]">receipt_long</span>
                </div>
                <div>
                    <h1 class="font-headline-sm text-xl font-bold tracking-tight text-on-surface">BOOKSELF PERPUSTAKAAN</h1>
                    <p class="font-body-sm text-xs text-on-surface-variant">Unit Administrasi & Rekapitulasi Denda • Terakreditasi A Perpustakaan Nasional RI</p>
                    <p class="font-body-sm text-[11px] text-on-surface-variant/80">Layanan Kas Sirkulasi • Buku Sanksi Keterlambatan Koleksi</p>
                </div>
            </div>
            <div class="text-left sm:text-right shrink-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                    {{ in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true) ? 'bg-secondary-container text-on-secondary-fixed-variant' : 'bg-error-container text-on-error-container' }}">
                    {{ in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true) ? 'LUNAS / SELESAI' : 'TERTUNGGAK / BELUM LUNAS' }}
                </span>
                <p class="font-mono text-xs text-on-surface-variant mt-1.5">{{ $invoiceNumber }}</p>
            </div>
        </div>

        {{-- Judul Invoice --}}
        <div class="text-center py-2">
            <h2 class="font-headline-sm text-lg sm:text-xl font-bold uppercase tracking-wider text-primary">Kuitansi Penetapan & Pembayaran Denda</h2>
            <p class="font-body-sm text-xs text-on-surface-variant mt-0.5">Diterbitkan pada: {{ ($fine->created_at ?? \Carbon\Carbon::now())->translatedFormat('l, d F Y - H:i') }} WIB</p>
        </div>

        {{-- Meta Data: Anggota & Transaksi --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-surface-container-low border border-surface-container">
            <div class="flex flex-col gap-2">
                <span class="font-label-sm text-[11px] uppercase tracking-wider text-on-surface-variant font-semibold">Identitas Wajib Denda</span>
                <div class="text-xs sm:text-sm flex flex-col gap-1">
                    <div class="flex"><span class="w-28 text-on-surface-variant">Nama:</span> <span class="font-semibold text-on-surface">{{ $fine->loan?->user?->name ?? 'Pemustaka' }}</span></div>
                    <div class="flex"><span class="w-28 text-on-surface-variant">Nomor Identitas:</span> <span class="font-mono text-on-surface">{{ $fine->loan?->user?->identifier ?? '-' }}</span></div>
                    <div class="flex"><span class="w-28 text-on-surface-variant">Kategori Akun:</span> <span class="text-on-surface capitalize">{{ $fine->loan?->user?->roleEnum()->label() ?? 'Anggota' }}</span></div>
                </div>
            </div>
            <div class="flex flex-col gap-2">
                <span class="font-label-sm text-[11px] uppercase tracking-wider text-on-surface-variant font-semibold">Detail Sirkulasi Terkait</span>
                <div class="text-xs sm:text-sm flex flex-col gap-1">
                    <div class="flex"><span class="w-32 text-on-surface-variant">ID Sirkulasi:</span> <span class="font-mono font-semibold text-on-surface">#TRX-{{ str_pad($fine->loan_id, 5, '0', STR_PAD_LEFT) }}</span></div>
                    <div class="flex"><span class="w-32 text-on-surface-variant">Tanggal Pinjam:</span> <span class="text-on-surface">{{ $fine->loan?->tanggal_pinjam?->translatedFormat('d F Y') ?? '-' }}</span></div>
                    <div class="flex"><span class="w-32 text-on-surface-variant">Jatuh Tempo:</span> <span class="text-on-surface">{{ $fine->loan?->batas_kembali?->translatedFormat('d F Y') ?? '-' }}</span></div>
                    <div class="flex"><span class="w-32 text-on-surface-variant">Tanggal Kembali:</span> 
                        <span class="font-semibold text-on-surface">{{ $fine->loan?->tanggal_dikembalikan?->translatedFormat('d F Y') ?? 'Belum Kembali' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Rincian Tagihan Denda --}}
        <div class="flex flex-col gap-2">
            <span class="font-label-sm text-xs uppercase tracking-wider text-on-surface-variant font-semibold">Rincian Komputasi Denda</span>
            <div class="border border-surface-container rounded-xl overflow-hidden">
                <table class="min-w-full divide-y divide-surface-container">
                    <thead class="bg-surface-container-high text-on-surface">
                        <tr class="text-left font-label-md text-xs uppercase tracking-wider">
                            <th class="py-3 px-4 w-12 text-center">No</th>
                            <th class="py-3 px-4">Deskripsi Sanksi</th>
                            <th class="py-3 px-4">Koleksi Terkait</th>
                            <th class="py-3 px-4 text-center">Tarif / Hari</th>
                            <th class="py-3 px-4 text-right">Jumlah Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container bg-surface text-xs sm:text-sm">
                        <tr>
                            <td class="py-3 px-4 text-center font-mono">1</td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-on-surface">Denda Keterlambatan Sirkulasi</span>
                                <p class="text-[11px] text-on-surface-variant mt-0.5">Penetapan denda otomatis sistem sirkulasi</p>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-primary">{{ $fine->loan?->book?->judul ?? 'Koleksi Buku' }}</span>
                                <span class="block text-[11px] font-mono text-on-surface-variant">({{ $fine->loan?->book?->kode_buku ?? '-' }})</span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">Rp {{ number_format($tarifPerHari, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-base text-on-surface">
                                Rp {{ number_format($fine->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-surface-container text-xs sm:text-sm font-semibold">
                        <tr>
                            <td colspan="4" class="py-3 px-4 text-right uppercase tracking-wider text-on-surface">Total Beban Denda</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-lg text-primary">
                                Rp {{ number_format($fine->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Status Pembayaran Alert Box --}}
        <div class="p-4 rounded-xl flex items-center justify-between border
            {{ in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true) 
                ? 'bg-secondary-container/40 border-secondary/30 text-on-secondary-fixed-variant' 
                : 'bg-error-container/30 border-error/30 text-on-error-container' }}">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-[24px]">
                    {{ in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true) ? 'verified' : 'pending_actions' }}
                </span>
                <div>
                    <p class="font-title-sm text-sm font-bold">
                        Status Pembayaran: {{ in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true) ? 'Lunas (Dana Terverifikasi)' : 'Tertunggak (Belum Diselesaikan)' }}
                    </p>
                    <p class="font-body-sm text-xs opacity-90">
                        {{ in_array($fine->status_pembayaran, ['LUNAS', 'PAID'], true) 
                            ? 'Pembayaran telah diterima dan dicatat pada buku kas perpustakaan.' 
                            : 'Silakan lakukan penyelesaian denda di meja layanan sirkulasi perpustakaan.' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Kolom Tanda Tangan & Kuitansi --}}
        <div class="grid grid-cols-2 pt-6 border-t border-surface-container gap-8 text-center text-xs">
            <div class="flex flex-col items-center">
                <p class="text-on-surface-variant mb-14">Pemustaka,</p>
                <p class="font-semibold text-on-surface border-b border-on-surface/40 pb-1 w-44">{{ $fine->loan?->user?->name ?? 'Pemustaka' }}</p>
                <p class="text-[10px] text-on-surface-variant mt-0.5">NISN/ID: {{ $fine->loan?->user?->identifier ?? '-' }}</p>
            </div>
            <div class="flex flex-col items-center">
                <p class="text-on-surface-variant mb-14">Kasir / Bagian Administrasi Perpustakaan,</p>
                <p class="font-semibold text-on-surface border-b border-on-surface/40 pb-1 w-44">Petugas Sirkulasi Bookself</p>
                <p class="text-[10px] text-on-surface-variant mt-0.5">Kuitansi Pembayaran Sah Sistem</p>
            </div>
        </div>

        {{-- Barcode / Verification Footer --}}
        <div class="pt-4 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between text-[10px] text-on-surface-variant gap-2">
            <span class="font-mono">VERIFICATION-HASH: {{ hash('sha256', $invoiceNumber . $fine->nominal . $fine->created_at) }}</span>
            <span>Bookself Archival Engine • Halaman 1 / 1</span>
        </div>

    </div>

</body>
</html>
