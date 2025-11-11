<?php

use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\ProdukController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Registrasi\PasienController;
use App\Http\Controllers\Registrasi\KunjunganController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticationController::class, 'showLoginForm'])
    ->middleware('guest');
Route::post('/login', [AuthenticationController::class, 'authenticate'])
    ->middleware('guest')->name('login');


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('layouts.app');
    })->name('dashboard');
});



Route::group(['prefix' => 'master', 'as' => 'master.', 'middleware' => 'auth'], function () {
    Route::get('pengguna', [UserController::class, 'index'])->name('pengguna.index');
    Route::get('departemen', [DepartemenController::class, 'index'])->name('departemen.index');
    Route::get('ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('produk/{jenis}', [ProdukController::class, 'index'])->name('produk.index');
});


Route::group(['prefix' => 'registrasi', 'as' => 'registrasi.', 'middleware' => 'auth'], function () {
    Route::get('pasien', [PasienController::class, 'index'])->name('pasien.index');
    Route::get('pasien/create', [PasienController::class, 'create'])->name('pasien.create');
    Route::get('pasien/{pasien}/edit', [PasienController::class, 'edit'])->name('pasien.edit');

    Route::get('kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
    Route::get('kunjungan/{pasien}', [KunjunganController::class, 'create'])->name('kunjungan.create');
});
