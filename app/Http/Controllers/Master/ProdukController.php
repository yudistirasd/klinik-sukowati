<?php

namespace App\Http\Controllers\Master;

use DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class ProdukController extends Controller
{
    public function dt($jenis)
    {

        $produk = Produk::query()->{$jenis}();

        return DataTables::of($produk)
            ->addIndexColumn()
            ->editColumn('tarif', fn($row) => formatUang($row->tarif))
            ->editColumn('dosis', fn($row) => $row->dosis . ' ' . $row->satuan)
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

    public function json(Request $request, $jenis)
    {
        $data = Produk::{$request->jenis}()
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
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
}
