<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Penjualan;
use App\Models\Resep;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanPasienController extends Controller
{
    public function dt()
    {

        $data = DB::table('kunjungan_pasien as kp')
            ->withExpression(
                'kunjungan_pasien',
                DB::table('kunjungan as kj')
                    ->join('pasien as ps', 'ps.id', '=', 'kj.pasien_id')
                    ->join('users as dokter', 'dokter.id', '=', 'kj.dokter_id')
                    ->join('ruangan as ru', 'ru.id', '=', 'kj.ruangan_id')
                    ->join('provinsi as prov', 'prov.id', '=', 'ps.provinsi_id')
                    ->join('kabupaten as kab', 'kab.id', '=', 'ps.kabupaten_id')
                    ->join('kelurahan as kel', 'kel.id', '=', 'ps.kelurahan_id')
                    ->join('kecamatan as kec', 'kec.id', '=', 'ps.kecamatan_id')
                    ->select([
                        'kj.id',
                        'kj.pasien_id',
                        'kj.noregistrasi',
                        'kj.tanggal_registrasi',
                        'kj.status_bayar',
                        'kj.tanggal_bayar',
                        'ru.name as ruangan',
                        'ps.nama',
                        'ps.norm',
                        'ps.alamat',
                        'prov.name as provinsi',
                        'kab.name as kabupaten',
                        'kec.name as kecamatan',
                        'kel.name as kelurahan',
                        'dokter.name as dokter',
                    ])
            )
            ->withExpression(
                'pelayanan',
                DB::table('pelayanan_pasien as pp')
                    ->select([
                        'kunjungan_id',
                        DB::raw('coalesce(sum(harga), 0) as layanan')
                    ])
                    ->whereRaw('kunjungan_id in (select id from kunjungan_pasien)')
                    ->groupBy('kunjungan_id')
            )
            ->withExpression(
                'penjualan_obat',
                DB::table('penjualan_detail as pd')
                    ->select([
                        'kunjungan_id',
                        DB::raw("string_agg(DISTINCT resep_id::text, ',') as resep_id"),
                        DB::raw('coalesce(sum(total), 0) as obat')
                    ])
                    ->whereRaw('kunjungan_id in (select id from kunjungan_pasien)')
                    ->groupBy(['kunjungan_id'])
            )
            ->leftJoin('pelayanan as pl', 'pl.kunjungan_id', '=', 'kp.id')
            ->leftJoin('penjualan_obat as po', 'po.kunjungan_id', '=', 'kp.id')
            ->select([
                '*',
                DB::raw('coalesce(layanan, 0) + coalesce(obat, 0) as jumlah_tagihan'),
                DB::raw("alamat || ', ' || kelurahan || ', ' || kecamatan  || ', ' || kabupaten || ', ' || provinsi as alamat_lengkap")
            ]);

        return DataTables::of($data)
            ->filterColumn('alamat_lengkap', function ($query, $keyword) {
                $query->where('alamat', 'ilike', '%' . $keyword . '%');
            })
            ->editColumn('jumlah_tagihan', fn($row) => formatUang($row->jumlah_tagihan))
            ->addIndexColumn()
            ->addColumn('status_bayar', function ($row) {
                $status = "<span class='badge badge-sm bg-warning text-warning-fg m-1'><i class='ti ti-alert-circle me-1'></i>Belum Bayar</span>";

                if ($row->status_bayar == 'lunas') {
                    $status = "<span class='badge badge-sm bg-green text-blue-fg m-1'><i class='ti ti-checkbox ms-1'></i>Lunas</span>";
                }
                return $status;
            })
            ->addColumn('action', function ($row) {
                if ($row->status_bayar == 'lunas') {
                    return "<a href='" . route('kasir.tagihan-pasien.cetak', $row->id) . "' class='btn btn-secondary btn-sm'><i class='ti ti-printer me-1'></i>Nota</a>";
                }
                return " <button class='btn btn-dark btn-sm' onclick='handleModalBayar(" . json_encode($row) . ")'>
                                    <i class='ti ti-credit-card-pay me-1'></i> Bayar
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
                'status_bayar',
            ])
            ->make(true);
    }

    public function index()
    {
        return view('kasir.tagihan.index');
    }

    public function bayar(Request $request, Kunjungan $kunjungan)
    {
        DB::beginTransaction();

        try {

            if ($request->filled('resep_id')) {
                $resepId = explode(',', $request->resep_id);
                Penjualan::whereIn('resep_id', $resepId)->update([
                    'status' => 'lunas'
                ]);
            }

            $kunjungan->status_bayar = 'lunas';
            $kunjungan->tanggal_bayar = Carbon::now();
            $kunjungan->save();

            DB::commit();

            return $this->sendResponse(message: 'Obat berhasil ditambahkan ke stok');
        } catch (\Exception $ex) {
            DB::rollback();
            \Log::error($ex);

            return $this->sendError(message: 'Obat gagal ditambahkan ke stok', errors: $ex->getMessage(), traces: $ex->getTrace());
        }


        return $this->sendResponse(message: 'Tagihan berhasil dibayar', data: $request->all());
    }
}
