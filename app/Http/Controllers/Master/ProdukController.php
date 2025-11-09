<?php

namespace App\Http\Controllers\Master;

use DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    public function dt($jenis)
    {

        $produk = Produk::query()->{$jenis}();

        return DataTables::of($produk)
            ->addIndexColumn()
            ->editColumn('tarif', fn($row) => formatUang($row->tarif))
            ->addColumn('action', function ($row) {
                return "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Produk`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.produk.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
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

            return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => $produk->jenis]));
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->sendError(message: __('http-response.success.update', ['Attribute' => $produk->jenis]), errors: $th->getMessage(), traces: $th->getTrace());
        }
    }

    public function destroy(Produk $produk)
    {
        $produk->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => $produk->jenis]));
    }
}
