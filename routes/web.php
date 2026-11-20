<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratMasukController;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TemplateSuratController;
use Illuminate\Support\Facades\Route;

// ─── Auth ─────────────────────────────────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout',[LoginController::class, 'destroy'])->name('logout');

// ─── Authenticated routes ──────────────────────────────────────────────────────
Route::middleware('auth.check')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Surat Masuk
    Route::get('/surat-masuk',                [SuratMasukController::class, 'index'])->name('surat-masuk.index');
    Route::post('/surat-masuk',               [SuratMasukController::class, 'store'])->name('surat-masuk.store');
    Route::put('/surat-masuk/{id}',           [SuratMasukController::class, 'update'])->name('surat-masuk.update');
    Route::delete('/surat-masuk/{id}',        [SuratMasukController::class, 'destroy'])->name('surat-masuk.destroy');
    Route::get('/surat-masuk/{id}/berkas',    [SuratMasukController::class, 'berkas'])->name('surat-masuk.berkas');

    // Surat Keluar
    Route::get('/surat-keluar',               [SuratKeluarController::class, 'index'])->name('surat-keluar.index');
    Route::post('/surat-keluar',              [SuratKeluarController::class, 'store'])->name('surat-keluar.store');
    Route::get('/surat-keluar/buat',          [SuratKeluarController::class, 'create'])->name('surat-keluar.create');
    Route::post('/surat-keluar/buat',         [SuratKeluarController::class, 'storeFromTemplate'])->name('surat-keluar.store-template');
    Route::put('/surat-keluar/{id}',          [SuratKeluarController::class, 'update'])->name('surat-keluar.update');
    Route::delete('/surat-keluar/{id}',       [SuratKeluarController::class, 'destroy'])->name('surat-keluar.destroy');
    Route::get('/surat-keluar/{id}/dokumen',  [SuratKeluarController::class, 'dokumen'])->name('surat-keluar.dokumen');

    // Arsip
    Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // ─── Admin-only routes ────────────────────────────────────────────────────
    Route::middleware('admin.only')->group(function () {

        // Pengguna
        Route::get('/pengguna',                          [PenggunaController::class, 'index'])->name('pengguna.index');
        Route::post('/pengguna',                         [PenggunaController::class, 'store'])->name('pengguna.store');
        Route::put('/pengguna/{username}',               [PenggunaController::class, 'update'])->name('pengguna.update');
        Route::put('/pengguna/{username}/reset-password',[PenggunaController::class, 'resetPassword'])->name('pengguna.reset-password');
        Route::delete('/pengguna/{username}',            [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

        // Template Surat
        Route::get('/template',               [TemplateSuratController::class, 'index'])->name('template.index');
        Route::post('/template',              [TemplateSuratController::class, 'store'])->name('template.store');
        Route::delete('/template/{id}',       [TemplateSuratController::class, 'destroy'])->name('template.destroy');
    });
});
