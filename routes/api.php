<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\MerkController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ServiceBarangController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatusController;

//untuk route barang
Route::get('/barang', [BarangController::class, 'index']);
Route::post('/barang', [BarangController::class, 'store']);
Route::put('/barang/{id}', [BarangController::class, 'update']);
Route::delete('/barang/{id}', [BarangController::class, 'destroy']);

// untuk route kategori
Route::get('/kategori', [KategoriController::class, 'index']);
Route::post('/kategori', [KategoriController::class, 'store']);
Route::put('/kategori/{id}', [KategoriController::class, 'update']);
Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);


// untuk route merk 
Route::get('/merk', [MerkController::class, 'index']);
Route::post('/merk', [MerkController::class, 'store']);
Route::put('/merk/{id}', [MerkController::class, 'update']);
Route::delete('/merk/{id}', [MerkController::class, 'destroy']);

// untuk route ruangan 
Route::get('/ruangan', [RuanganController::class, 'index']);
Route::post('/ruangan', [RuanganController::class, 'store']);
Route::put('/ruangan/{id}', [RuanganController::class, 'update']);
Route::delete('/ruangan/{id}', [RuanganController::class, 'destroy']);

// route untuk peminjaman
Route::middleware(['auth', 'role:admin,ketua_masjid,ketua_yayasan'])->group(function () {
    // Rute untuk peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index']);
    Route::post('/peminjaman', [PeminjamanController::class, 'store']);
    Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update']);
    Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy']);
    Route::post('/peminjaman/setujui/{id}', [PeminjamanController::class, 'setujui']);
    Route::post('/peminjaman/{id}/tolak', [PeminjamanController::class, 'tolak']);
    Route::post('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan']);
});

//route untuk service barang
Route::get('/service-barang', [ServiceBarangController::class, 'index']);
Route::post('/service-barang', [ServiceBarangController::class, 'store']);
Route::get('/service-barang/{id}', [ServiceBarangController::class, 'show']); // ←  ini untuk butuh detail
Route::put('/service-barang/{id}', [ServiceBarangController::class, 'update']); // ← Untuk update
Route::delete('/service-barang/{id}', [ServiceBarangController::class, 'destroy']); // ← Untuk delete

//route untuk status
Route::get('/status', [StatusController::class, 'getStatus']);
Route::get('/barang/status/{status}', [StatusController::class, 'getBarangByStatus']);


Route::middleware(['auth'])->group(function () {
    // Semua role bisa melihat daftar user
    Route::get('/user', [UserController::class, 'index']);
    
    // Hanya admin bisa menambah, edit, hapus user
    Route::post('/user', [UserController::class, 'store']);
    Route::put('/user/{id}', [UserController::class, 'update']);
    Route::delete('/user/{id}', [UserController::class, 'destroy']);
});

// Route::middleware(['auth:sanctum', 'role:admin,ketua_masjid'])->group(function () {
//     Route::post('/peminjaman/{id}/setujui', [PeminjamanController::class, 'setujui']);
// });

// Route::middleware(['auth:sanctum', 'role:admin,ketua_masjid,ketua_yayasan'])->group(function () {
//     Route::get('/peminjaman', [PeminjamanController::class, 'index']);
// });



