<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\ProdukStok;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\User;
use Auth;
use Carbon\Carbon;
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
                return " <a class='btn btn-primary btn-icon' target='_blank' href='" . route('farmasi.resep-pasien.show', $row->resep_id) . "'>
                                    <i class='ti ti-search'></i>
                                </a>
                            ";
            })
            ->rawColumns([
                'action',
                'status',
            ])
            ->make(true);
    }

    public function obat(Resep $resep)
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
            select
                rs.id as resep_id,
                rs.status,
                rs.nomor,
                pr.name || ' ' || pr.dosis || ' ' || pr.satuan as obat,
                pr.sediaan,
                pr.satuan as satuan_dosis,
                rd.id,
                rd.signa,
                rd.qty,
                rd.lama_hari,
                rd.receipt_number,
                rd.jenis_resep,
                rd.tipe_racikan,
                rd.jumlah_racikan,
                rd.kemasan_racikan,
                rd.total_dosis_obat,
                rd.dosis_per_racikan,
                rd.dosis_per_satuan,
                rd.catatan,
                ap.name as aturan_pakai,
                os.qty_tersedia,
                os.harga_jual,
                rd.qty * os.harga_jual as total
            from resep as rs
            inner join resep_detail as rd on rd.resep_id = rs.id
            inner join produk as pr on pr.id = rd.produk_id
            inner join aturan_pakai_obat as ap on ap.id = rd.aturan_pakai_id
            LEFT JOIN obat_stok as os ON os.produk_id = rd.produk_id
            where rs.id = ?
        ", [$resep->id, $resep->id]);

        $details = collect($data);

        $resep->tanggal = Carbon::parse($resep->created_at)->translatedFormat('d F Y');

        if (!$details->isEmpty()) {
            // non racikan
            $items = $details->where('resep_id', $resep->id)->where('jenis_resep', 'non_racikan');

            // racikan
            $headerRacikan = $details->where('resep_id', $resep->id)->where('jenis_resep', 'racikan')->groupBy('receipt_number')->map->first();

            foreach ($headerRacikan as $header) {
                $item = (object) [
                    'jenis_resep'       => $header->jenis_resep,
                    'receipt_number'      => $header->receipt_number,
                    'tipe_racikan'        => $header->tipe_racikan,
                    'jumlah_racikan'      => $header->jumlah_racikan,
                    'kemasan_racikan'     => $header->kemasan_racikan,
                    'signa'               => $header->signa,
                    'aturan_pakai'        => $header->aturan_pakai,
                    'catatan' => $header->catatan,
                    'obat' => "Racikan " . tipeRacikan($header->tipe_racikan),
                    'komposisi' => $details->where('receipt_number', $header->receipt_number)
                        ->where('jenis_resep', 'racikan')
                ];


                $items->push($item);
            }
            $resep->items = $items->sortBy('receipt_number');
        } else {
            $resep->items = [];
        }

        $resep->load(['dokter']);

        $view = view('farmasi.resep-pasien._resep_table', compact('resep'))->render();

        return $this->sendResponse(data: $view);
    }

    public function index()
    {
        return view('farmasi.resep-pasien.index');
    }

    public function create(Pasien $pasien)
    {
        $pasien->load([
            'agama',
            'pekerjaan',
            'provinsi',
            'kabupaten',
            'kecamatan',
            'kelurahan',
        ]);

        $dokter = User::dokter()->get();

        return view('farmasi.resep-pasien.create', compact(['pasien', 'dokter']));
    }

    public function show(Resep $resep)
    {
        $pasien = Pasien::find($resep->pasien_id);
        $kunjungan = Kunjungan::find($resep->kunjungan_id);
        return view('farmasi.resep-pasien.show', compact(['pasien', 'kunjungan', 'resep']));
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
