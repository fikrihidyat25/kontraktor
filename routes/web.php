<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\KontraktorController;
use App\Http\Controllers\KonsultanController;
use App\Http\Controllers\PPKController;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-role', function () {
    $user = \App\Models\User::find(2); // kontraktor
    Auth::login($user);
    
    $request = request();
    $middleware = new \App\Http\Middleware\RoleMiddleware();
    return $middleware->handle($request, function() {
        return "SUCCESS!";
    }, 'kontraktor');
});

// Dashboard — redirect berdasarkan role
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'super_admin' => redirect()->route('admin.dashboard'),
        'kontraktor'  => redirect()->route('kontraktor.dashboard'),
        'konsultan'   => redirect()->route('konsultan.dashboard'),
        'ppk'         => redirect()->route('ppk.dashboard'),
        default       => redirect('/'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// ─── SUPER ADMIN ───────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');

    // User Management
    Route::get('/users', [SuperAdminController::class, 'userIndex'])->name('users.index');
    Route::get('/users/create', [SuperAdminController::class, 'userCreate'])->name('users.create');
    Route::post('/users', [SuperAdminController::class, 'userStore'])->name('users.store');
    Route::delete('/users/{user}', [SuperAdminController::class, 'userDestroy'])->name('users.destroy');

    // Proyek Management
    Route::get('/proyeks', [SuperAdminController::class, 'proyekIndex'])->name('proyeks.index');
    Route::get('/proyeks/create', [SuperAdminController::class, 'proyekCreate'])->name('proyeks.create');
    Route::post('/proyeks', [SuperAdminController::class, 'proyekStore'])->name('proyeks.store');
    Route::delete('/proyeks/{proyek}', [SuperAdminController::class, 'proyekDestroy'])->name('proyeks.destroy');
});

// ─── KONTRAKTOR ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kontraktor'])->prefix('kontraktor')->name('kontraktor.')->group(function () {
    Route::get('/dashboard', [KontraktorController::class, 'dashboard'])->name('dashboard');


});

// ─── KONSULTAN ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:konsultan'])->prefix('konsultan')->name('konsultan.')->group(function () {
    Route::get('/dashboard', [KonsultanController::class, 'dashboard'])->name('dashboard');


});

// ─── PPK ───────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:ppk'])->prefix('ppk')->name('ppk.')->group(function () {
    Route::get('/dashboard', [PPKController::class, 'dashboard'])->name('dashboard');


});

// ─── UNIFIED INTERFACE (Multiple Authorizations) ─────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Uang Muka
    Route::get('/uang-muka', [App\Http\Controllers\UangMukaController::class, 'index'])->name('uang-muka.index');
    Route::post('/uang-muka', [App\Http\Controllers\UangMukaController::class, 'store'])->name('uang-muka.store');
    Route::post('/uang-muka/{uangMuka}/approve', [App\Http\Controllers\UangMukaController::class, 'approve'])->name('uang-muka.approve');
    Route::post('/uang-muka/{uangMuka}/reject', [App\Http\Controllers\UangMukaController::class, 'reject'])->name('uang-muka.reject');

    // Laporan Harian
    Route::get('/laporan-harian', [App\Http\Controllers\LaporanHarianController::class, 'index'])->name('laporan-harian.index');
    Route::get('/laporan-harian/create', [App\Http\Controllers\LaporanHarianController::class, 'create'])->name('laporan-harian.create');
    Route::post('/laporan-harian', [App\Http\Controllers\LaporanHarianController::class, 'store'])->name('laporan-harian.store');
    Route::get('/laporan-harian/{laporanHarian}', [App\Http\Controllers\LaporanHarianController::class, 'show'])->name('laporan-harian.show');
    Route::post('/laporan-harian/{laporanHarian}/submit', [App\Http\Controllers\LaporanHarianController::class, 'submit'])->name('laporan-harian.submit');
    Route::post('/laporan-harian/{laporanHarian}/verify', [App\Http\Controllers\LaporanHarianController::class, 'verify'])->name('laporan-harian.verify');
    Route::post('/laporan-harian/{laporanHarian}/reject-konsultan', [App\Http\Controllers\LaporanHarianController::class, 'rejectKonsultan'])->name('laporan-harian.reject-konsultan');
    Route::post('/laporan-harian/{laporanHarian}/approve', [App\Http\Controllers\LaporanHarianController::class, 'approve'])->name('laporan-harian.approve');
    Route::post('/laporan-harian/{laporanHarian}/reject-ppk', [App\Http\Controllers\LaporanHarianController::class, 'rejectPPK'])->name('laporan-harian.reject-ppk');

    // Laporan Mingguan
    Route::get('/laporan-mingguan', [App\Http\Controllers\LaporanMingguanController::class, 'index'])->name('laporan-mingguan.index');
    Route::get('/laporan-mingguan/create', [App\Http\Controllers\LaporanMingguanController::class, 'create'])->name('laporan-mingguan.create');
    Route::post('/laporan-mingguan', [App\Http\Controllers\LaporanMingguanController::class, 'store'])->name('laporan-mingguan.store');
    Route::get('/laporan-mingguan/{laporanMingguan}', [App\Http\Controllers\LaporanMingguanController::class, 'show'])->name('laporan-mingguan.show');
    Route::post('/laporan-mingguan/{laporanMingguan}/verify', [App\Http\Controllers\LaporanMingguanController::class, 'verify'])->name('laporan-mingguan.verify');
    Route::post('/laporan-mingguan/{laporanMingguan}/reject-konsultan', [App\Http\Controllers\LaporanMingguanController::class, 'rejectKonsultan'])->name('laporan-mingguan.reject-konsultan');
    Route::post('/laporan-mingguan/{laporanMingguan}/approve', [App\Http\Controllers\LaporanMingguanController::class, 'approve'])->name('laporan-mingguan.approve');
    Route::post('/laporan-mingguan/{laporanMingguan}/reject-ppk', [App\Http\Controllers\LaporanMingguanController::class, 'rejectPPK'])->name('laporan-mingguan.reject-ppk');

    Route::post('laporan-bulanan/{laporan_bulanan}/submit', [App\Http\Controllers\LaporanBulananController::class, 'submit'])->name('laporan-bulanan.submit');
    Route::post('laporan-bulanan/{laporan_bulanan}/verify', [App\Http\Controllers\LaporanBulananController::class, 'verify'])->name('laporan-bulanan.verify');
    Route::post('laporan-bulanan/{laporan_bulanan}/approve', [App\Http\Controllers\LaporanBulananController::class, 'approve'])->name('laporan-bulanan.approve');
    Route::post('laporan-bulanan/{laporan_bulanan}/reject', [App\Http\Controllers\LaporanBulananController::class, 'reject'])->name('laporan-bulanan.reject');
    Route::resource('laporan-bulanan', App\Http\Controllers\LaporanBulananController::class);

    // Kerja Tambah Kurang
    Route::get('/kerja-tambah-kurang', [App\Http\Controllers\KerjaTambahKurangController::class, 'index'])->name('kerja-tambah-kurang.index');
    Route::post('/kerja-tambah-kurang', [App\Http\Controllers\KerjaTambahKurangController::class, 'store'])->name('kerja-tambah-kurang.store');
    Route::post('/kerja-tambah-kurang/{ktk}/verify', [App\Http\Controllers\KerjaTambahKurangController::class, 'verify'])->name('kerja-tambah-kurang.verify');
    Route::post('/kerja-tambah-kurang/{ktk}/reject-konsultan', [App\Http\Controllers\KerjaTambahKurangController::class, 'rejectKonsultan'])->name('kerja-tambah-kurang.reject-konsultan');
    Route::post('/kerja-tambah-kurang/{ktk}/approve', [App\Http\Controllers\KerjaTambahKurangController::class, 'approve'])->name('kerja-tambah-kurang.approve');
    Route::post('/kerja-tambah-kurang/{ktk}/reject-ppk', [App\Http\Controllers\KerjaTambahKurangController::class, 'rejectPPK'])->name('kerja-tambah-kurang.reject-ppk');

    // Permintaan Pembayaran
    Route::get('/permintaan-pembayaran', [App\Http\Controllers\PermintaanPembayaranController::class, 'index'])->name('permintaan-pembayaran.index');
    Route::post('/permintaan-pembayaran', [App\Http\Controllers\PermintaanPembayaranController::class, 'store'])->name('permintaan-pembayaran.store');
    Route::post('/permintaan-pembayaran/{pembayaran}/verify', [App\Http\Controllers\PermintaanPembayaranController::class, 'verify'])->name('permintaan-pembayaran.verify');
    Route::post('/permintaan-pembayaran/{pembayaran}/reject-konsultan', [App\Http\Controllers\PermintaanPembayaranController::class, 'rejectKonsultan'])->name('permintaan-pembayaran.reject-konsultan');
    Route::post('/permintaan-pembayaran/{pembayaran}/approve', [App\Http\Controllers\PermintaanPembayaranController::class, 'approve'])->name('permintaan-pembayaran.approve');
    Route::post('/permintaan-pembayaran/{pembayaran}/reject-ppk', [App\Http\Controllers\PermintaanPembayaranController::class, 'rejectPPK'])->name('permintaan-pembayaran.reject-ppk');
});

// ─── PROFILE ───────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
