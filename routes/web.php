<?php

use App\Http\Controllers\Admin\BukuController;
use App\Http\Controllers\Admin\PeminjamanController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\PengembalianController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\Siswa\AkunController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\PinjamanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- RUTE AUTENTIKASI ---
Route::get('/', fn () => redirect()->route('login'))->name('home');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'authenticate'])->name('login.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// --- REDIRECT DASHBOARD SESUAI ROLE ---
Route::middleware('auth')->get('/dashboard', function (Request $request) {
    return redirect()->route($request->user()->roleEnum()->dashboardRoute());
})->name('dashboard');

// --- KATALOG (semua role yang login) ---
Route::middleware(['auth', 'role:admin,pustakawan,petugas,guru,siswa,user'])
    ->get('/katalog', [DashboardController::class, 'index'])
    ->name('siswa.katalog');

// --- SIRKULASI MANDIRI (anggota/pemustaka) ---
Route::middleware(['auth', 'role:siswa,guru,user'])->group(function () {
    Route::post('/peminjaman/simpan', [LoanController::class, 'store'])->name('loans.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/peminjaman', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/invoice/loan/{loan}', [InvoiceController::class, 'loan'])->name('invoices.loan');
    Route::get('/invoice/fine/{fine}', [InvoiceController::class, 'fine'])->name('invoices.fine');
});

// --- AREA ANGGOTA / PEMUSTAKA (siswa & guru) ---
Route::middleware(['auth', 'role:siswa,guru,user'])->prefix('siswa')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');

    Route::get('/pinjaman', [PinjamanController::class, 'index'])->name('siswa.pinjaman');
    Route::post('/pinjaman/{loan}/perpanjang', [PinjamanController::class, 'perpanjang'])->name('siswa.pinjaman.perpanjang');

    Route::get('/riwayat', [PinjamanController::class, 'riwayat'])->name('siswa.riwayat');

    Route::get('/kartu', [AkunController::class, 'kartu'])->name('siswa.kartu');
    Route::get('/profil', [AkunController::class, 'profil'])->name('siswa.profil');
    Route::post('/profil', [AkunController::class, 'update'])->name('siswa.profil.update');
    Route::post('/profil/sandi', [AkunController::class, 'updateSandi'])->name('siswa.profil.sandi');
});

// --- AREA STAF: DASHBOARD & SIRKULASI ---
Route::middleware(['auth', 'role:admin,pustakawan,petugas', 'can:circulate'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    Route::get('/admin/peminjaman', [PeminjamanController::class, 'index'])->name('admin.peminjaman');
    Route::post('/admin/peminjaman', [PeminjamanController::class, 'store'])->name('admin.peminjaman.store');

    Route::get('/admin/pengembalian', [PengembalianController::class, 'index'])->name('admin.pengembalian');
    Route::patch('/admin/pengembalian/{loan}', [PengembalianController::class, 'proses'])->name('admin.pengembalian.proses');

    Route::get('/admin/petugas', [PetugasDashboardController::class, 'index'])->name('admin.petugas');
    Route::get('/petugas/dashboard', [PetugasDashboardController::class, 'index'])->name('petugas.dashboard');
});

// --- AREA STAF: KATALOG BUKU (lihat) ---
Route::middleware(['auth', 'role:admin,pustakawan,petugas'])->group(function () {
    Route::get('/admin/buku', [BukuController::class, 'index'])->name('admin.buku.index');
});

// --- AREA ADMIN & PUSTAKAWAN: KELOLA KOLEKSI ---
Route::middleware(['auth', 'role:admin,pustakawan', 'can:manage-books'])->group(function () {
    Route::get('/admin/buku/tambah', [DashboardController::class, 'tambahBuku'])->name('admin.buku.tambah');
    Route::post('/admin/buku', [BukuController::class, 'store'])->name('admin.buku.store');
    Route::put('/admin/buku/{book}', [BukuController::class, 'update'])->name('admin.buku.update');
    Route::delete('/admin/buku/{book}', [BukuController::class, 'destroy'])->name('admin.buku.destroy');
});

// --- AREA ADMIN: TATA KELOLA PENGGUNA ---
Route::middleware(['auth', 'role:admin', 'can:manage-users'])->group(function () {
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

// --- AREA ADMIN: PENGATURAN SISTEM (mis. tarif denda otomatis) ---
Route::middleware(['auth', 'role:admin', 'can:manage-settings'])->group(function () {
    Route::get('/admin/pengaturan', [PengaturanController::class, 'index'])->name('admin.pengaturan');
    Route::put('/admin/pengaturan', [PengaturanController::class, 'update'])->name('admin.pengaturan.update');
});

// --- PENGEMBALIAN OLEH STAF DITANGANI ADMIN\PengembalianController ---
