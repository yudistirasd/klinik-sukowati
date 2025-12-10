<?php

use App\Http\Controllers\Cetak\PenjualanController as  CetakPenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kasir\TagihanTindakanPasienController;
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
use App\Http\Controllers\Farmasi\PembelianController;
use App\Http\Controllers\Farmasi\PembelianDetailController;
use App\Http\Controllers\Farmasi\PenjualanDetailController;
use App\Http\Controllers\Farmasi\ProdukStokController;
use App\Http\Controllers\Farmasi\ResepPasienController;
use App\Http\Controllers\Farmasi\StokOpnameController;
use App\Http\Controllers\Farmasi\StokOpnameDetailController;
use App\Http\Controllers\Master\TempatTidurController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['as' => 'api.', 'middleware' => ['web', 'auth']], function () {
    Route::group(['prefix' => 'master', 'as' => 'master.'], function () {
        Route::get('pengguna/dt', [UserController::class, 'dt'])->name('pengguna.dt');
        Route::get('pengguna/{pengguna}/setting-ruangan/dt', [UserController::class, 'dtSettingRuangan'])->name('pengguna.setting-ruangan.dt');
        Route::post('pengguna/{pengguna}/setting-ruangan', [UserController::class, 'storeSettingRuangan'])->name('pengguna.setting-ruangan.store');
        Route::post('pengguna/dokter-external', [UserController::class, 'storeDokterExternal'])->name('pengguna.dokter-external.store');

        Route::get('departemen/dt', [DepartemenController::class, 'dt'])->name('departemen.dt');
        Route::get('ruangan/dt', [RuanganController::class, 'dt'])->name('ruangan.dt');
        Route::get('ruangan/select2', [RuanganController::class, 'select2'])->name('ruangan.select2');
        Route::get('ruangan/select2-tt', [TempatTidurController::class, 'select2'])->name('tempat-tidur.select2');
        Route::get('ruangan/{ruangan}/tempat-tidur/dt', [TempatTidurController::class, 'dt'])->name('ruangan.tempat-tidur.dt');
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
        Route::get('farmasi/kondisi-pemberian-obat', [FarmasiController::class, 'kondisiPemberianObat'])->name('farmasi.kondisi-pemberian-obat.get');
        Route::post('farmasi/kondisi-pemberian-obat', [FarmasiController::class, 'storeKondisiPemberianObat'])->name('farmasi.kondisi-pemberian-obat.store');

        Route::apiResource('ruangan.tempat-tidur', TempatTidurController::class)->only(['store', 'destroy']);

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
        Route::post('tindakan/{kunjungan}', [PemeriksaanController::class, 'storeTindakan'])->name('store.tindakan');
        Route::delete('tindakan/{tindakan}', [PemeriksaanController::class, 'destroyTindakan'])->name('destroy.tindakan');

        Route::get('resep', [PemeriksaanController::class, 'dtResep'])->name('get.resep');
        Route::post('resep', [PemeriksaanController::class, 'storeResep'])->name('store.resep');
        Route::delete('resep/{resep}/manual', [PemeriksaanController::class, 'destroyResepManual'])->name('destroy.resep-manual');
        Route::delete('resep/{resep}/{receipt_number}', [PemeriksaanController::class, 'destroyResepDetail'])->name('destroy.resep-detail');
    });

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('scorecard-admin', [DashboardController::class, 'scoreCardAdmin'])->name('scorecard.admin');
    });

    Route::group(['prefix' => 'kasir', 'as' => 'kasir.'], function () {
        Route::get('tagihan-pasien/dt', [TagihanTindakanPasienController::class, 'dt'])->name('tagihan-tindakan.dt');
        Route::get('tagihan-pasien/{kunjungan}', [TagihanTindakanPasienController::class, 'show'])->name('tagihan-tindakan.show');
        Route::post('tagihan-pasien/{kunjungan}', [TagihanTindakanPasienController::class, 'bayar'])->name('tagihan-tindakan.bayar');
    });

    Route::group(['prefix' => 'farmasi', 'as' => 'farmasi.'], function () {
        Route::get('pembelian/dt', [PembelianController::class, 'dt'])->name('pembelian.dt');
        Route::post('pembelian/{pembelian}', [PembelianController::class, 'storeStok'])->name('pembelian.store-stok');
        Route::get('pembelian/{pembelian}/detail/dt', [PembelianDetailController::class, 'dt'])->name('pembelian.detail.dt');

        Route::apiResource('pembelian', PembelianController::class)->only(['store', 'update', 'destroy']);
        Route::apiResource('pembelian.detail', PembelianDetailController::class)->only(['store', 'update', 'destroy']);

        Route::get('stok-obat/dt', [ProdukStokController::class, 'dt'])->name('stok-obat.dt');
        Route::get('stok-obat/select2-bebas', [ProdukStokController::class, 'select2Bebas'])->name('stok-obat.select2-bebas');
        Route::get('stok-obat/select2-stok-opname', [ProdukStokController::class, 'select2StokOpname'])->name('stok-obat.select2-stok-opname');
        Route::get('resep-pasien/dt', [ResepPasienController::class, 'dt'])->name('resep-pasien.dt');
        Route::get('resep-pasien/{resep}/obat', [ResepPasienController::class, 'obat'])->name('resep-pasien.obat');
        Route::post('resep-pasien/{resep}/verifikasi', [ResepPasienController::class, 'verifikasi'])->name('resep-pasien.verifikasi');
        Route::post('resep-pasien/{resep}/jasa-resep/{receipt_number}', [ResepPasienController::class, 'jasaResep'])->name('resep-pasien.jasa-resep');
        Route::post('resep-pasien-external', [ResepPasienController::class, 'storeResepExternal'])->name('resep-pasien.external.store');
        Route::post('resep-pasien/{resep}/detail', [ResepPasienController::class, 'storeResepDetail'])->name('resep-pasien.detail.store');
        Route::post('resep-pasien/{resep}/bayar', [ResepPasienController::class, 'bayarTagihan'])->name('resep-pasien.bayar-tagihan');

        Route::get('penjualan/{penjualan}/dt', [PenjualanDetailController::class, 'dt'])->name('penjualan.detail.dt');
        Route::post('penjualan/{penjualan}/detail', [PenjualanDetailController::class, 'store'])->name('penjualan.detail.store');
        Route::delete('penjualan/{penjualan}/destroy/{produk}', [PenjualanDetailController::class, 'destroy'])->name('penjualan.detail.destroy');

        Route::get('stok-opname/dt', [StokOpnameController::class, 'dt'])->name('stok-opname.dt');
        Route::post('stok-opname/{stok_opname}/toggle-status', [StokOpnameController::class, 'toggleStatus'])->name('stok-opname.toggle-status');
        Route::apiResource('stok-opname', StokOpnameController::class)->only(['store', 'update', 'destroy']);

        Route::get('stok-opname/{stok_opname}/detail/dt', [StokOpnameDetailController::class, 'dt'])->name('stok-opname.detail.dt');
        Route::apiResource('stok-opname.detail', StokOpnameDetailController::class)->only(['store', 'update', 'destroy']);
    });

    Route::group(['prefix' => 'cetak', 'as' => 'cetak.'], function () {
        Route::get('penjualan/print-test', [CetakPenjualanController::class, 'printTest'])->name('penjualan.print-test');
    });
});
