<?php

namespace App\Http\Controllers\Registrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKunjunganRequest;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Ruangan;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    public function index()
    {
        return 'index';
    }
    public function create(Pasien $pasien)
    {
        $dokter = User::dokter()->get();
        $ruangan = Ruangan::all();
        $jenis_pembayaran = jenisPembayaran();
        $jenis_layanan = jenisLayanan();

        return view('registrasi.kunjungan.create', compact([
            'pasien',
            'dokter',
            'jenis_pembayaran',
            'jenis_layanan',
            'ruangan'
        ]));
    }

    public function store(StoreKunjunganRequest $request)
    {
        Kunjungan::create($request->except(['id']));

        return $this->sendResponse(message: 'Registrasi kunjungan pasien berhasil');
    }
}
