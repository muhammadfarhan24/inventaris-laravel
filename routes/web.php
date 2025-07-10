<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

// API endpoints (bisa tetap di sini kalau belum pisah ke api.php)
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [UserController::class, 'index']);
Route::post('/user', [UserController::class, 'store']);
Route::put('/user/{id}', [UserController::class, 'update']);
Route::delete('/user/{id}', [UserController::class, 'destroy']);

// Route fallback untuk semua path Vue
Route::get('/{any}', function () {
    return view('app'); // Akan render resources/views/app.blade.php
})->where('any', '.*');