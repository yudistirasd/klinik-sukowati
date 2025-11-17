<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\ProdukStok;
use App\Models\Resep;
use App\Models\ResepDetail;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class ResepPasienController extends Controller
{
    public function dt()
    {

        $data = DB::table('kunjungan as kj')
            ->join('pasien as ps', 'ps.id', '=', 'kj.pasien_id')
            ->join('users as dokter', 'dokter.id', '=', 'kj.dokter_id')
            ->join('ruangan as ru', 'ru.id', '=', 'kj.ruangan_id')
            ->join('provinsi as prov', 'prov.id', '=', 'ps.provinsi_id')
            ->join('kabupaten as kab', 'kab.id', '=', 'ps.kabupaten_id')
            ->join('kelurahan as kel', 'kel.id', '=', 'ps.kelurahan_id')
            ->join('kecamatan as kec', 'kec.id', '=', 'ps.kecamatan_id')
            ->join('resep as rs', 'rs.kunjungan_id', '=', 'kj.id')
            ->select([
                'kj.id',
                'kj.pasien_id',
                'kj.noregistrasi',
                DB::raw('kj.tanggal_registrasi::date tanggal_registrasi'),
                'kj.status_bayar',
                'kj.tanggal_bayar',
                'ru.name as ruangan',
                'ps.nama',
                'ps.norm',
                DB::raw("alamat || ', ' || kel.name || ', ' || kec.name  as alamat_lengkap"),
                'dokter.name as dokter',
                'rs.id as resep_id',
                'rs.nomor',
                'rs.status'
            ]);

        return DataTables::of($data)
            ->filterColumn('alamat_lengkap', function ($query, $keyword) {
                $query->where('alamat', 'ilike', '%' . $keyword . '%');
            })
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                $color = $row->status == 'VERIFIED' ? 'green' : 'orange';
                $text = Str::upper($row->status);
                return "<span class='badge bg-{$color} text-{$color}-fg'>{$text}</span>";
            })
            ->addColumn('action', function ($row) {
                if ($row->status == 'VERIFIED') {
                    return "";
                }
                return " <button class='btn btn-dark btn-sm' onclick='handleModalVerif(" . json_encode($row) . ")'>
                                    <i class='ti ti-credit-card-pay me-1'></i> Verifikasi
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
                'status',
            ])
            ->make(true);
    }

    public function index()
    {
        return view('farmasi.resep-pasien.index');
    }

    public function show(Resep $resep)
    {
        $data = DB::select("
            WITH obat_stok AS (
                SELECT
                    ps.produk_id,
                    ps.harga_jual,
                    SUM ( ps.ready ) as qty_tersedia
                FROM
                    produk_stok ps
                WHERE
                    produk_id IN ( SELECT produk_id FROM resep_detail WHERE resep_id = ? )
                GROUP BY
                    ps.produk_id,
                    ps.harga_jual
            )
            SELECT
                pro.name || ' ' || pro.dosis || ' ' || pro.satuan || ' ' || pro.sediaan as obat,
                    pro.sediaan,
                    rsd.signa,
                    tko.name as takaran,
                    apo.name as aturan_pakai,
                    rsd.qty as qty_dibutuhkan,
                    os.qty_tersedia,
                    os.harga_jual,
                    rsd.qty * os.harga_jual as total
            FROM resep_detail rsd
            JOIN produk pro ON pro.id = rsd.produk_id
            JOIN takaran_obat tko ON tko.id = rsd.takaran_id
            JOIN aturan_pakai_obat as apo ON apo.id = rsd.aturan_pakai_id
            LEFT JOIN obat_stok as os ON os.produk_id = rsd.produk_id
            WHERE rsd.resep_id = ?
        ", [$resep->id, $resep->id]);

        $totalObat = collect($data)->sum('total');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('qty_diperlukan', fn($row) => $row->qty_dibutuhkan . ' ' . $row->sediaan)
            ->editColumn('harga_jual', fn($row) => formatUang($row->harga_jual))
            ->editColumn('total', fn($row) => formatUang($row->total))
            ->with('total_obat', function () use ($totalObat) {
                return formatUang($totalObat);
            })
            ->make(true);
    }

    public function verifikasi(Resep $resep)
    {
        DB::beginTransaction();

        try {
            $details = ResepDetail::where('resep_id', $resep->id)->get();

            $penjualan = Penjualan::where('resep_id', $resep->id)
                ->where('kunjungan_id', $resep->kunjungan_id)
                ->where('status', 'belum')
                ->first();

            if (empty($penjualan)) {
                $penjualan = Penjualan::create([
                    'resep_id' => $resep->id,
                    'kunjungan_id' => $resep->kunjungan_id,
                    'jenis' => 'resep_in',
                    'tanggal' => date('Y-m-d'),
                    'created_by' => Auth::id()
                ]);
            }

            foreach ($details as $detail) {
                $stoks = ProdukStok::where('produk_id', $detail->produk_id)
                    ->where('ready', '>', 0)
                    ->orderBy('expired_date', 'asc')
                    ->get();

                if ($stoks->sum('ready') < $detail->qty) {
                    throw new \Exception("Stok {$detail->produk->name} {$detail->produk->dosis} {$detail->produk->satuan} {$detail->produk->sediaan} tidak mencukupi, stok saat ini {$stoks->sum('ready')} {$detail->produk->sediaan}", 403);
                }

                $dijual = $detail->qty;

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

                    $hargaJual = $stok->harga_jual;
                    $total = $stok->harga_jual * $terjual;

                    $detail = PenjualanDetail::create([
                        'penjualan_id' => $penjualan->id,
                        'produk_id' => $detail->produk_id,
                        'produk_stok_id' => $stok->id,
                        'kunjungan_id' => $resep->kunjungan_id,
                        'resep_id' => $resep->id,
                        'resep_detail_id' => $detail->id,
                        'harga_jual' => $hargaJual,
                        'harga_beli' => $stok->harga_beli,
                        'keuntungan' => $hargaJual - $stok->harga_beli,
                        'qty' => $terjual,
                        'total' => $total,
                    ]);
                }
            }

            $resep->verified_by = Auth::id();
            $resep->status = 'VERIFIED';
            $resep->save();

            DB::commit();
            return $this->sendResponse(message: 'Obat berhasil diverifikasi');
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
}
