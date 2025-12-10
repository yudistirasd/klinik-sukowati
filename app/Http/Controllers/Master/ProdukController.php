<?php

namespace App\Http\Controllers\Master;

use DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\StoreTarifInapRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\Produk;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class ProdukController extends Controller
{
    public function dt($jenis)
    {

        $produk = Produk::query()->with('ruangan')->{$jenis}();

        return DataTables::of($produk)
            ->addIndexColumn()
            ->editColumn('tarif', fn($row) => formatUang($row->tarif))
            ->editColumn('dosis', fn($row) => $row->dosis . ' ' . $row->satuan)
            ->addColumn('action', function ($row) {
                $btn = null;

                if (in_array($row->jenis, ['tindakan', 'laborat'])) {
                    $btn .= "<a class='btn btn-dark btn-icon' href='" . route('master.produk.tarif-inap', $row->id) . "'>
                                    <i class='ti ti-bed'></i>
                                </a>";
                }


                $btn .=    "    <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Produk`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.produk.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";

                return $btn;
            })
            ->addColumn('tarif_rawat_inap', function ($row) {
                // Jika produk tidak punya relasi ruangan
                if ($row->ruangan->isEmpty()) {
                    return '<span class="badge bg-dark-lt">Belum disetting</span>';
                }

                // Loop semua ruangan yang terhubung untuk ambil tarif pivotnya
                $list = '<ul class="mb-0 ps-3">';
                foreach ($row->ruangan as $ruang) {
                    // Akses pivot table menggunakan $ruang->pivot->nama_kolom
                    $tarifPivot = formatUang($ruang->pivot->tarif);
                    $namaRuang = $ruang->name; // Sesuaikan dengan nama kolom di tabel ruangan

                    $list .= "<li><small><b>{$namaRuang}:</b> {$tarifPivot}</small></li>";
                }
                $list .= '</ul>';

                return $list;
            })
            ->rawColumns([
                'tarif_rawat_inap',
                'action',
            ])
            ->make(true);
    }

    public function json(Request $request, $jenis)
    {
        $data = Produk::{$request->jenis}()
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
            ->orderBy('name', 'asc')
            ->get(['id', 'name as text', '*'])
            ->map(function ($row) {
                $row->text = "{$row->text} - " . formatUang($row->tarif);

                return $row;
            });

        return $this->sendResponse(data: $data);
    }

    public function index($jenis)
    {
        return view('master.produk.' . $jenis);
    }

    public function store(StoreProdukRequest $request)
    {

        DB::beginTransaction();

        try {
            $data = $request->except(['_method']);

            Produk::create($data);

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => $request->jenis]));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.error.store', ['Attribute' => $request->jenis]), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function update(UpdateProdukRequest $request, Produk $produk)
    {
        DB::beginTransaction();

        try {
            $data = $request->except(['_method', '_token']);

            Produk::where('id', $produk->id)->update($data);

            DB::commit();

            return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => Str::ucfirst($produk->jenis)]));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.success.update', ['Attribute' => Str::ucfirst($produk->jenis)]), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => Str::ucfirst($produk->jenis)]));
    }


    public function tarifInap(Produk $produk)
    {

        $ruangan = Ruangan::where('layanan', 'RI')->get();

        return view('master.produk.tarif-inap', compact('produk', 'ruangan'));
    }

    public function getTarifInap(Produk $produk)
    {
        $tarif = DB::table('ruangan as ru')
            ->join('produk_map_to_ruangan as pmr', 'pmr.ruangan_id', '=', 'ru.id')
            ->select([
                'ru.name as ruangan',
                'pmr.ruangan_id',
                'pmr.produk_id',
                'pmr.tarif'
            ])
            ->where('pmr.produk_id', $produk->id)
            ->get();

        return DataTables::of($tarif)
            ->addIndexColumn()
            ->editColumn('tarif', fn($row) => formatUang($row->tarif))
            ->addColumn('action', function ($row) {
                return "  <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.produk.tarif-inap.destroy', ['produk' => $row->produk_id, 'ruangan' =>  $row->ruangan_id]) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>";
            })
            ->make(true);
    }

    public function storeTarifInap(Produk $produk, StoreTarifInapRequest $request)
    {
        $produk->load('ruangan');

        $check = $produk->ruangan()->where('ruangan_id', $request->ruangan_id)->exists();

        if ($check) {
            return $this->sendError(message: 'Sudah ditambahkan');
        }

        $produk->ruangan()->attach($request->ruangan_id, ['tarif' => $request->tarif]);

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Tarif']));
    }

    public function destroyTarifInap(Produk $produk, Ruangan $ruangan)
    {

        $produk->ruangan()->detach($ruangan->id);

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Tarif']));
    }
}
