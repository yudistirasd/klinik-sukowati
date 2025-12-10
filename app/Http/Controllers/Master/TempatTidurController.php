<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTempatTidurRequest;
use App\Models\Ruangan;
use App\Models\TempatTidur;
use DataTables;
use Illuminate\Http\Request;

class TempatTidurController extends Controller
{

    public function dt(Ruangan $ruangan)
    {
        $query = TempatTidur::query()->where('ruangan_id', $ruangan->id);

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                return "<button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.master.ruangan.tempat-tidur.destroy', ['ruangan' => $row->ruangan_id, 'tempat_tidur' => $row->id]) . "`, table.ajax.reload)'>
                            <i class='ti ti-trash'></i>
                         </button>";
            })
            ->rawColumns([
                'action',
            ])
            ->make(true);
    }

    public function select2(Request $request)
    {
        $data = TempatTidur::select([
            'id as value',
            'name as text',
            '*'
        ])
            ->where('ruangan_id', $request->ruangan_id)
            ->where('status', 'kosong')
            ->get();

        return $this->sendResponse(data: $data);
    }

    public function index(Ruangan $ruangan)
    {
        return view('master.ruangan.tempat-tidur', compact('ruangan'));
    }

    public function store(StoreTempatTidurRequest $request, Ruangan $ruangan)
    {
        TempatTidur::create($request->only(['name', 'ruangan_id']));

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Tempat tidur']));
    }

    public function destroy(Ruangan $ruangan, TempatTidur $tempatTidur)
    {
        $tempatTidur->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Ruangan']));
    }
}
