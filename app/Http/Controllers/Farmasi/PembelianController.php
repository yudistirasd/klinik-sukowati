<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Http\Requests\StorePembelianRequest;
use App\Http\Requests\UpdatePembelianRequest;
use App\Models\LogPerubahanHarga;
use App\Models\PembelianDetail;
use App\Models\ProdukStok;
use Auth;
use DataTables;
use DB;
use Str;

class PembelianController extends Controller
{

    public function dt()
    {
        $data = Pembelian::query()->with(['suplier']);


        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('insert_stok', function ($row) {
                $color = $row->insert_stok == 'sudah' ? 'green' : 'orange';
                $text = Str::upper($row->insert_stok);
                return "<span class='badge bg-{$color} text-{$color}-fg'>{$text}</span>";
            })
            ->addColumn('action', function ($row) {
                $btn = "<a class='btn btn-primary btn-icon' href='" . route('farmasi.pembelian.show', $row->id) . "'>
                                    <i class='ti ti-search'></i>
                                </a>";

                if ($row->insert_stok == 'belum') {
                    $btn .= "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Pembelian`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.farmasi.pembelian.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
                }
                return $btn;
            })
            ->rawColumns([
                'insert_stok',
                'action',
            ])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('farmasi.pembelian.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePembelianRequest $request)
    {
        $request->merge(['created_by' => Auth::id()]);

        $pembelian = Pembelian::create($request->only(['tanggal', 'suplier_id', 'created_by', 'tgl_faktur', 'no_faktur', 'tanggal_obat_datang']));

        return $this->sendResponse(data: $pembelian, message: __('http-response.success.store', ['Attribute' => 'Pembelian']));
    }

    public function storeStok(Pembelian $pembelian)
    {
        $userId = Auth::id();

        DB::beginTransaction();
        try {

            $detail = PembelianDetail::where('pembelian_id', $pembelian->id)->get();

            foreach ($detail as $item) {
                $keuntunganResepBaru = $item->harga_jual_resep - $item->harga_beli_satuan;
                $keuntunganBebasBaru = $item->harga_jual_bebas - $item->harga_beli_satuan;
                $keuntunganApotekBaru = $item->harga_jual_apotek - $item->harga_beli_satuan;

                $oldStok = ProdukStok::where('produk_id', $item->produk_id)
                    ->whereRaw('ready > 0')
                    ->get();

                if ($oldStok->count() > 0) {
                    foreach ($oldStok as  $stok) {
                        $data = [
                            'stok_id' => $stok->id,
                            'harga_jual_resep_lama' => $stok->harga_jual_resep,
                            'harga_jual_resep_baru' => $item->harga_jual_resep,
                            'keuntungan_resep_lama' => $stok->harga_jual_resep - $stok->harga_beli_satuam,
                            'keuntungan_resep_baru' => $keuntunganResepBaru,

                            'harga_jual_bebas_lama' => $stok->harga_jual_bebas,
                            'harga_jual_bebas_baru' => $item->harga_jual_bebas,
                            'keuntungan_bebas_lama' => $stok->harga_jual_bebas - $stok->harga_beli_satuam,
                            'keuntungan_bebas_baru' => $keuntunganBebasBaru,

                            'harga_jual_apotek_lama' => $stok->harga_jual_apotek,
                            'harga_jual_apotek_baru' => $item->harga_jual_apotek,
                            'keuntungan_apotek_lama' => $stok->harga_jual_apotek - $stok->harga_beli_satuam,
                            'keuntungan_apotek_baru' => $keuntunganApotekBaru,

                            'keterangan' => 'Update harga terakhir otomatis oleh sistem.'
                        ];
                        LogPerubahanHarga::create($data);
                        $stok->harga_jual_resep = $item->harga_jual_resep;
                        $stok->harga_jual_bebas = $item->harga_jual_bebas;
                        $stok->harga_jual_apotek = $item->harga_jual_apotek;
                        $stok->harga_terakhir_id = $item->id;
                        $stok->save();
                    }
                }
            }



            DB::select("
                INSERT INTO produk_stok (
                    id,
                    produk_id,
                    pembelian_id,
                    pembelian_detail_id,
                    tanggal_stok,
                    barcode,
                    expired_date,
                    harga_beli,
                    harga_jual_resep,
                    harga_jual_bebas,
                    harga_jual_apotek,
                    masuk,
                    keluar,
                    ready,
                    created_by,
                    created_at,
                    updated_at
                )
                select
                    gen_random_uuid(),
                    a.produk_id,
                    a.pembelian_id,
                    a.id as pembelian_detail_id,
                    b.tanggal as tanggal_stok,
                    a.barcode,
                    a.expired_date,
                    a.harga_beli_satuan as harga_beli,
                    a.harga_jual_resep,
                    a.harga_jual_bebas,
                    a.harga_jual_apotek,
                    a.qty,
                    0,
                    a.qty,
                    '$userId',
                    CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta' as created_at,
                    CURRENT_TIMESTAMP AT TIME ZONE 'Asia/Jakarta' as updated_at
                from pembelian_detail a
                    join pembelian b
                        on b.id = a.pembelian_id
                where a.pembelian_id = ?
                ON CONFLICT (produk_id, pembelian_id, pembelian_detail_id) DO NOTHING;
            ", [$pembelian->id]);
            $pembelian->insert_stok = 'sudah';
            $pembelian->save();

            DB::commit();


            return $this->sendResponse(message: 'Obat berhasil ditambahkan ke stok');
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::error($ex);

            return $this->sendError(message: 'Obat gagal ditambahkan ke stok', errors: $ex->getMessage(), traces: $ex->getTrace());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        $pembelian->load('suplier');

        return view('farmasi.pembelian.show', compact('pembelian'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePembelianRequest $request, Pembelian $pembelian)
    {

        Pembelian::find($pembelian->id)->update($request->only(['tanggal', 'suplier_id', 'tgl_faktur', 'no_faktur', 'tanggal_obat_datang']));

        return $this->sendResponse(data: $pembelian, message: __('http-response.success.update', ['Attribute' => 'Pembelian']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        $pembelian->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Pembelian']));
    }
}
