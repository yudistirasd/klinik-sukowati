<?php

namespace App\Http\Controllers\Registrasi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKunjunganRequest;
use App\Http\Requests\UpdateKunjunganRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Resep;
use App\Models\Ruangan;
use App\Models\User;
use Auth;
use DataTables;
use Illuminate\Http\Request;

class KunjunganController extends Controller
{
    public function dt()
    {
        $currentUser = Auth::user();

        $data = Kunjungan::query()
            ->with([
                'pasien',
                'pasien.provinsi',
                'pasien.kabupaten',
                'pasien.kecamatan',
                'pasien.kelurahan',
                'ruangan',
                'dokter',
                'asmed' => function ($query) {
                    $query->select(['id', 'kunjungan_id']);
                },
                'askep' => function ($query) {
                    $query->select(['id', 'kunjungan_id']);
                }
            ])
            ->when($currentUser->role == 'dokter', function ($query) use ($currentUser) {
                $query->where('dokter_id', $currentUser->id);
            });

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('noregistrasi', function ($row) {
                return "<a href='" . route('pemeriksaan.index', $row->id) . "'>{$row->noregistrasi}</a>";
            })
            ->editColumn('alamat', function ($row) {
                return "{$row->pasien->alamat}, {$row->pasien->kelurahan->name}, {$row->pasien->kecamatan->name}, {$row->pasien->kabupaten->name}, {$row->pasien->provinsi->name}";
            })
            ->addColumn('status', function ($row) {
                $askep = "";
                $asmed = "";

                if ($row->askep?->id) {
                    $askep = "<span class='badge badge-sm bg-green text-green-fg m-1'>Askep<i class='ti ti-checkbox ms-1'></i></span>";
                }

                if ($row->asmed?->id) {
                    $asmed = "<span class='badge badge-sm bg-blue text-blue-fg m-1'>Asmed<i class='ti ti-checkbox ms-1'></i></span>";
                }
                return "{$asmed} {$askep}";
            })
            ->addColumn('action', function ($row) use ($currentUser) {
                if ($currentUser->role != 'admin') {
                    return "";
                }

                return "
                                <a class='btn btn-warning btn-icon' href='" . route('registrasi.kunjungan.edit', $row->id) . "'>
                                    <i class='ti ti-edit'></i>
                                </a>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.registrasi.kunjungan.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'status',
                'action',
                'noregistrasi'
            ])
            ->make(true);
    }
    public function index()
    {
        return view('registrasi.kunjungan.index');
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

    public function edit(Kunjungan $kunjungan)
    {
        $kunjungan->load(['pasien', 'jenisPenyakit']);

        $dokter = User::dokter()->get();
        $ruangan = Ruangan::all();
        $jenis_pembayaran = jenisPembayaran();
        $jenis_layanan = jenisLayanan();

        $pasien = $kunjungan->pasien;

        return view('registrasi.kunjungan.edit', compact([
            'pasien',
            'dokter',
            'jenis_pembayaran',
            'jenis_layanan',
            'ruangan',
            'kunjungan'
        ]));
    }

    public function update(UpdateKunjunganRequest $request, Kunjungan $kunjungan)
    {
        Kunjungan::find($kunjungan->id)
            ->update($request->except(['id', '_method']));

        return $this->sendResponse(message: 'Registrasi kunjungan pasien berhasil diubah');
    }

    public function destroy(Kunjungan $kunjungan)
    {
        $kunjungan->delete();

        return $this->sendResponse(message: 'Registrasi kunjungan pasien berhasil dihapus');
    }
}
