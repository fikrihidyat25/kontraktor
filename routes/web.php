<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\KontraktorController;
use App\Http\Controllers\KonsultanController;
use App\Http\Controllers\PPKController;
use App\Http\Controllers\PPTKController;
use App\Http\Controllers\LaporanLainnyaController;
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

// Dashboard — redirect berdasarkan role (menggunakan switch agar kompatibel dengan editor cPanel)
Route::get('/dashboard', function () {
    $user = auth()->user();
    switch ($user->role) {
        case 'kontraktor':
            return redirect()->route('kontraktor.dashboard');
        case 'konsultan':
            return redirect()->route('konsultan.dashboard');
        case 'ppk':
            return redirect()->route('ppk.dashboard');
        case 'pptk':
            return redirect()->route('pptk.dashboard');
        default:
            return redirect('/');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// ─── KONTRAKTOR ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:kontraktor'])->prefix('kontraktor')->name('kontraktor.')->group(function () {
    Route::get('/dashboard', [KontraktorController::class, 'dashboard'])->name('dashboard');
    Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity_logs');
});

// ─── KONSULTAN ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:konsultan'])->prefix('konsultan')->name('konsultan.')->group(function () {
    Route::get('/dashboard', [KonsultanController::class, 'dashboard'])->name('dashboard');
});

// ─── PPK ───────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:ppk'])->prefix('ppk')->name('ppk.')->group(function () {
    Route::get('/dashboard', [PPKController::class, 'dashboard'])->name('dashboard');
    Route::get('/proyek/{proyek}/edit-tim', [PPKController::class, 'editTim'])->name('proyek.edit-tim');
    Route::post('/proyek/{proyek}/update-tim', [PPKController::class, 'updateTim'])->name('proyek.update-tim');

    // User Management
    Route::get('/users', [PPKController::class, 'userIndex'])->name('users.index');
    Route::get('/users/create', [PPKController::class, 'userCreate'])->name('users.create');
    Route::post('/users', [PPKController::class, 'userStore'])->name('users.store');
    Route::delete('/users/{user}', [PPKController::class, 'userDestroy'])->name('users.destroy');

    // Proyek Management
    Route::get('/proyeks', [PPKController::class, 'proyekIndex'])->name('proyeks.index');
    Route::get('/proyeks/create', [PPKController::class, 'proyekCreate'])->name('proyeks.create');
    Route::post('/proyeks', [PPKController::class, 'proyekStore'])->name('proyeks.store');
    Route::delete('/proyeks/{proyek}', [PPKController::class, 'proyekDestroy'])->name('proyeks.destroy');
});

// ─── PPTK ──────────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:pptk'])->prefix('pptk')->name('pptk.')->group(function () {
    Route::get('/dashboard', [PPTKController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan-harian', [PPTKController::class, 'laporanHarianIndex'])->name('laporan-harian.index');
    Route::get('/laporan-mingguan', [PPTKController::class, 'laporanMingguanIndex'])->name('laporan-mingguan.index');
});

// ─── UNIFIED INTERFACE (Multiple Authorizations) ─────────────────────────────────
Route::middleware(['auth'])->group(function () {
    // Tentang (Profil Proyek)
    Route::get('/tentang', [App\Http\Controllers\TentangController::class, 'index'])->name('tentang.index');

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
    Route::post('/laporan-harian/{laporanHarian}/reject-pptk', [App\Http\Controllers\LaporanHarianController::class, 'rejectPPTK'])->name('laporan-harian.reject-pptk');
    Route::post('/laporan-harian/{laporanHarian}/mark-read', [App\Http\Controllers\LaporanHarianController::class, 'markRead'])->name('laporan-harian.mark-read');

    // Laporan Mingguan
    Route::get('/laporan-mingguan', [App\Http\Controllers\LaporanMingguanController::class, 'index'])->name('laporan-mingguan.index');
    Route::get('/laporan-mingguan/create', [App\Http\Controllers\LaporanMingguanController::class, 'create'])->name('laporan-mingguan.create');
    Route::post('/laporan-mingguan', [App\Http\Controllers\LaporanMingguanController::class, 'store'])->name('laporan-mingguan.store');
    Route::get('/laporan-mingguan/{laporanMingguan}', [App\Http\Controllers\LaporanMingguanController::class, 'show'])->name('laporan-mingguan.show');
    Route::post('/laporan-mingguan/{laporanMingguan}/verify', [App\Http\Controllers\LaporanMingguanController::class, 'verify'])->name('laporan-mingguan.verify');
    Route::post('/laporan-mingguan/{laporanMingguan}/reject-konsultan', [App\Http\Controllers\LaporanMingguanController::class, 'rejectKonsultan'])->name('laporan-mingguan.reject-konsultan');
    Route::post('/laporan-mingguan/{laporanMingguan}/approve', [App\Http\Controllers\LaporanMingguanController::class, 'approve'])->name('laporan-mingguan.approve');
    Route::post('/laporan-mingguan/{laporanMingguan}/reject-pptk', [App\Http\Controllers\LaporanMingguanController::class, 'rejectPPTK'])->name('laporan-mingguan.reject-pptk');

    Route::post('laporan-bulanan/{laporan_bulanan}/submit', [App\Http\Controllers\LaporanBulananController::class, 'submit'])->name('laporan-bulanan.submit');
    Route::post('laporan-bulanan/{laporan_bulanan}/verify', [App\Http\Controllers\LaporanBulananController::class, 'verify'])->name('laporan-bulanan.verify');
    Route::post('laporan-bulanan/{laporan_bulanan}/approve', [App\Http\Controllers\LaporanBulananController::class, 'approve'])->name('laporan-bulanan.approve');
    Route::post('laporan-bulanan/{laporan_bulanan}/reject', [App\Http\Controllers\LaporanBulananController::class, 'reject'])->name('laporan-bulanan.reject');
    Route::resource('laporan-bulanan', App\Http\Controllers\LaporanBulananController::class)->except(['create', 'store']);

    // Laporan Lainnya
    Route::get('/laporan-lainnya', [App\Http\Controllers\LaporanLainnyaController::class, 'index'])->name('laporan-lainnya.index');
    Route::post('/laporan-lainnya', [App\Http\Controllers\LaporanLainnyaController::class, 'store'])->name('laporan-lainnya.store');
    Route::post('/laporan-lainnya/{laporan_lainnya}/approve', [App\Http\Controllers\LaporanLainnyaController::class, 'approve'])->name('laporan-lainnya.approve');
    Route::post('/laporan-lainnya/{laporan_lainnya}/reject', [App\Http\Controllers\LaporanLainnyaController::class, 'reject'])->name('laporan-lainnya.reject');
    Route::delete('/laporan-lainnya/{laporan_lainnya}', [App\Http\Controllers\LaporanLainnyaController::class, 'destroy'])->name('laporan-lainnya.destroy');

    // Kerja Tambah Kurang
    Route::get('/kerja-tambah-kurang', [App\Http\Controllers\KerjaTambahKurangController::class, 'index'])->name('kerja-tambah-kurang.index');
    Route::post('/kerja-tambah-kurang', [App\Http\Controllers\KerjaTambahKurangController::class, 'store'])->name('kerja-tambah-kurang.store');
    Route::post('/kerja-tambah-kurang/{ktk}/approve', [App\Http\Controllers\KerjaTambahKurangController::class, 'approve'])->name('kerja-tambah-kurang.approve');
    Route::post('/kerja-tambah-kurang/{ktk}/reject', [App\Http\Controllers\KerjaTambahKurangController::class, 'reject'])->name('kerja-tambah-kurang.reject');

    // Serah Terima (PHO/FHO)
    Route::get('/serah-terima', [App\Http\Controllers\SerahTerimaController::class, 'index'])->name('serah-terima.index');
    Route::post('/serah-terima', [App\Http\Controllers\SerahTerimaController::class, 'store'])->name('serah-terima.store');
    Route::post('serah-terima/{serah_terima}/approve', [App\Http\Controllers\SerahTerimaController::class, 'approve'])->name('serah-terima.approve');
    Route::post('serah-terima/{serah_terima}/reject', [App\Http\Controllers\SerahTerimaController::class, 'reject'])->name('serah-terima.reject');
    Route::post('serah-terima/{serah_terima}/approve-konsultan', [App\Http\Controllers\SerahTerimaController::class, 'approveKonsultan'])->name('serah-terima.approveKonsultan');
    Route::post('serah-terima/{serah_terima}/reject-konsultan', [App\Http\Controllers\SerahTerimaController::class, 'rejectKonsultan'])->name('serah-terima.rejectKonsultan');
    Route::resource('serah-terima', App\Http\Controllers\SerahTerimaController::class)->except(['create', 'edit', 'update', 'destroy']);

    // Permintaan Pembayaran
    Route::get('/permintaan-pembayaran', [App\Http\Controllers\PermintaanPembayaranController::class, 'index'])->name('permintaan-pembayaran.index');
    Route::post('/permintaan-pembayaran', [App\Http\Controllers\PermintaanPembayaranController::class, 'store'])->name('permintaan-pembayaran.store');
    Route::post('/permintaan-pembayaran/{pembayaran}/verify', [App\Http\Controllers\PermintaanPembayaranController::class, 'verify'])->name('permintaan-pembayaran.verify');
    Route::post('/permintaan-pembayaran/{pembayaran}/reject-konsultan', [App\Http\Controllers\PermintaanPembayaranController::class, 'rejectKonsultan'])->name('permintaan-pembayaran.reject-konsultan');
    Route::post('/permintaan-pembayaran/{pembayaran}/approve', [App\Http\Controllers\PermintaanPembayaranController::class, 'approve'])->name('permintaan-pembayaran.approve');
    Route::post('/permintaan-pembayaran/{pembayaran}/reject-pptk', [App\Http\Controllers\PermintaanPembayaranController::class, 'rejectPPTK'])->name('permintaan-pembayaran.reject-pptk');

    // Dokumen Proyek (Kontrak, SPMK, dll)
    Route::get('/dokumen', [App\Http\Controllers\DokumenProyekController::class, 'index'])->name('dokumen.index');
    Route::get('/dokumen/{dokuman}/show', [App\Http\Controllers\DokumenProyekController::class, 'show'])->name('dokumen.show');
    Route::get('/dokumen/{dokuman}/file', [App\Http\Controllers\DokumenProyekController::class, 'viewFile'])->name('dokumen.file');
    Route::get('/dokumen/{dokuman}/lampiran', [App\Http\Controllers\DokumenProyekController::class, 'viewLampiran'])->name('dokumen.lampiran');
    Route::post('/dokumen', [App\Http\Controllers\DokumenProyekController::class, 'store'])->name('dokumen.store');
    Route::post('/dokumen/{dokuman}/approve', [App\Http\Controllers\DokumenProyekController::class, 'approve'])->name('dokumen.approve');
    Route::post('/dokumen/{dokuman}/reject', [App\Http\Controllers\DokumenProyekController::class, 'reject'])->name('dokumen.reject');
    Route::delete('/dokumen/{dokuman}', [App\Http\Controllers\DokumenProyekController::class, 'destroy'])->name('dokumen.destroy');

    // Site Meeting
    Route::get('/site-meeting', [App\Http\Controllers\SiteMeetingController::class, 'index'])->name('site-meeting.index');
    Route::post('/site-meeting', [App\Http\Controllers\SiteMeetingController::class, 'store'])->name('site-meeting.store');
    Route::delete('/site-meeting/{id}', [App\Http\Controllers\SiteMeetingController::class, 'destroy'])->name('site-meeting.destroy');
    // Shop Drawing
    Route::get('/shop-drawing', [App\Http\Controllers\ShopDrawingController::class, 'index'])->name('shop-drawing.index');
    Route::post('/shop-drawing', [App\Http\Controllers\ShopDrawingController::class, 'store'])->name('shop-drawing.store');
    Route::post('/shop-drawing/{shopDrawing}/approve', [App\Http\Controllers\ShopDrawingController::class, 'approve'])->name('shop-drawing.approve');
    Route::post('/shop-drawing/{shopDrawing}/reject', [App\Http\Controllers\ShopDrawingController::class, 'reject'])->name('shop-drawing.reject');

    // KONSULTAN FEATURES
    Route::get('/laporan-pengawas', [App\Http\Controllers\LaporanPengawasController::class, 'index'])->name('laporan-pengawas.index');
    Route::post('/laporan-pengawas', [App\Http\Controllers\LaporanPengawasController::class, 'store'])->name('laporan-pengawas.store');
    Route::post('/laporan-pengawas/{laporanPengawa}/approve', [App\Http\Controllers\LaporanPengawasController::class, 'approve'])->name('laporan-pengawas.approve');
    Route::post('/laporan-pengawas/{laporanPengawa}/reject', [App\Http\Controllers\LaporanPengawasController::class, 'reject'])->name('laporan-pengawas.reject');

    Route::get('/uang-muka-konsultan', [App\Http\Controllers\UangMukaKonsultanController::class, 'index'])->name('uang-muka-konsultan.index');
    Route::post('/uang-muka-konsultan', [App\Http\Controllers\UangMukaKonsultanController::class, 'store'])->name('uang-muka-konsultan.store');
    Route::post('/uang-muka-konsultan/{uangMukaKonsultan}/approve', [App\Http\Controllers\UangMukaKonsultanController::class, 'approve'])->name('uang-muka-konsultan.approve');
    Route::post('/uang-muka-konsultan/{uangMukaKonsultan}/reject', [App\Http\Controllers\UangMukaKonsultanController::class, 'reject'])->name('uang-muka-konsultan.reject');

    Route::get('/berita-acara-konsultan', [App\Http\Controllers\BeritaAcaraKonsultanController::class, 'index'])->name('berita-acara-konsultan.index');
    Route::post('/berita-acara-konsultan', [App\Http\Controllers\BeritaAcaraKonsultanController::class, 'store'])->name('berita-acara-konsultan.store');
    Route::delete('/berita-acara-konsultan/{id}', [App\Http\Controllers\BeritaAcaraKonsultanController::class, 'destroy'])->name('berita-acara-konsultan.destroy');

    Route::get('/serah-terima-konsultan', [App\Http\Controllers\SerahTerimaKonsultanController::class, 'index'])->name('serah-terima-konsultan.index');
    Route::post('/serah-terima-konsultan', [App\Http\Controllers\SerahTerimaKonsultanController::class, 'store'])->name('serah-terima-konsultan.store');
    Route::post('/serah-terima-konsultan/{serahTerimaKonsultan}/approve', [App\Http\Controllers\SerahTerimaKonsultanController::class, 'approve'])->name('serah-terima-konsultan.approve');
    Route::post('/serah-terima-konsultan/{serahTerimaKonsultan}/reject', [App\Http\Controllers\SerahTerimaKonsultanController::class, 'reject'])->name('serah-terima-konsultan.reject');

    Route::get('/pembayaran-konsultan', [App\Http\Controllers\PembayaranKonsultanController::class, 'index'])->name('pembayaran-konsultan.index');
    Route::post('/pembayaran-konsultan', [App\Http\Controllers\PembayaranKonsultanController::class, 'store'])->name('pembayaran-konsultan.store');
    Route::post('/pembayaran-konsultan/{pembayaranKonsultan}/reject-pptk', [App\Http\Controllers\PembayaranKonsultanController::class, 'rejectPPTK'])->name('pembayaran-konsultan.reject-pptk');
    Route::post('/pembayaran-konsultan/{pembayaranKonsultan}/approve', [App\Http\Controllers\PembayaranKonsultanController::class, 'approve'])->name('pembayaran-konsultan.approve');
});

// ─── PROFILE ───────────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifikasi/{id}/read', [App\Http\Controllers\NotificationController::class, 'read'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [App\Http\Controllers\NotificationController::class, 'readAll'])->name('notifikasi.read-all');
});

// ─── STORAGE FILE SERVE & BACKUP RESTORE (KHUSUS HOSTING NON-SYMLINK) ───────────
// Restore file dari backup jika ada file lama
Route::get('/artisan/restore-storage', function () {
    $backups = glob(public_path('storage_backup_*'));
    $target = storage_path('app/public');
    $copied = [];

    foreach ($backups as $b) {
        if (is_dir($b)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($b, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($iterator as $item) {
                $subPath = str_replace('\\', '/', substr($item->getPathname(), strlen($b) + 1));
                $dest = $target . '/' . $subPath;
                if ($item->isDir()) {
                    if (!is_dir($dest)) @mkdir($dest, 0755, true);
                } else {
                    $destDir = dirname($dest);
                    if (!is_dir($destDir)) @mkdir($destDir, 0755, true);
                    @copy($item->getPathname(), $dest);
                    $copied[] = $subPath;
                }
            }
        }
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Restore selesai',
        'copied_count' => count($copied),
        'copied_files' => $copied,
    ]);
});

// Melayani file storage secara langsung via Laravel (tanpa butuh symlink)
Route::get('/storage/{path}', function ($path) {
    // Normalisasi slash agar kompatibel di Windows & Linux
    $cleanPath = str_replace('\\', '/', $path);
    $filename = basename($cleanPath);

    // 1. Cek direct path di storage/app/public/
    $direct = storage_path('app/public/' . $cleanPath);
    if (file_exists($direct) && !is_dir($direct)) {
        return response()->file($direct);
    }

    // 2. Cek via disk public Laravel
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
        return \Illuminate\Support\Facades\Storage::disk('public')->response($cleanPath);
    }
    
    // 3. Cek di storage/app/
    $appPath = storage_path('app/' . $cleanPath);
    if (file_exists($appPath) && !is_dir($appPath)) {
        return response()->file($appPath);
    }

    // 4. Cek di folder backup lama
    $backups = glob(public_path('storage_backup_*'));
    foreach ($backups as $b) {
        $backupPath = $b . '/' . $cleanPath;
        if (file_exists($backupPath) && !is_dir($backupPath)) {
            return response()->file($backupPath);
        }
    }

    // 5. Pencarian rekursif jika lokasi folder tersimpan agak berbeda
    $searchRoots = [
        storage_path('app/public'),
        storage_path('app'),
        public_path(),
    ];
    foreach ($searchRoots as $sRoot) {
        if (is_dir($sRoot)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sRoot, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($iterator as $item) {
                if ($item->isFile() && $item->getFilename() === $filename) {
                    return response()->file($item->getPathname());
                }
            }
        }
    }

    // Diagnosa jika file benar-benar belum pernah terunggah ke server
    return response()->json([
        'status' => 'error',
        'message' => 'File fisik tidak ditemukan di server. Kemungkinan file belum berhasil terunggah atau terhapus.',
        'requested_file' => $filename,
        'requested_path' => $cleanPath,
        'files_in_folder' => is_dir(dirname($direct)) ? scandir(dirname($direct)) : 'folder tidak ditemukan',
    ], 404);
})->where('path', '.*')->name('storage.fallback');
