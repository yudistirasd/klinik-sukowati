<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResepApotekerRequest;
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
use Intervention\Image\Colors\Rgb\Channels\Red;
use Str;

class ResepPasienController extends Controller
{
    public function dt()
    {

        $embalaseDanJasaResep = "
            SELECT
                rd.resep_id,
                COALESCE(rd.embalase, 0) AS embalase,
                COALESCE(rd.jasa_resep, 0) AS jasa_resep,
                ROW_NUMBER() OVER(
                    PARTITION BY rd.resep_id, rd.receipt_number
                ) AS rn
            FROM resep_detail rd
            INNER JOIN resep_pasien rp ON rp.id = rd.resep_id
        ";

        $data = DB::table('resep_pasien as rsp')
            ->withExpression(
                'resep_pasien',
                DB::table('resep as rs')
                    ->select(
                        'rs.*',
                        'rs.id as resep_id',
                        'dokter.name as dokter',
                        'kj.noregistrasi',
                        'kj.tanggal_registrasi',
                        'ru.name as ruangan',
                        'ps.norm',
                        'ps.id as pasien_id',
                        'ps.nama',
                        'ps.alamat',
                        'ps.tanggal_lahir',
                        'ps.jenis_kelamin'
                    )
                    ->join('users as dokter', 'dokter.id', '=', 'rs.dokter_id')
                    ->join('pasien as ps', 'ps.id', '=', 'rs.pasien_id')
                    ->leftJoin('kunjungan as kj', 'kj.id', '=', 'rs.kunjungan_id')
                    ->leftJoin('ruangan as ru', 'ru.id', '=', 'kj.ruangan_id')
            )->withExpression(
                'obat_stok',
                DB::table('produk_stok as ps')
                    ->select(
                        'ps.produk_id',
                        'ps.harga_jual_resep as harga_jual',
                        DB::raw('SUM(ps.ready) as qty_tersedia')
                    )
                    ->join('resep_detail as rd', 'rd.produk_id', '=', 'ps.produk_id')
                    ->join('resep_pasien as rp', 'rp.id', '=', 'rd.resep_id')
                    ->groupBy('ps.produk_id', 'ps.harga_jual_resep')
            )
            ->withExpression(
                'biaya_obat',
                DB::table('resep_pasien as rsp')
                    ->select([
                        'rsp.nomor',
                        'rsp.id as resep_id',
                        DB::raw("
                                    COALESCE(
                                        SUM(
                                            CASE
                                                WHEN rsp.status = 'ORDER'
                                                    THEN rd.qty * os.harga_jual
                                                    ELSE rd.qty * pjd.harga_jual
                                            END
                                        ),
                                    0) AS total
                        ")
                    ])
                    ->join('resep_detail as rd', 'rd.resep_id', '=', 'rsp.id')
                    ->leftJoin('obat_stok as os', 'os.produk_id', '=', 'rd.produk_id')
                    ->leftJoin('penjualan_detail as pjd', function ($join) {
                        $join->on('pjd.resep_id', '=', 'rsp.id')
                            ->on('pjd.resep_detail_id', '=', 'rd.id');
                    })
                    ->groupBy('rsp.id', 'rsp.nomor')
            )->withExpression(
                'embalase_and_jasa_resep',
                DB::table(DB::raw("($embalaseDanJasaResep) as e"))
                    ->select(
                        'e.resep_id',
                        DB::raw('SUM(e.jasa_resep) AS jasa_resep'),
                        DB::raw('SUM(e.embalase) AS embalase')
                    )
                    ->where('e.rn', 1)
                    ->groupBy('e.resep_id')
            )
            ->leftJoin('biaya_obat as bo', 'bo.resep_id', '=', 'rsp.id')
            ->leftJoin('embalase_and_jasa_resep as ejr', 'ejr.resep_id', '=', 'rsp.id')
            ->leftJoin('penjualan as pj', 'pj.resep_id', '=', 'rsp.id')
            ->select([
                'rsp.*',
                'bo.total',
                'ejr.embalase',
                'ejr.jasa_resep',
                DB::raw("coalesce(pj.status, 'belum') as status_pembayaran"),
                DB::raw("coalesce(total + jasa_resep + embalase, 0) as total_akhir")
            ]);

        return DataTables::of($data)
            // ->filterColumn('alamat_lengkap', function ($query, $keyword) {
            //     $query->where('alamat', 'ilike', '%' . $keyword . '%');
            // })
            // ->filterColumn('norm', function ($query, $keyword) {
            //     $query->where('ps.norm', 'ilike', '%' . $keyword . '%');
            // })
            // ->filterColumn('nama', function ($query, $keyword) {
            //     $query->where('ps.nama', 'ilike', '%' . $keyword . '%');
            // })
            // ->filterColumn('ruangan', function ($query, $keyword) {
            //     $query->where('ru.name', 'ilike', '%' . $keyword . '%');
            // })
            // ->filterColumn('dokter', function ($query, $keyword) {
            //     $query->where('dokter.name', 'ilike', '%' . $keyword . '%');
            // })
            // ->filterColumn('nomor', function ($query, $keyword) {
            //     $query->where('rs.nomor', 'ilike', '%' . $keyword . '%');
            // })
            // ->filterColumn('status', function ($query, $keyword) {
            //     $query->where('rs.status', 'ilike', '%' . $keyword . '%');
            // })
            ->addIndexColumn()
            ->editColumn('nomor', function ($row) {
                $color = $row->asal_resep == 'IN' ? 'bg-blue-lt text-blue-lt-fg' : 'bg-dark-lt text-dark-lt-fg';
                $badgeNomor =  "<span class='badge {$color}'>{$row->nomor}</span>";

                return "{$badgeNomor}";
            })
            ->addColumn('usia', fn($row) => hitungUsiaPasien($row->tanggal_lahir, $row->tanggal_registrasi ?? null))
            ->editColumn('metode_penulisan', function ($row) {
                $colorMetode = $row->metode_penulisan == 'manual' ? 'bg-warning text-warning-fg' : 'bg-success text-success-fg';
                $iconMetode = $row->metode_penulisan == 'manual' ? 'writing' : 'pill';
                $textMode = $row->metode_penulisan == 'manual' ? 'Tulis Manual' : 'Pilih Obat';

                return "<span class='badge badge-sm {$colorMetode}'>
                  <i class='ti ti-{$iconMetode}'></i>{$textMode}
                </span>";
            })
            ->editColumn('total_akhir', fn($row) => formatUang($row->total_akhir))
            ->editColumn('status_bayar', function ($row) {
                $color = $row->status_pembayaran == 'lunas' ? 'bg-green text-green-fg' : 'bg-yellow text-yellow-fg';
                return "<span class='badge {$color} text-uppercase'>{$row->status_pembayaran}</span>";
            })
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
                'nomor',
                'action',
                'status',
                'metode_penulisan',
                'status_bayar'
            ])
            ->make(true);
    }

    public function obat(Resep $resep)
    {

        $resep->load(['penjualan', 'pasien', 'kunjungan', 'kunjungan.ruangan', 'dokter']);

        $data = DB::select("
            WITH obat_stok AS (
                SELECT
                    ps.produk_id,
                    ps.harga_jual_resep as harga_jual,
                    SUM ( ps.ready ) as qty_tersedia
                FROM
                    produk_stok ps
                WHERE
                    produk_id IN ( SELECT produk_id FROM resep_detail WHERE resep_id = ? )
                GROUP BY
                    ps.produk_id,
                    ps.harga_jual_resep
            )
            select
                rs.id as resep_id,
                rs.status,
                rs.nomor,
                rs.asal_resep,
                pr.name || ' ' || pr.dosis || ' ' || pr.satuan as obat,
                pr.sediaan,
                pr.satuan as satuan_dosis,
                rd.id as detail_resep_id,
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
                rd.embalase,
                rd.jasa_resep,
                rd.waktu_pemberian_obat,
                ap.name as aturan_pakai,
                os.qty_tersedia,
                (
                    case
                        when rs.status = 'ORDER' then
                            os.harga_jual
                        else
                            pjd.harga_jual
                    end
                ) as harga_jual,
                 (
                    case
                        when rs.status = 'ORDER' then
                            rd.qty * os.harga_jual
                        else
                            rd.qty * pjd.harga_jual
                    end
                ) as total,
                kpo.name as kondisi_pemberian
            from resep as rs
            inner join resep_detail as rd on rd.resep_id = rs.id
            inner join produk as pr on pr.id = rd.produk_id
            inner join aturan_pakai_obat as ap on ap.id = rd.aturan_pakai_id
            LEFT JOIN obat_stok as os ON os.produk_id = rd.produk_id
            left join kondisi_pemberian_obat as kpo ON kpo.id = rd.kondisi_pemberian_obat_id
            left join penjualan_detail as pjd on pjd.resep_id = rs.id
                and pjd.resep_detail_id = rd.id
                and pjd.produk_id = rd.produk_id
            where rs.id = ?
        ", [$resep->id, $resep->id]);

        $totalTagihan = 0;
        $details = collect($data)->map(function ($row) use (&$totalTagihan) {
            if (!empty($row->waktu_pemberian_obat)) {
                $row->waktu_pemberian_obat = implode(", ", json_decode($row->waktu_pemberian_obat));
            }

            if ($row->jenis_resep == 'non_racikan') {
                $totalTagihan += $row->embalase + $row->jasa_resep + $row->total;
            }

            return $row;
        });

        $resep->tanggal = Carbon::parse($resep->created_at)->translatedFormat('d F Y');

        if (!$details->isEmpty()) {
            // non racikan
            $items = $details->where('resep_id', $resep->id)->where('jenis_resep', 'non_racikan');

            // racikan
            $headerRacikan = $details->where('resep_id', $resep->id)->where('jenis_resep', 'racikan')->groupBy('receipt_number')->map->first();


            foreach ($headerRacikan as $header) {
                $item = (object) [
                    'resep_id' => $header->resep_id,
                    'detail_resep_id' => $header->detail_resep_id,
                    'jenis_resep'       => $header->jenis_resep,
                    'receipt_number'      => $header->receipt_number,
                    'tipe_racikan'        => $header->tipe_racikan,
                    'jumlah_racikan'      => $header->jumlah_racikan,
                    'kemasan_racikan'     => $header->kemasan_racikan,
                    'signa'               => $header->signa,
                    'aturan_pakai'        => $header->aturan_pakai,
                    'kondisi_pemberian' => $header->kondisi_pemberian,
                    'waktu_pemberian_obat' => $header->waktu_pemberian_obat,
                    'catatan' => $header->catatan,
                    'embalase' => $header->embalase,
                    'jasa_resep' => $header->jasa_resep,
                    'obat' => "Racikan " . tipeRacikan($header->tipe_racikan),
                    'komposisi' => $details->where('receipt_number', $header->receipt_number)
                        ->where('jenis_resep', 'racikan')
                ];

                $totalTagihan += $details->where('receipt_number', $header->receipt_number)
                    ->where('jenis_resep', 'racikan')
                    ->sum('total');

                $totalTagihan += $header->embalase + $header->jasa_resep;


                $items->push($item);
            }
            $resep->items = $items->sortBy('receipt_number');
        } else {
            $resep->items = [];
        }

        $resep->total_tagihan = $totalTagihan;
        $resep->total_tagihan_view = formatUang($totalTagihan);
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

        $dokter = User::dokter('Y')->get();

        return view('farmasi.resep-pasien.create', compact(['pasien', 'dokter']));
    }

    public function show(Resep $resep)
    {
        $pasien = Pasien::find($resep->pasien_id);
        $kunjungan = Kunjungan::find($resep->kunjungan_id);
        return view('farmasi.resep-pasien.show', compact(['pasien', 'kunjungan', 'resep']));
    }

    public function storeResepExternal(Request $request)
    {
        $request->validate([
            'tanggal' => 'required',
            'pasien_id' => 'required',
            'dokter_id' => 'required'
        ]);

        $resep = Resep::create([
            'tanggal' => $request->tanggal,
            'pasien_id' => $request->pasien_id,
            'dokter_id' => $request->dokter_id,
            'asal_resep' => 'EX',
        ]);

        return $this->sendResponse(data: $resep, message: __('http-response.success.store', ['Attribute' => 'Resep External']));
    }


    public function storeResepDetail(Resep $resep, StoreResepApotekerRequest $request)
    {
        DB::beginTransaction();

        try {

            if ($request->jenis_resep == 'non_racikan') {
                ResepDetail::create([
                    'resep_id' => $resep->id,
                    'produk_id' => $request->produk_id,
                    'signa' => $request->signa,
                    'frekuensi' => $request->frekuensi,
                    'unit_dosis' => $request->unit_dosis,
                    'lama_hari' => $request->lama_hari,
                    'qty' => $request->qty,
                    'aturan_pakai_id' => $request->aturan_pakai_id,
                    'embalase' => $request->embalase ?? null,
                    'jasa_resep' => $request->jasa_resep ?? null,
                    'catatan' => $request->catatan,
                    'waktu_pemberian_obat' => $request->waktu_pemberian_obat,
                    'kondisi_pemberian_obat_id' => $request->kondisi_pemberian_obat_id
                ]);
            }

            if ($request->jenis_resep == 'racikan') {
                $receipt_number = ResepDetail::where('resep_id', $resep->id)
                    ->max('receipt_number') + 1;

                $komposisiRacikan = $request->komposisi_racikan;

                foreach ($komposisiRacikan as $komposisi) {
                    $komposisi = (object) $komposisi;

                    // hitung qty berdasarkan total_dosis_obat dan jumlah_racikan
                    if ($request->tipe_racikan == 'non_dtd') {
                        // dibulatkan keatas 2 desimal
                        $dosis_per_racikan = ceil($komposisi->total_dosis_obat / $request->jumlah_racikan * 100) / 100;
                        $qty = ceil($komposisi->total_dosis_obat / $komposisi->dosis_per_satuan);
                    }

                    if ($request->tipe_racikan == 'dtd') {
                        $dosis_per_racikan = $komposisi->dosis_per_racikan;
                        $qty = ceil($komposisi->dosis_per_racikan / $komposisi->dosis_per_satuan * $request->jumlah_racikan);
                    }

                    $data = [
                        'jenis_resep' => $request->jenis_resep,
                        'receipt_number' => $receipt_number,
                        'resep_id' => $resep->id,
                        'produk_id' => $komposisi->produk_id,
                        'signa' => $request->signa,
                        'frekuensi' => $request->frekuensi,
                        'unit_dosis' => $request->unit_dosis,
                        'aturan_pakai_id' => $request->aturan_pakai_id,
                        'tipe_racikan' => $request->tipe_racikan,
                        'jumlah_racikan' => $request->jumlah_racikan,
                        'kemasan_racikan' => $request->kemasan_racikan,
                        'total_dosis_obat' => $komposisi->total_dosis_obat,
                        'dosis_per_racikan' => $dosis_per_racikan,
                        'dosis_per_satuan' => $komposisi->dosis_per_satuan,
                        'qty' => $qty,
                        'embalase' => $request->embalase,
                        'jasa_resep' => $request->jasa_resep,
                        'catatan' => $request->catatan,
                        'waktu_pemberian_obat' => $request->waktu_pemberian_obat,
                        'kondisi_pemberian_obat_id' => $request->kondisi_pemberian_obat_id
                    ];

                    ResepDetail::create($data);
                }
            }


            $resep = $resep->refresh();

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Obat']), data: $resep);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.error.store', ['Attribute' => 'Obat']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function verifikasi(Resep $resep)
    {

        $details = ResepDetail::where('resep_id', $resep->id)->get();

        if ($resep->metode_penulisan == 'manual' && $details->count() == 0) {
            return $this->sendError(message: 'Resep ini masih berupa instruksi manual dari dokter. Mohon input item obat ke dalam sistem terlebih dahulu agar stok dan harga dapat dihitung');
        }

        DB::beginTransaction();

        try {

            $penjualan = Penjualan::where('resep_id', $resep->id)
                ->where('kunjungan_id', $resep->kunjungan_id)
                ->where('status', 'belum')
                ->first();

            if (empty($penjualan)) {
                $penjualan = Penjualan::create([
                    'resep_id' => $resep->id,
                    'kunjungan_id' => $resep->kunjungan_id,
                    'jenis' => 'resep',
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

                    $hargaJual = $stok->harga_jual_resep;
                    $total = $hargaJual * $terjual;

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
                        'harga_jual_tipe' => 'resep'
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

    public function jasaResep(Resep $resep, $receiptNumber, Request $request)
    {
        ResepDetail::where('resep_id', $resep->id)
            ->where('receipt_number', $receiptNumber)
            ->update([
                'embalase' => $request->embalase,
                'jasa_resep' => $request->jasa_resep
            ]);

        return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Jasa Resep & Embalase']));
    }

    public function bayarTagihan(Resep $resep, Request $request)
    {
        $request->merge([
            'status' => 'lunas'
        ]);

        Penjualan::where('resep_id', $resep->id)
            ->update($request->only(['metode_pembayaran', 'total_tagihan', 'diskon', 'total_bayar', 'cash', 'kembalian', 'status']));

        return $this->sendResponse(message: 'Tagihan Resep berhasil dibayar', data: $request->all());
    }
}
