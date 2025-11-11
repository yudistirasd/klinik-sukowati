<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsesmenKeperawatanRequest;
use App\Models\AsesmenKeperawatan;
use App\Models\AsesmenMedis;
use App\Models\Kunjungan;
use Illuminate\Http\Request;

class PemeriksaanController extends Controller
{
    public function index(Kunjungan $kunjungan)
    {
        $kunjungan->load(['pasien', 'ruangan', 'dokter']);
        $pasien = $kunjungan->pasien;
        $asesmenKeperawatan = AsesmenKeperawatan::where('kunjungan_id', $kunjungan->id)->with('petugas')->first();
        $asesmenMedis = AsesmenMedis::where('kunjungan_id', $kunjungan->id)->first();

        return view('pemeriksaan.index', compact([
            'pasien',
            'kunjungan',
            'asesmenKeperawatan',
            'asesmenMedis'
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
}
