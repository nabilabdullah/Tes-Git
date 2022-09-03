<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::POST('/daftar', App\Http\Controllers\Api\RegisterController::class)->name('register');

Route::POST('/login', App\Http\Controllers\Api\LoginController::class)->name('login');

// Hanya bisa diakses jika user sudah melakukan proses login dan memiliki token JWT.
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('/logout', App\Http\Controllers\Api\LogoutController::class)->name('logout');

// Paket Foto
Route::apiResource('/paket', App\Http\Controllers\Api\PacketController::class);