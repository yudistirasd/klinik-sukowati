<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsesmenKeperawatanRequest;
use App\Http\Requests\StoreResepRequest;
use App\Models\AsesmenKeperawatan;
use App\Models\AsesmenMedis;
use App\Models\CPPT;
use App\Models\DiagnosaPasien;
use App\Models\Kunjungan;
use App\Models\PelayananPasien;
use App\Models\ProsedurePasien;
use App\Models\Resep;
use App\Models\ResepDetail;
use Auth;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemeriksaanController extends Controller
{
    public function dtDiagnosa(Request $request)
    {
        $currentUser = Auth::user();


        $data = DB::table('diagnosa_pasien as dp')
            ->join('icd10', 'icd10.id', '=', 'dp.icd10_id')
            ->select([
                'dp.id',
                'dp.created_by',
                'icd10.code',
                'icd10.display_en',
                'icd10.display_id',
            ])
            ->where('dp.pasien_id', $request->pasien_id)
            ->where('dp.kunjungan_id', $request->kunjungan_id);


        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($currentUser) {
                if ($currentUser->id != $row->created_by) {
                    return '';
                }

                return "
                                <button type='button' class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.pemeriksaan.destroy.diagnosa-pasien', $row->id) . "`, diagnosaPasienTable.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
                'noregistrasi'
            ])
            ->make(true);
    }

    public function dtProsedure(Request $request)
    {
        $currentUser = Auth::user();

        $data = DB::table('prosedure_pasien as pp')
            ->join('icd9', 'icd9.id', '=', 'pp.icd9_id')
            ->select([
                'pp.id',
                'pp.created_by',
                'icd9.code',
                'icd9.display_en',
                'icd9.display_id',
            ])
            ->where('pp.pasien_id', $request->pasien_id)
            ->where('pp.kunjungan_id', $request->kunjungan_id);


        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($currentUser) {
                if ($currentUser->id != $row->created_by) {
                    return '';
                }

                return "
                                <button type='button' class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.pemeriksaan.destroy.prosedure-pasien', $row->id) . "`, prosedurePasienTable.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
                'noregistrasi'
            ])
            ->make(true);
    }

    public function dtCppt(Request $request)
    {
        $data = DB::table('cppt as a')
            ->join('users as b', 'b.id', '=', 'a.created_by')
            ->select([
                'a.*',
                'b.name as petugas'
            ])
            ->where('a.pasien_id', $request->pasien_id);

        $currentUser = Auth::user();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('keterangan', function ($row) {
                $soap = "
                 <p><b>S</b> : {$row->subjective}</p>
                 <p><b>O</b> : {$row->objective}</p>
                 <p><b>A</b> : {$row->asesmen}</p>
                 <p><b>P</b> : {$row->asesmen}</p>
                ";

                if (!empty($row->edukasi)) {
                    $soap .= "<p><b>Edukasi Pasien</b> : {$row->edukasi}</p>";
                }

                $soap .= "<p><i class='ti ti-stethoscope fs-4'></i> <b>{$row->petugas}</b></p>";

                return $soap;
            })
            ->addColumn('action', function ($row) use ($currentUser) {
                $btn = "";
                if ($currentUser->id == $row->created_by) {
                    $btn =  "<button type='button' class='btn btn-warning btn-icon' onclick='editCppt(`" . json_encode($row) . "`, cpptPasienTable.ajax.reload)'>
                                <i class='ti ti-edit'></i>
                            </button>
                            <button type='button' class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.pemeriksaan.destroy.cppt', $row->id) . "`, cpptPasienTable.ajax.reload)'>
                                <i class='ti ti-trash'></i>
                            </button>
                        ";
                }

                return $btn;
            })
            ->rawColumns([
                'action',
                'keterangan'
            ])
            ->make(true);
    }

    public function dtTindakan(Request $request)
    {

        $currentUser = Auth::user();

        $data = PelayananPasien::query()->with('produk')
            ->where('kunjungan_id', $request->kunjungan_id);

        $total = PelayananPasien::where('kunjungan_id', $request->kunjungan_id)
            ->sum('harga');

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('harga', function ($row) {
                return formatUang($row->harga);
            })
            ->addColumn('action', function ($row) use ($currentUser) {
                if ($currentUser->role == 'perawat') {
                    return "";
                }
                return "
                                <button type='button' class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.pemeriksaan.destroy.tindakan', $row->id) . "`, tindakanPasienTable.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
            ])
            ->with('total', formatUang($total))
            ->make(true);
    }

    public function dtResep(Request $request)
    {
        $currentUser = Auth::user();

        $data = DB::table('resep as rs')
            ->join('resep_detail as rd', 'rd.resep_id', '=', 'rs.id')
            ->join('produk as pr', 'pr.id', '=', 'rd.produk_id')
            ->join('takaran_obat as tr', 'tr.id', '=', 'rd.takaran_id')
            ->join('aturan_pakai_obat as ap', 'ap.id', '=', 'rd.aturan_pakai_id')
            ->select([
                'rs.nomor',
                DB::raw("pr.name || ' ' || pr.dosis || ' ' || pr.satuan as obat"),
                'rd.id',
                'rd.signa',
                'rd.qty',
                'rd.lama_hari',
                'tr.name as takaran',
                'ap.name as aturan_pakai',
                'rs.status'
            ])
            ->where('rs.kunjungan_id', $request->kunjungan_id);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row)  use ($currentUser) {
                if ($row->status == 'VERIFIED' || $currentUser->role != 'dokter') {
                    return "";
                }
                return "
                                <button type='button' class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.pemeriksaan.destroy.resep-detail', $row->id) . "`, resepObat.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
    }

    public function index(Kunjungan $kunjungan)
    {
        $kunjungan->load(['pasien', 'ruangan', 'dokter']);
        $pasien = $kunjungan->pasien;
        $asesmenKeperawatan = AsesmenKeperawatan::where('kunjungan_id', $kunjungan->id)->with('petugas')->first();
        $asesmenMedis = AsesmenMedis::where('kunjungan_id', $kunjungan->id)->first();
        $resep = Resep::where('kunjungan_id', $kunjungan->id)
            ->where('status', 'ORDER')
            ->first();

        return view('pemeriksaan.index', compact([
            'pasien',
            'kunjungan',
            'asesmenKeperawatan',
            'asesmenMedis',
            'resep'
        ]));
    }

    public function storeAsesmenKeperawatan(AsesmenKeperawatanRequest $request)
    {
        AsesmenKeperawatan::updateOrCreate([
            'pasien_id' => $request->pasien_id,
            'kunjungan_id' => $request->kunjungan_id
        ], $request->except(['pasien_id', 'kunjungan_id']));

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Asesmen Keperawatan']));
    }

    public function storeAsesmenMedis(Request $request)
    {
        AsesmenMedis::updateOrCreate([
            'pasien_id' => $request->pasien_id,
            'kunjungan_id' => $request->kunjungan_id
        ], $request->except([
            'pasien_id',
            'kunjungan_id',
            'berat',
            'tinggi',
            'nadi',
            'suhu',
            'respirasi',
            'tekanan_darah',
        ]));

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Asesmen Medis']));
    }

    public function storeDiagnosaPasien(Request $request)
    {
        DiagnosaPasien::updateOrCreate([
            'pasien_id' => $request->pasien_id,
            'kunjungan_id' => $request->kunjungan_id,
            'icd10_id' => $request->icd10_id,
            'created_by' => $request->created_by
        ]);

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Diagnosa Pasien']));
    }

    public function destroyDiagnosaPasien(DiagnosaPasien $diagnosa)
    {
        $diagnosa->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Diagnosa Pasien']));
    }

    public function storeProsedurePasien(Request $request)
    {
        ProsedurePasien::updateOrCreate([
            'pasien_id' => $request->pasien_id,
            'kunjungan_id' => $request->kunjungan_id,
            'icd9_id' => $request->icd9_id,
            'created_by' => $request->created_by
        ]);

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Prosedure Pasien']));
    }

    public function destroyProsedurePasien(ProsedurePasien $prosedure)
    {
        $prosedure->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Prosedure Pasien']));
    }

    public function storeCppt(Request $request)
    {
        if ($request->filled('id')) {
            CPPT::find($request->id)
                ->update($request->except('id'));
        } else {
            CPPT::create($request->except('id'));
        }

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'CPPT']));
    }

    public function destroyCppt(CPPT $cppt)
    {
        $cppt->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'CPPT']));
    }

    public function storeTindakan(Request $request)
    {
        PelayananPasien::updateOrCreate([
            'pasien_id' => $request->pasien_id,
            'kunjungan_id' => $request->kunjungan_id,
            'produk_id' => $request->produk_id,
        ], [
            'harga' => $request->tarif
        ]);

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Tindakan']));
    }

    public function destroyTindakan(PelayananPasien $tindakan)
    {
        $tindakan->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Tindakan']));
    }

    public function storeResep(StoreResepRequest $request)
    {
        DB::beginTransaction();

        try {

            $resep = Resep::where('kunjungan_id', $request->kunjungan_id)
                ->where('pasien_id', $request->pasien_id)
                ->where('dokter_id', $request->dokter_id)
                ->where('status', 'ORDER')
                ->first();

            if (empty($resep)) {
                $resep = Resep::create([
                    'tanggal' => $request->tanggal,
                    'pasien_id' => $request->pasien_id,
                    'kunjungan_id' => $request->kunjungan_id,
                    'dokter_id' => $request->dokter_id
                ]);
            }

            ResepDetail::create([
                'resep_id' => $resep->id,
                'produk_id' => $request->produk_id,
                'signa' => $request->signa,
                'frekuensi' => $request->frekuensi,
                'unit_dosis' => $request->unit_dosis,
                'lama_hari' => $request->lama_hari,
                'qty' => $request->qty,
                'takaran_id' => $request->takaran_id,
                'aturan_pakai_id' => $request->aturan_pakai_id,
            ]);

            $resep = $resep->refresh();

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Obat']), data: $resep);
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.error.store', ['Attribute' => 'Obat']), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function destroyResepDetail(ResepDetail $detail)
    {
        $detail->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Obat']));
    }
}
