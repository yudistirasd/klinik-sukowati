<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\StokOpnameDetail;
use App\Http\Requests\StoreStokOpnameDetailRequest;
use App\Http\Requests\UpdateStokOpnameDetailRequest;
use App\Models\ProdukStok;
use App\Models\StokOpname;
use Auth;
use DataTables;
use DB;
use Illuminate\Http\Request;
use Str;

class StokOpnameDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function dt(StokOpname $stokOpname)
    {
        $details = DB::table('stok_opname_detail as sod')
            ->join('stok_opname as so', 'so.id', '=', 'sod.stok_opname_id')
            ->join('produk as pro', 'pro.id', '=', 'sod.produk_id')
            ->select([
                'sod.*',
                'sod.id as detail_id',
                'so.status',
                DB::raw("pro.name || ' ' || pro.dosis || ' ' || pro.satuan || ' ' || pro.sediaan as obat"),
            ])
            ->where('sod.stok_opname_id', $stokOpname->id);

        return DataTables::of($details)
            ->addIndexColumn()
            ->editColumn('harga_beli', fn($row) => formatUang($row->harga_beli, true))
            ->editColumn('expired_date', fn($row) => empty($row->expired_date) ? '-' : $row->expired_date)
            ->editColumn('obat', function ($row) {
                $obat = $row->obat;

                if ($row->alasan == 'lainnya') {
                    $alasan = "Lainnya ({$row->alasan_lainnya})";
                } else {
                    $alasan = Str::ucfirst($row->alasan);
                }

                $obat .= "<br>
                    <small>
                    - Di entry oleh : {$row->created_by} <br>
                    - Alasan : {$alasan}
                    </small>
                ";

                return $obat;
            })
            ->addColumn('harga_jual_group', function ($row) {
                return "
                    <div class='row row-cols-1'>
                        <div class='d-flex justify-content-between'><strong><i class='ti ti-letter-r'></i> </strong> " . formatUang($row->harga_jual_resep, true) . "</div>
                        <div class='d-flex justify-content-between'><strong><i class='ti ti-letter-b'></i> </strong> " . formatUang($row->harga_jual_bebas, true) . "</div>
                        <div class='d-flex justify-content-between'><strong><i class='ti ti-letter-a'></i> </strong>" . formatUang($row->harga_jual_apotek, true) . "</div>
                    </div>
                ";
            })
            ->addColumn('action', function ($row) {
                if ($row->status == 'done') {
                    return "";
                }
                return "
                    <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.farmasi.stok-opname.detail.destroy', ['stok_opname' => $row->stok_opname_id, 'detail' => $row->detail_id]) . "`, table.ajax.reload)'>
                        <i class='ti ti-trash'></i>
                    </button>
                ";
            })
            ->rawColumns([
                'obat',
                'action',
                'harga_jual_group'
            ])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StokOpname $stokOpname, StoreStokOpnameDetailRequest $request)
    {
        DB::beginTransaction();

        $check = StokOpnameDetail::where('stok_opname_id', $stokOpname->id)
            ->where('produk_id', $request->produk_id)
            ->where('barcode', $request->barcode)
            ->when($request->expired_date == 'ED Kosong', function ($query) {
                $query->whereNull('expired_date');
            })
            ->count();

        if ($check > 0) {
            return $this->sendError(code: 403, message: 'Obat sudah distok opname, jika ada perubahan harap dihapus dulu');
        }


        $stokOld = ProdukStok::where('produk_id', $request->produk_id)
            ->where('barcode', $request->barcode)
            ->when($request->expired_date == 'ED Kosong', function ($query) {
                $query->whereNull('expired_date');
            })
            ->get();

        try {

            $expiredDate = ($request->expired_date == 'ED Kosong') ? null : $request->expired_date;

            $stokSystem = $stokOld->sum('ready');

            $selisih = $request->qty_real - $stokSystem;

            if ($selisih < 0) {
                $status = 'kurang';
            }

            if ($selisih > 0) {
                $status = 'lebih';
            }

            $data = [
                'stok_opname_id' => $stokOpname->id,
                'produk_id' => $request->produk_id,
                'barcode' => $request->barcode,
                'expired_date' => $expiredDate,
                'harga_beli' => (int) $stokOld->pluck('harga_beli')->first(),
                'harga_jual_resep' => (int) $stokOld->pluck('harga_jual_resep')->first(),
                'harga_jual_bebas' => (int) $stokOld->pluck('harga_jual_bebas')->first(),
                'harga_jual_apotek' => (int) $stokOld->pluck('harga_jual_apotek')->first(),
                'qty_system' => $request->qty_system,
                'qty_real' => $request->qty_real,
                'qty_selisih' => $selisih,
                'created_by' => Auth::user()->name,
                'status_stok' => $status,
                'alasan' => $request->alasan,
                'alasan_lainnya' => $request->alasan_lainnya,
                'barang_stok_backup' => json_encode($stokOld),
            ];

            if ($selisih < 0) {

                $sisaYangHarusDibuang = abs($selisih);

                foreach ($stokOld as $stok) {
                    if ($sisaYangHarusDibuang <= 0) {
                        break;
                    }

                    if ($stok->ready >= $sisaYangHarusDibuang) {
                        // Jika stok di batch ini cukup untuk menutupi selisih
                        $stok->ready -= $sisaYangHarusDibuang;


                        $stok->save();
                        $sisaYangHarusDibuang = 0;
                    } else {
                        // Jika stok di batch ini tidak cukup (habiskan batch ini, lanjut ke batch berikutnya)
                        $yangDiambil = $stok->ready;
                        $stok->ready = 0;

                        $stok->save();
                        $sisaYangHarusDibuang -= $yangDiambil;
                    }
                }
            } elseif ($selisih > 0) {
                // KASUS PENAMBAHAN (STOK FISIK LEBIH BANYAK)
                // Logic: Tambahkan ke batch PALING BARU (Last In) .

                $lastStok = $stokOld->last();

                $lastStok->ready += $selisih;
                $lastStok->save();
            }

            StokOpnameDetail::create($data);


            DB::commit();

            return $this->sendResponse(message: 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: $th->getMessage(), errors: $th->getMessage(), traces: $th->getTrace());
        }



        return $this->sendResponse(data: $stokOld);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokOpname $stokOpname, StokOpnameDetail $detail)
    {

        DB::beginTransaction();

        try {

            if (!empty($detail->barang_stok_backup)) {
                $stokBackup = json_decode($detail->barang_stok_backup);

                if (count($stokBackup) > 0) {
                    foreach ($stokBackup as $backup) {
                        ProdukStok::find($backup->id)
                            ->update(['ready' => $backup->ready]);
                    }
                }
            }

            $detail->delete();

            DB::commit();

            return $this->sendResponse(message: 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->sendError(message: $th->getMessage(), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }
}
