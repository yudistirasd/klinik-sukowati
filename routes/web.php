<?php

use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Cetak\TagihanPasienController as CetakTagihanPasienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kasir\TagihanPasienController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\ProdukController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\SuplierController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Registrasi\PasienController;
use App\Http\Controllers\Registrasi\KunjunganController;
use App\Http\Controllers\Farmasi\ProdukStokController;
use App\Http\Controllers\Farmasi\PembelianController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthenticationController::class, 'showLoginForm'])
    ->middleware('guest');
Route::post('/login', [AuthenticationController::class, 'authenticate'])
    ->middleware('guest')->name('login');


Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        Route::get('pengguna', [UserController::class, 'index'])->name('pengguna.index');
        Route::get('departemen', [DepartemenController::class, 'index'])->name('departemen.index');
        Route::get('ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
        Route::get('produk/{jenis}', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('suplier', [SuplierController::class, 'index'])->name('suplier.index');
    });


    Route::group(['prefix' => 'registrasi', 'as' => 'registrasi.'], function () {
        Route::get('pasien', [PasienController::class, 'index'])->name('pasien.index');
        Route::get('pasien/create', [PasienController::class, 'create'])->name('pasien.create');
        Route::get('pasien/{pasien}/edit', [PasienController::class, 'edit'])->name('pasien.edit');

        Route::get('kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
        Route::get('kunjungan/{pasien}', [KunjunganController::class, 'create'])->name('kunjungan.create');
        Route::get('kunjungan/edit/{kunjungan}', [KunjunganController::class, 'edit'])->name('kunjungan.edit');
    });

    Route::group(['prefix' => 'pemeriksaan', 'as' => 'pemeriksaan.'], function () {
        Route::get('{kunjungan}', [PemeriksaanController::class, 'index'])->name('index');
    });


    Route::get('kasir/tagihan-pasien', [TagihanPasienController::class, 'index'])->name('kasir.tagihan-pasien');
    Route::get('kasir/tagihan-pasien/cetak/{kunjungan}', [CetakTagihanPasienController::class, 'index'])->name('kasir.tagihan-pasien.cetak');

    Route::group(['prefix' => 'farmasi', 'as' => 'farmasi.'], function () {
        Route::get('pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('pembelian/{pembelian}', [PembelianController::class, 'show'])->name('pembelian.show');
        Route::get('stok-obat', [ProdukStokController::class, 'index'])->name('stok-obat.index');
    });

    Route::group(['prefix' => 'stok', 'as' => 'stok.'], function () {});
});
