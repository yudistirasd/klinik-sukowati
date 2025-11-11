<?php

use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\ICDController;
use App\Http\Controllers\Master\ProdukController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\WilayahController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Registrasi\KunjunganController;
use App\Http\Controllers\Registrasi\PasienController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['as' => 'api.'], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        Route::get('pengguna/dt', [UserController::class, 'dt'])->name('pengguna.dt');
        Route::get('departemen/dt', [DepartemenController::class, 'dt'])->name('departemen.dt');
        Route::get('ruangan/dt', [RuanganController::class, 'dt'])->name('ruangan.dt');
        Route::get('produk/{jenis}', [ProdukController::class, 'dt'])->name('produk.dt');

        Route::get('wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
        Route::get('wilayah/kabupaten', [WilayahController::class, 'kabupaten'])->name('wilayah.kabupaten');
        Route::get('wilayah/kecamatan', [WilayahController::class, 'kecamatan'])->name('wilayah.kecamatan');
        Route::get('wilayah/kelurahan', [WilayahController::class, 'kelurahan'])->name('wilayah.kelurahan');

        Route::get('icd10/dt', [ICDController::class, 'icd10dt'])->name('icd10.dt');


        Route::apiResources([
            'pengguna' => UserController::class,
            'departemen' => DepartemenController::class,
            'ruangan' => RuanganController::class,
            'produk' => ProdukController::class,
            'pasien' => PasienController::class,
        ], [
            'only' => ['store', 'edit', 'update', 'destroy'],
            'parameters' => [
                'departemen' => 'departemen'
            ]
        ]);
    });

    Route::group(['prefix' => 'registrasi', 'as' => 'registrasi.'], function () {
        Route::get('pasien/dt', [PasienController::class, 'dt'])->name('pasien.dt');
        Route::get('kunjungan/dt', [KunjunganController::class, 'dt'])->name('kunjungan.dt');


        Route::apiResources([
            'pasien' => PasienController::class,
            'kunjungan' => KunjunganController::class,
        ], [
            'only' => ['store', 'edit', 'update', 'destroy'],
            'parameters' => [
                'departemen' => 'departemen'
            ]
        ]);
    });

    Route::group(['prefix' => 'pemeriksaan', 'as' => 'pemeriksaan.'], function () {
        Route::post('asesmen-keperawatan', [PemeriksaanController::class, 'storeAsesmenKeperawatan'])->name('store.asesmen-keperawatan');
        Route::post('asesmen-medis', [PemeriksaanController::class, 'storeAsesmenMedis'])->name('store.asesmen-medis');
    });
});
