<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\PembelianDetail;
use App\Http\Requests\StorePembelianDetailRequest;
use App\Http\Requests\UpdatePembelianDetailRequest;
use App\Models\Pembelian;
use DataTables;

class PembelianDetailController extends Controller
{

    public function dt(Pembelian $pembelian)
    {
        $data = PembelianDetail::query()->select('pembelian_detail.*')->with(['pembelian', 'produk'])
            ->where('pembelian_id', $pembelian->id);


        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('produk.name', function ($row) {
                return "<strong>{$row->produk->name} {$row->produk->dosis} {$row->produk->satuan} {$row->produk->sediaan}</strong>
                    <div class='row row-cols-2 text-muted'>
                        <div class='col'>Barcode / Batch : {$row->barcode} </div>
                        <div class='col'>ED : " . ($row->expired_date ?? '-') . "</div>
                        <div class='col'>Jumlah : {$row->jumlah_kemasan} {$row->satuan_kemasan}</div>
                        <div class='col'>Harga Beli Kemasan: " . formatUang($row->harga_beli_kemasan) . "/{$row->satuan_kemasan}</div>
                    </div>
                ";
            })
            // HARGA BELI (gabungan)
            ->addColumn('harga_beli_group', function ($row) {
                return formatUang($row->harga_beli_satuan, true) . " / {$row->produk->sediaan}";
            })

            // QTY (gabungan)
            ->addColumn('qty_group', function ($row) {
                return "{$row->qty} {$row->produk->sediaan}";
            })

            // HARGA JUAL (gabungan: HJ, Untung, Margin)
            ->addColumn('harga_jual_group', function ($row) {
                return "
                    <div class='row row-cols-1'>
                        <div class='d-flex justify-content-between'><strong>Resep : </strong> " . formatUang($row->harga_jual_resep, true) . "</div>
                        <div class='d-flex justify-content-between'><strong>Bebas : </strong> " . formatUang($row->harga_jual_bebas, true) . "</div>
                        <div class='d-flex justify-content-between'><strong>Apotek : </strong>" . formatUang($row->harga_jual_apotek, true) . "</div>
                    </div>
                ";
            })

            // HARGA JUAL (gabungan: HJ, Untung, Margin)
            ->addColumn('keuntungan_group', function ($row) {
                return "
                    <div class='row row-cols-1'>
                        <div class='d-flex justify-content-between'><strong>Resep : </strong> " . formatUang($row->harga_jual_resep - $row->harga_beli_satuan, true) . "</div>
                        <div class='d-flex justify-content-between'><strong>Bebas : </strong> " . formatUang($row->harga_jual_bebas - $row->harga_beli_satuan, true) . "</div>
                        <div class='d-flex justify-content-between'><strong>Apotek : </strong>" . formatUang($row->harga_jual_apotek - $row->harga_beli_satuan, true) . "</div>
                    </div>
                ";
            })

            // AKSI
            ->addColumn('action', function ($row) {
                if ($row->pembelian->insert_stok == 'sudah') {
                    return "";
                }
                return "
                    <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.farmasi.pembelian.detail.destroy', ['pembelian' => $row->pembelian_id, 'detail' => $row->id]) . "`, table.ajax.reload)'>
                        <i class='ti ti-trash'></i>
                    </button>
                ";
            })
            ->editColumn('total', fn($row) => formatUang($row->total, true))

            ->rawColumns([
                'produk.name',
                'harga_beli_group',
                'qty_group',
                'harga_jual_group',
                'keuntungan_group',
                'action',
            ])

            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePembelianDetailRequest $request)
    {
        $request->merge([
            'total' => $request->jumlah_kemasan * $request->harga_beli_kemasan
        ]);

        PembelianDetail::create($request->only([
            'pembelian_id',
            'produk_id',
            'barcode',
            'expired_date',
            'jumlah_kemasan',
            'satuan_kemasan',
            'isi_per_kemasan',
            'qty',
            'harga_beli_kemasan',
            'harga_beli_satuan',
            'harga_jual_resep',
            'harga_jual_bebas',
            'harga_jual_apotek',
            'margin_resep',
            'margin_bebas',
            'margin_apotek',
            'total'
        ]));

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Obat']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian, PembelianDetail $detail)
    {
        $detail->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Obat']));
    }
}
