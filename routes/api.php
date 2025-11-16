<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kasir\TagihanPasienController;
use App\Http\Controllers\Master\DepartemenController;
use App\Http\Controllers\Master\FarmasiController;
use App\Http\Controllers\Master\ICDController;
use App\Http\Controllers\Master\ProdukController;
use App\Http\Controllers\Master\RuanganController;
use App\Http\Controllers\Master\SuplierController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Master\WilayahController;
use App\Http\Controllers\PemeriksaanController;
use App\Http\Controllers\Registrasi\KunjunganController;
use App\Http\Controllers\Registrasi\PasienController;
use App\Http\Controllers\Transaksi\PembelianController;
use App\Http\Controllers\Transaksi\PembelianDetailController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['as' => 'api.', 'middleware' => ['web', 'auth']], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        Route::get('pengguna/dt', [UserController::class, 'dt'])->name('pengguna.dt');
        Route::get('departemen/dt', [DepartemenController::class, 'dt'])->name('departemen.dt');
        Route::get('ruangan/dt', [RuanganController::class, 'dt'])->name('ruangan.dt');
        Route::get('produk/{jenis}', [ProdukController::class, 'dt'])->name('produk.dt');
        Route::get('produk/json/{jenis}', [ProdukController::class, 'json'])->name('produk.json');
        Route::get('suplier/dt', [SuplierController::class, 'dt'])->name('suplier.dt');
        Route::get('suplier/json', [SuplierController::class, 'json'])->name('suplier.json');

        Route::get('wilayah/provinsi', [WilayahController::class, 'provinsi'])->name('wilayah.provinsi');
        Route::get('wilayah/kabupaten', [WilayahController::class, 'kabupaten'])->name('wilayah.kabupaten');
        Route::get('wilayah/kecamatan', [WilayahController::class, 'kecamatan'])->name('wilayah.kecamatan');
        Route::get('wilayah/kelurahan', [WilayahController::class, 'kelurahan'])->name('wilayah.kelurahan');

        Route::get('icd10/dt', [ICDController::class, 'icd10dt'])->name('icd10.dt');
        Route::get('icd9/dt', [ICDController::class, 'icd9dt'])->name('icd9.dt');

        Route::get('farmasi/satuan-dosis', [FarmasiController::class, 'satuanDosis'])->name('farmasi.satuan-dosis.get');
        Route::post('farmasi/satuan-dosis', [FarmasiController::class, 'storeSatuanDosis'])->name('farmasi.satuan-dosis.store');
        Route::get('farmasi/sediaan-obat', [FarmasiController::class, 'sediaan'])->name('farmasi.sediaan.get');
        Route::post('farmasi/sediaan-obat', [FarmasiController::class, 'storeSediaan'])->name('farmasi.sediaan.store');
        Route::get('farmasi/takaran-obat', [FarmasiController::class, 'takaran'])->name('farmasi.takaran.get');
        Route::post('farmasi/takaran-obat', [FarmasiController::class, 'storeTakaran'])->name('farmasi.takaran.store');
        Route::get('farmasi/aturan-pakai-obat', [FarmasiController::class, 'aturanPakai'])->name('farmasi.aturan-pakai.get');
        Route::post('farmasi/aturan-pakai-obat', [FarmasiController::class, 'storeAturanPakai'])->name('farmasi.aturan-pakai.store');
        Route::get('farmasi/satuan-kemasan-obat', [FarmasiController::class, 'satuanKemasan'])->name('farmasi.satuan-kemasan.get');
        Route::post('farmasi/satuan-kemasan-obat', [FarmasiController::class, 'storeSatuanKemasan'])->name('farmasi.satuan-kemasan.store');

        Route::apiResources([
            'pengguna' => UserController::class,
            'departemen' => DepartemenController::class,
            'ruangan' => RuanganController::class,
            'produk' => ProdukController::class,
            'pasien' => PasienController::class,
            'suplier' => SuplierController::class,
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

        Route::get('diagnosa-pasien', [PemeriksaanController::class, 'dtDiagnosa'])->name('get.diagnosa-pasien');
        Route::post('diagnosa-pasien', [PemeriksaanController::class, 'storeDiagnosaPasien'])->name('store.diagnosa-pasien');
        Route::delete('diagnosa-pasien/{diagnosa}', [PemeriksaanController::class, 'destroyDiagnosaPasien'])->name('destroy.diagnosa-pasien');

        Route::get('prosedure-pasien', [PemeriksaanController::class, 'dtProsedure'])->name('get.prosedure-pasien');
        Route::post('prosedure-pasien', [PemeriksaanController::class, 'storeProsedurePasien'])->name('store.prosedure-pasien');
        Route::delete('prosedure-pasien/{prosedure}', [PemeriksaanController::class, 'destroyProsedurePasien'])->name('destroy.prosedure-pasien');

        Route::get('cppt', [PemeriksaanController::class, 'dtCppt'])->name('get.cppt');
        Route::post('cppt', [PemeriksaanController::class, 'storeCppt'])->name('store.cppt');
        Route::delete('cppt/{cppt}', [PemeriksaanController::class, 'destroyCppt'])->name('destroy.cppt');

        Route::get('tindakan', [PemeriksaanController::class, 'dtTindakan'])->name('get.tindakan');
        Route::post('tindakan', [PemeriksaanController::class, 'storeTindakan'])->name('store.tindakan');
        Route::delete('tindakan/{tindakan}', [PemeriksaanController::class, 'destroyTindakan'])->name('destroy.tindakan');

        Route::get('resep', [PemeriksaanController::class, 'dtResep'])->name('get.resep');
        Route::post('resep', [PemeriksaanController::class, 'storeResep'])->name('store.resep');
        Route::delete('resep/{detail}', [PemeriksaanController::class, 'destroyResepDetail'])->name('destroy.resep-detail');
    });

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('scorecard-admin', [DashboardController::class, 'scoreCardAdmin'])->name('scorecard.admin');
    });

    Route::group(['prefix' => 'kasir', 'as' => 'kasir.'], function () {
        Route::get('tagihan-pasien/dt', [TagihanPasienController::class, 'dt'])->name('tagihan.dt');
        Route::post('tagihan-pasien/{kunjungan}', [TagihanPasienController::class, 'bayar'])->name('tagihan.bayar');
    });

    Route::group(['prefix' => 'transaksi', 'as' => 'transaksi.'], function () {
        Route::get('pembelian/dt', [PembelianController::class, 'dt'])->name('pembelian.dt');
        Route::post('pembelian/{pembelian}', [PembelianController::class, 'storeStok'])->name('pembelian.store-stok');
        Route::get('pembelian/{pembelian}/detail/dt', [PembelianDetailController::class, 'dt'])->name('pembelian.detail.dt');

        Route::apiResource('pembelian', PembelianController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('pembelian.detail', PembelianDetailController::class)->only(['store', 'update', 'destroy']);
    });
});
