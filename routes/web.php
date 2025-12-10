<?php

use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Cetak\LabelPasienController;
use App\Http\Controllers\Cetak\TagihanTindakanPasienController as CetakTagihanTindakanPasienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kasir\TagihanTindakanPasienController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\ProdukController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\SuplierController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Registrasi\PasienController;
use App\Http\Controllers\Registrasi\KunjunganController;
use App\Http\Controllers\Farmasi\ProdukStokController;
use App\Http\Controllers\Farmasi\PembelianController;
use App\Http\Controllers\Farmasi\PenjualanController;
use App\Http\Controllers\Farmasi\ResepPasienController;
use App\Http\Controllers\Farmasi\StokOpnameController;
use App\Http\Controllers\Kasir\TagihanResepController;
use App\Http\Controllers\Master\TempatTidurController;
use App\Models\PenjualanDetail;
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
        Route::get('pengguna/{pengguna}/setting', [UserController::class, 'setting'])->name('pengguna.setting');
        Route::get('departemen', [DepartemenController::class, 'index'])->name('departemen.index');
        Route::get('ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
        Route::get('ruangan/{ruangan}/tempat-tidur', [TempatTidurController::class, 'index'])->name('ruangan.tempat-tidur.index');
        Route::get('produk/{jenis}', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('produk/tarif-inap/{produk}', [ProdukController::class, 'tarifInap'])->name('produk.tarif-inap');
        Route::get('suplier', [SuplierController::class, 'index'])->name('suplier.index');
    });


    Route::group(['prefix' => 'registrasi', 'as' => 'registrasi.'], function () {
        Route::get('pasien', [PasienController::class, 'index'])->name('pasien.index');
        Route::get('pasien/create', [PasienController::class, 'create'])->name('pasien.create');
        Route::get('pasien/{pasien}/edit', [PasienController::class, 'edit'])->name('pasien.edit');

        Route::get('kunjungan', [KunjunganController::class, 'index'])->name('kunjungan.index');
        Route::get('kunjungan/{pasien}', [KunjunganController::class, 'create'])->name('kunjungan.create');
        Route::get('kunjungan/edit/{kunjungan}', [KunjunganController::class, 'edit'])->name('kunjungan.edit');
        Route::get('kunjungan/{kunjungan}/cetak-label-pasien', [LabelPasienController::class, 'index'])->name('kunjungan.cetak-label');
    });

    Route::group(['prefix' => 'pemeriksaan', 'as' => 'pemeriksaan.'], function () {
        Route::get('{kunjungan}', [PemeriksaanController::class, 'index'])->name('index');
    });

    Route::group(['prefix' => 'kasir', 'as' => 'kasir.'], function () {
        Route::get('tagihan-pasien', [TagihanTindakanPasienController::class, 'index'])->name('tagihan-pasien');
        Route::get('tagihan-pasien/cetak/{kunjungan}', [CetakTagihanTindakanPasienController::class, 'index'])->name('tagihan-pasien.cetak');

        Route::get('tagihan-resep', [TagihanResepController::class, 'index'])->name('tagihan-resep.index');
    });

    Route::group(['prefix' => 'farmasi', 'as' => 'farmasi.'], function () {
        Route::get('pembelian', [PembelianController::class, 'index'])->name('pembelian.index');
        Route::get('pembelian/{pembelian}', [PembelianController::class, 'show'])->name('pembelian.show');
        Route::get('stok-obat', [ProdukStokController::class, 'index'])->name('stok-obat.index');

        Route::get('resep-pasien', [ResepPasienController::class, 'index'])->name('resep-pasien.index');
        Route::get('resep-pasien/{resep}', [ResepPasienController::class, 'show'])->name('resep-pasien.show');
        Route::get('resep-pasien/create/{pasien}', [ResepPasienController::class, 'create'])->name('resep-pasien.create');

        Route::get('penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');
        Route::get('penjualan/create', [PenjualanController::class, 'create'])->name('penjualan.create');

        Route::get('stok-opname', [StokOpnameController::class, 'index'])->name('stok-opname.index');
        Route::get('stok-opname/{stok_opname}', [StokOpnameController::class, 'show'])->name('stok-opname.show');
    });
});
