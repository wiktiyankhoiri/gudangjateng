<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\PabrikController;
use App\Http\Controllers\InitialStokController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PenyesuaianStokController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LaporanSalesStokController;
use App\Http\Controllers\LaporanBarangMasukController;
use App\Http\Controllers\LaporanBarangKeluarController;
use App\Http\Controllers\LaporanMutasiController;
use App\Http\Controllers\StokOpnameController;
Route::get('/', [LoginController::class, 'showLoginForm'])->name('home');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/beranda', [DashboardController::class, 'index'])->name('beranda');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register')->middleware('role:admin');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('role:admin');

    // ===================== MASTER DATA =====================
    Route::prefix('masterdata')->name('masterdata.')->middleware('role:admin')->group(function () {
        Route::resource('barang', BarangController::class)->except(['show']);
        Route::resource('toko', TokoController::class)->except(['show']);
        Route::resource('pabrik', PabrikController::class)->except(['show']);

        Route::post('barang/delete/{barang}', [BarangController::class, 'destroy'])->name('barang.delete');
        Route::get('barang/template', [BarangController::class, 'template'])->name('barang.template');
        Route::get('barang/export', [BarangController::class, 'export'])->name('barang.export');
        Route::post('barang/import', [BarangController::class, 'import'])->name('barang.import');
        Route::post('toko/delete/{toko}', [TokoController::class, 'destroy'])->name('toko.delete');
        Route::get('toko/template', [TokoController::class, 'template'])->name('toko.template');
        Route::get('toko/export', [TokoController::class, 'export'])->name('toko.export');
        Route::post('toko/import', [TokoController::class, 'import'])->name('toko.import');
        Route::post('pabrik/delete/{pabrik}', [PabrikController::class, 'destroy'])->name('pabrik.delete');
        Route::get('pabrik/template', [PabrikController::class, 'template'])->name('pabrik.template');
        Route::get('pabrik/export', [PabrikController::class, 'export'])->name('pabrik.export');
        Route::post('pabrik/import', [PabrikController::class, 'import'])->name('pabrik.import');
    });

    // ===================== TRANSAKSI =====================
    // CRUD Transaksi - admin only
    Route::prefix('transaksi')->name('transaksi.')->middleware('role:admin')->group(function () {
        Route::resource('barangmasuk', BarangMasukController::class)->except(['show']);
        Route::resource('barangkeluar', BarangKeluarController::class)->except(['show']);
        Route::resource('mutasi', MutasiController::class)->except(['show']);

        Route::post('barangmasuk/delete/{barangMasuk}', [BarangMasukController::class, 'destroy'])->name('barangmasuk.delete');
        Route::post('barangkeluar/delete/{barangKeluar}', [BarangKeluarController::class, 'destroy'])->name('barangkeluar.delete');
        Route::post('mutasi/delete/{mutasi}', [MutasiController::class, 'destroy'])->name('mutasi.delete');
    });

    // Detail Transaksi - broader access
    Route::prefix('transaksi')->name('transaksi.')->middleware('role:admin,audit,manager,sales,staff')->group(function () {
        Route::get('barangmasuk/detail/{barangMasuk}', [BarangMasukController::class, 'detail'])->name('barangmasuk.detail');
        Route::get('barangkeluar/detail/{barangKeluar}', [BarangKeluarController::class, 'detail'])->name('barangkeluar.detail');
    });

    Route::prefix('transaksi')->name('transaksi.')->middleware('role:admin,audit,manager')->group(function () {
        Route::get('mutasi/detail/{mutasi}', [MutasiController::class, 'detail'])->name('mutasi.detail');
    });

    // Penyesuaian Stok - audit only
    Route::prefix('transaksi')->name('transaksi.')->middleware('role:audit')->group(function () {
        Route::get('penyesuaianstok', [PenyesuaianStokController::class, 'index'])->name('penyesuaianstok.index');
        Route::get('penyesuaianstok/create', [PenyesuaianStokController::class, 'create'])->name('penyesuaianstok.create');
        Route::post('penyesuaianstok/store', [PenyesuaianStokController::class, 'store'])->name('penyesuaianstok.store');
        Route::get('penyesuaianstok/detail/{penyesuaianStok}', [PenyesuaianStokController::class, 'detail'])->name('penyesuaianstok.detail');
    });

    // ===================== Stok Opname =====================
    Route::prefix('transaksi')->name('transaksi.')->middleware('role:admin,audit')->group(function () {
        Route::get('stok-opname', [StokOpnameController::class, 'index'])->name('stokopname.index');
        Route::get('stok-opname/create', [StokOpnameController::class, 'create'])->name('stokopname.create');
        Route::post('stok-opname/store', [StokOpnameController::class, 'store'])->name('stokopname.store');
        Route::get('stok-opname/detail/{stokOpname}', [StokOpnameController::class, 'detail'])->name('stokopname.detail');
        Route::post('stok-opname/selesaikan/{stokOpname}', [StokOpnameController::class, 'selesaikan'])->name('stokopname.selesaikan');
        Route::post('stok-opname/terapkan/{stokOpname}', [StokOpnameController::class, 'terapkan'])->name('stokopname.terapkan');
        Route::post('stok-opname/batalkan/{stokOpname}', [StokOpnameController::class, 'batalkan'])->name('stokopname.batalkan');
        Route::get('stok-opname/template', [StokOpnameController::class, 'template'])->name('stokopname.template');
        Route::post('stok-opname/import', [StokOpnameController::class, 'import'])->name('stokopname.import');
    });

    // ===================== LAPORAN =====================
    Route::prefix('laporan')->name('laporan.')->middleware('role:admin,audit,manager,sales,staff')->group(function () {
        Route::get('salesstok', [LaporanSalesStokController::class, 'index'])->name('salesstok.index');
        Route::get('salesstok/export', [LaporanSalesStokController::class, 'export'])->name('salesstok.export');
        Route::get('barangmasuk', [LaporanBarangMasukController::class, 'index'])->name('barangmasuk.index');
        Route::get('barangkeluar', [LaporanBarangKeluarController::class, 'index'])->name('barangkeluar.index');
        Route::get('kartustok', [KartuStokController::class, 'index'])->name('kartustok.index');
    });

    Route::prefix('laporan')->name('laporan.')->middleware('role:admin,audit,manager')->group(function () {
        Route::get('mutasi', [LaporanMutasiController::class, 'index'])->name('mutasi.index');
    });

    // ===================== PENGATURAN =====================
    // User & Initial Stok - admin only
    Route::prefix('pengaturan')->name('pengaturan.')->middleware('role:admin')->group(function () {
        Route::resource('user', UserController::class)->except(['show']);
        Route::post('user/delete/{user}', [UserController::class, 'destroy'])->name('user.delete');

        Route::resource('initialstok', InitialStokController::class)->except(['show', 'destroy']);
        Route::get('initialstok/template', [InitialStokController::class, 'template'])->name('initialstok.template');
        Route::get('initialstok/export', [InitialStokController::class, 'export'])->name('initialstok.export');
        Route::post('initialstok/import', [InitialStokController::class, 'import'])->name('initialstok.import');

        Route::delete('backup/delete/{filename}', [\App\Http\Controllers\BackupController::class, 'deleteBackup'])->name('backup.delete');
        Route::get('restore', [\App\Http\Controllers\RestoreController::class, 'restore'])->name('backup.restore');
        Route::post('restore/do', [\App\Http\Controllers\RestoreController::class, 'doRestore'])->name('backup.doRestore')->middleware('throttle:3,30');
    });

    // Backup (view) & Audit Log - admin & audit
    Route::prefix('pengaturan')->name('pengaturan.')->middleware('role:admin,audit')->group(function () {
        Route::get('backup', [\App\Http\Controllers\BackupController::class, 'backup'])->name('backup.index');
        Route::post('backup/do', [\App\Http\Controllers\BackupController::class, 'doBackup'])->name('backup.do');
        Route::get('backup/download/{filename}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');

        Route::get('auditlog', [AuditLogController::class, 'index'])->name('auditlog.index');
        Route::get('auditlog/detail/{auditLog}', [AuditLogController::class, 'detail'])->name('auditlog.detail');

        Route::get('tentangsistem', [\App\Http\Controllers\TentangSistemController::class, 'index'])->name('tentangsistem.index');
        Route::post('tentangsistem/prune-audit', [\App\Http\Controllers\TentangSistemController::class, 'pruneAuditLogs'])->name('tentangsistem.prune-audit');
        Route::post('tentangsistem/clear-cache', [\App\Http\Controllers\TentangSistemController::class, 'clearCache'])->name('tentangsistem.clear-cache');
        Route::post('tentangsistem/cleanup-logs', [\App\Http\Controllers\TentangSistemController::class, 'cleanupLogs'])->name('tentangsistem.cleanup-logs');
});

    // ===================== SEARCH =====================
    Route::get('search', [SearchController::class, 'index'])->name('search')->middleware('role:admin,audit,manager,sales,staff');

    // ===================== NOTIFIKASI =====================
    Route::get('notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::post('notifications/read/{notification}', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::get('notifikasi', [NotificationController::class, 'all'])->name('notifications.all');});
