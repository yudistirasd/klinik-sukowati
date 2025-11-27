<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePenjualanDetailRequest;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Produk;
use App\Models\ProdukStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class PenjualanDetailController extends Controller
{
    public function dt(Penjualan $penjualan)
    {

        $details = collect(DB::select("
        SELECT
            pr.id as produk_id,
            pr.name || ' ' || pr.dosis || ' ' || pr.satuan as obat,
            pr.sediaan,
            coalesce(sum(pd.qty), 0) as qty,
            pd.harga_jual,
            pd.harga_jual_tipe,
            ps.barcode,
            ps.expired_date,
            pj.status
        FROM
            penjualan_detail AS pd
        JOIN penjualan as pj on pj.id = pd.penjualan_id
        JOIN produk as pr ON pr.id = pd.produk_id
        JOIN produk_stok ps ON ps.id = pd.produk_stok_id
        WHERE
            pd.penjualan_id = ?
        GROUP BY pr.id, pr.name, pr.dosis, pr.satuan, pr.sediaan, pj.status, pd.harga_jual, pd.harga_jual_tipe, ps.barcode, ps.expired_date
        ", [$penjualan->id]));

        $view = view('farmasi.penjualan._table_detail', compact('penjualan', 'details'))->render();

        return $this->sendResponse(data: $view);
    }

    public function store(Penjualan $penjualan, StorePenjualanDetailRequest $request)
    {
        DB::beginTransaction();

        try {

            $produk = Produk::find($request->produk_id);

            $stoks = ProdukStok::where('produk_id', $request->produk_id)
                ->where('ready', '>', 0)
                ->orderBy('expired_date', 'asc')
                ->get();

            if ($stoks->sum('ready') < $request->qty) {
                throw new \Exception("Stok {$produk->name} {$produk->dosis} {$produk->satuan} {$produk->sediaan} tidak mencukupi, stok saat ini {$stoks->sum('ready')} {$produk->sediaan}", 403);
            }

            $dijual = $request->qty;

            foreach ($stoks as $stok) {
                if ($dijual == 0) {
                    break;
                }

                // untuk menhandle double diskon jika stok id berbeda
                // diskon hanya diberikan pada record pertama di penjualan_detail
                $tersedia = $stok->ready;

                if (($tersedia - $dijual) < 0) {
                    $stok->keluar += $tersedia;
                    $stok->ready  -= $tersedia;
                    $stok->save();

                    $terjual = $tersedia;

                    $dijual -= $terjual;
                } else {
                    $stok->keluar += $dijual;
                    $stok->ready  -= $dijual;
                    $stok->save();

                    $terjual = $dijual;
                    $dijual -= $terjual;
                }

                if ($request->jenis == 'bebas') {
                    $hargaJual = $stok->harga_jual_bebas;
                } else {
                    $hargaJual = $stok->harga_jual_apotek;
                }

                $total = $hargaJual * $terjual;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $request->produk_id,
                    'produk_stok_id' => $stok->id,
                    'harga_jual' => $hargaJual,
                    'harga_beli' => $stok->harga_beli,
                    'keuntungan' => $hargaJual - $stok->harga_beli,
                    'qty' => $terjual,
                    'total' => $total,
                    'harga_jual_tipe' => $request->jenis
                ]);
            }

            DB::commit();
            return $this->sendResponse(message: 'Obat berhasil ditambahkan');
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::error($ex);

            $message = 'Obat gagal diverifikasi';
            $code = 500;


            if ($ex->getCode() == 403) {
                $code = $ex->getCode();
                $message = $ex->getMessage();
            }

            return $this->sendError(message: $message, errors: $ex->getMessage(), traces: $ex->getTrace(), code: $code);
        }
    }

    public function destroy(Penjualan $penjualan, $produk)
    {
        DB::beginTransaction();
        try {
            $code = 200;
            $status = 'success';
            $message = 'Barang berhasil ditambahkan';

            $details = PenjualanDetail::where('penjualan_id', $penjualan->id)->where('produk_id', $produk)->get();

            $produk = Produk::find($produk);


            foreach ($details as  $detail) {
                $stokBarang = ProdukStok::find($detail->produk_stok_id);

                $keluar     = $stokBarang->keluar - $detail->qty < 0 ? 0 : $stokBarang->keluar - $detail->qty;
                $ready      = $stokBarang->ready + $detail->qty;

                $stokBarang->keluar = $keluar;
                $stokBarang->ready  = $ready;
                $stokBarang->save();

                $penjualan->save();

                PenjualanDetail::destroy($detail->id);
            }

            DB::commit();

            return $this->sendResponse(message: "Obat {$produk->name} {$produk->dosis} {$produk->satuan} {$produk->sediaan} berhasil dihapus");
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::error($ex);

            $message = 'Obat gagal dihapus';
            $code = 500;


            if ($ex->getCode() == 403) {
                $code = $ex->getCode();
                $message = $ex->getMessage();
            }

            return $this->sendError(message: $message, errors: $ex->getMessage(), traces: $ex->getTrace(), code: $code);
        }
    }
}
