<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSuplierRequest;
use App\Http\Requests\UpdateSuplierRequest;
use App\Models\Suplier;
use DataTables;
use Illuminate\Http\Request;

class SuplierController extends Controller
{
    public function dt()
    {

        $suplier = Suplier::query();

        return DataTables::of($suplier)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                return "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Suplier`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.suplier.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
    }

    public function json(Request $request)
    {
        $data = Suplier::select([
            'id',
            'name as text'
        ])
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
            ->get();

        return $this->sendResponse(data: $data);
    }

    public function index()
    {
        return view('master.suplier.index');
    }

    public function store(StoreSuplierRequest $request)
    {
        $suplier = Suplier::create($request->only(['name', 'alamat', 'telp']));

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Suplier']), data: $suplier);
    }

    public function update(UpdateSuplierRequest $request, Suplier $suplier)
    {
        Suplier::where('id', $suplier->id)->update($request->only(['name', 'alamat', 'telp']));

        return $this->sendResponse(message: __('http-response.success.update', ['Attribute' => 'Suplier']));
    }

    public function destroy(Suplier $suplier)
    {
        $suplier->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Suplier']));
    }
}
