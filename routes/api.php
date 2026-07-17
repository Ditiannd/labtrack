<?php

use App\Http\Controllers\Api\AlatController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DebugController;
use App\Http\Controllers\Api\KerusakanController;
use App\Http\Controllers\Api\PengembalianController;
use App\Http\Controllers\Api\PeminjamanController;
use App\Http\Controllers\Api\SiswaController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — LabTrack
|--------------------------------------------------------------------------
|
| otomatis prefix "/api" oleh Laravel dan
| memakai guard "sanctum" (Bearer Token). Struktur otorisasi:
|
|   - Publik   : login
|   - auth:sanctum saja        : me, logout, logout-all, GET alat (semua role)
|   - api.role:admin           : kelola siswa, users, dashboard
|   - api.role:admin,petugas   : validasi peminjaman, pengembalian, kerusakan,
|                                 create/update/delete alat
|   - api.role:siswa           : ajukan peminjaman
|
*/

// ── Auth (publik) ───────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Debug (khusus local): demo response HTML & dd() di Bruno ──
// Tidak butuh auth agar mudah dicoba, tapi HANYA didaftarkan kalau
// environment = local, supaya tidak pernah aktif/bocor di production.
if (app()->environment('local')) {
    Route::get('/debug/preview', [DebugController::class, 'preview']);
    Route::get('/debug/dd-example', [DebugController::class, 'ddExample']);
}

Route::middleware('auth:sanctum')->group(function () {

    // ── Profil user yang sedang login ───────────────────────
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);

    // ── Alat: GET boleh semua role, tulis hanya admin ───────
    Route::get('/alat', [AlatController::class, 'index']);
    Route::get('/alat/{alat}', [AlatController::class, 'show']);

    Route::middleware('api.role:admin')->group(function () {
        Route::post('/alat', [AlatController::class, 'store']);
        Route::put('/alat/{alat}', [AlatController::class, 'update']);
        Route::patch('/alat/{alat}', [AlatController::class, 'update']);
        // Alias khusus upload foto: PHP tidak mem-parsing body multipart pada
        // method PUT/PATCH, jadi gunakan POST + field "_method=PUT" (method spoofing).
        Route::post('/alat/{alat}', [AlatController::class, 'update']);
        Route::delete('/alat/{alat}', [AlatController::class, 'destroy']);

        // ── Manajemen data siswa (akun + profil) ────────────
        Route::apiResource('siswa', SiswaController::class)->parameters(['siswa' => 'siswa']);
        // Alias method-spoofing (sama alasannya seperti /alat di atas):
        // PHP tidak mem-parsing body multipart pada method PUT/PATCH asli,
        // jadi request multipart memakai POST + field "_method=PUT".
        Route::post('/siswa/{siswa}', [SiswaController::class, 'update']);

        // ── Manajemen akun admin/petugas ────────────────────
        Route::apiResource('users', UserController::class);
        // Alias method-spoofing untuk demo/varian multipart, lihat catatan di atas.
        Route::post('/users/{user}', [UserController::class, 'update']);

        // ── Dashboard / laporan statistik ───────────────────
        Route::get('/dashboard', [DashboardController::class, 'index']);
    });

    // ── Peminjaman: index/show untuk semua role (auto-scoped
    //    ke milik sendiri untuk siswa di dalam controller) ───
    Route::get('/peminjaman', [PeminjamanController::class, 'index']);
    Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);

    // ── Siswa: mengajukan peminjaman ─────────────────────────
    Route::middleware('api.role:siswa')->group(function () {
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);
    });

    // ── Admin & Petugas: validasi pengajuan + pengembalian ──
    Route::middleware('api.role:admin,petugas')->group(function () {
        Route::patch('/peminjaman/{peminjaman}/acc', [PeminjamanController::class, 'acc']);
        Route::patch('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak']);
        // Alias method-spoofing untuk varian multipart "Tolak Peminjaman",
        // lihat catatan di rute /alat di atas soal PHP + multipart + PUT/PATCH.
        Route::post('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak']);
        Route::post('/peminjaman/{peminjaman}/pengembalian', [PengembalianController::class, 'store']);

        Route::get('/kerusakan', [KerusakanController::class, 'index']);
    });
});
