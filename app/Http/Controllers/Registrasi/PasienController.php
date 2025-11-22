<?php

namespace App\Http\Controllers\Registrasi;

use DataTables;
use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Http\Requests\StorePasienRequest;
use App\Http\Requests\UpdatePasienRequest;
use App\Models\Agama;
use App\Models\Pekerjaan;
use Auth;
use Illuminate\Support\Facades\DB;

class PasienController extends Controller
{

    public function dt()
    {
        $currentUser = Auth::user();

        $data = Pasien::query()
            ->select([
                '*',
                DB::raw("
                CASE
                    WHEN EXTRACT(YEAR FROM age(tanggal_lahir)) > 0 THEN
                        CONCAT(EXTRACT(YEAR FROM age(tanggal_lahir)), ' tahun ',
                            EXTRACT(MONTH FROM age(tanggal_lahir)), ' bulan ',
                            EXTRACT(DAY FROM age(tanggal_lahir)), ' hari')
                    WHEN EXTRACT(MONTH FROM age(tanggal_lahir)) > 0 THEN
                        CONCAT(EXTRACT(MONTH FROM age(tanggal_lahir)), ' bulan ',
                            EXTRACT(DAY FROM age(tanggal_lahir)), ' hari')
                    ELSE
                        CONCAT(EXTRACT(DAY FROM age(tanggal_lahir)), ' hari')
                END AS usia
                ")
            ])
            ->with([
                'provinsi',
                'kabupaten',
                'kecamatan',
                'kelurahan',
                'agama',
                'pekerjaan'
            ]);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('alamat', function ($row) {
                return "{$row->alamat}, {$row->kelurahan->name}, {$row->kecamatan->name}, {$row->kabupaten->name}, {$row->provinsi->name}";
            })
            ->editColumn('tempat_lahir', function ($row) {
                return "{$row->tempat_lahir}, {$row->tanggal_lahir}";
            })
            ->addColumn('action', function ($row) use ($currentUser) {
                $btn = "";
                if ($currentUser->hasRole(['admin', 'loket'])) {
                    $btn .= "<a class='btn btn-primary btn-icon me-1' href='" . route('registrasi.kunjungan.create', $row->id) . "'>
                                <i class='ti ti-calendar-user'></i>
                            </a>";
                }

                if ($currentUser->hasRole(['apoteker'])) {
                    $btn .= "<a class='btn btn-primary btn-icon me-1' href='" . route('farmasi.resep-pasien.create', $row->id) . "'>
                                <i class='ti ti-checkup-list'></i>
                            </a>";
                }

                $btn .= "<a class='btn btn-warning btn-icon' href='" . route('registrasi.pasien.edit', $row->id) . "'>
                                    <i class='ti ti-edit'></i>
                                </a>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.registrasi.pasien.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>";
                return $btn;
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('registrasi.pasien.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agama = Agama::all();
        $pekerjaan = Pekerjaan::all();

        return view('registrasi.pasien.create', compact([
            'agama',
            'pekerjaan'
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePasienRequest $request)
    {
        $data = Pasien::create($request->except(['id']));

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Pasien']), data: $data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pasien $pasien) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pasien $pasien)
    {
        $agama = Agama::all();
        $pekerjaan = Pekerjaan::all();

        $pasien->load([
            'provinsi',
            'kabupaten',
            'kecamatan',
            'kelurahan',
            'agama',
            'pekerjaan'
        ]);

        return view('registrasi.pasien.edit', compact([
            'agama',
            'pekerjaan',
            'pasien'
        ]));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePasienRequest $request, Pasien $pasien)
    {
        Pasien::where('id', $pasien->id)
            ->update($request->except(['id', '_method']));

        return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Pasien']), data: $pasien);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pasien $pasien)
    {
        $pasien->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Pasien']));
    }
}
