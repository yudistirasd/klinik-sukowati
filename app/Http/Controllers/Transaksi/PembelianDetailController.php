<?php

namespace App\Http\Controllers\Transaksi;

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
                return "{$row->produk->name} {$row->produk->dosis} {$row->produk->satuan} {$row->produk->sediaan}
                    <div>
                        <div><strong>Barcode / Batch :</strong> {$row->barcode} </div>
                        <div><strong>ED:</strong>" . ($row->expired_date ?? '-') . "</div>
                    </div>
                ";
            })
            // HARGA BELI (gabungan)
            ->addColumn('harga_beli_group', function ($row) {
                return "
                    <div>
                        <div><strong>Kemasan:</strong> " . formatUang($row->harga_beli_kemasan, true) . "</div>
                        <div><strong>Satuan:</strong> " . formatUang($row->harga_beli_satuan, true) . "</div>
                    </div>
                ";
            })

            // QTY (gabungan)
            ->addColumn('qty_group', function ($row) {
                return "
                    <div>
                        <div><strong>Kemasan:</strong> {$row->jumlah_kemasan} {$row->satuan_kemasan}</div>
                        <div><strong>Stok:</strong> {$row->qty} {$row->produk->sediaan} </div>
                    </div>
                ";
            })

            // HARGA JUAL (gabungan: HJ, Untung, Margin)
            ->addColumn('harga_jual_group', function ($row) {
                return "
                    <div>
                        <div><strong>Harga Jual/Satuan:</strong> " . formatUang($row->harga_jual_satuan, true) . "</div>
                        <div><strong>Keuntungan:</strong> " . formatUang($row->keuntungan_satuan, true) . "</div>
                        <div><strong>Margin:</strong> {$row->margin}%</div>
                    </div>
                ";
            })

            // AKSI
            ->addColumn('action', function ($row) {
                if ($row->pembelian->insert_stok == 'sudah') {
                    return "";
                }
                return "
                    <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.transaksi.pembelian.detail.destroy', ['pembelian' => $row->pembelian_id, 'detail' => $row->id]) . "`, table.ajax.reload)'>
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
            'harga_jual_satuan',
            'keuntungan_satuan',
            'margin',
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
