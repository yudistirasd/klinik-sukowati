<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\StokOpname;
use App\Http\Requests\StoreStokOpnameRequest;
use App\Http\Requests\UpdateStokOpnameRequest;
use Auth;
use DataTables;
use Str;

class StokOpnameController extends Controller
{
    public function dt()
    {
        $data = StokOpname::query()->with(['user']);


        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                $color = $row->status == 'done' ? 'green' : 'orange';
                $text = Str::upper($row->status);
                return "<span class='badge bg-{$color} text-{$color}-fg'>{$text}</span>";
            })
            ->addColumn('action', function ($row) {
                $btn = "<a class='btn btn-primary btn-icon' href='" . route('farmasi.stok-opname.show', $row->id) . "'>
                                    <i class='ti ti-search'></i>
                                </a>";

                if ($row->status == 'process') {
                    $btn .= "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah StokOpname`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.farmasi.stok-opname.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
                }
                return $btn;
            })
            ->rawColumns([
                'status',
                'action',
            ])
            ->make(true);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('farmasi.stok-opname.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStokOpnameRequest $request)
    {
        $request->merge(['created_by' => Auth::id()]);

        $stokOpname = StokOpname::create($request->only(['tanggal', 'created_by']));

        return $this->sendResponse(data: $stokOpname, message: __('http-response.success.store', ['Attribute' => 'Stok Opname']));
    }

    public function toggleStatus(StokOpname $stokOpname)
    {
        $stokOpname->status = 'done';
        $stokOpname->save();

        return $this->sendResponse(message: 'Status berhasil diubah');
    }

    /**
     * Display the specified resource.
     */
    public function show(StokOpname $stokOpname)
    {
        $stokOpname->load(['user']);

        return view('farmasi.stok-opname.show', compact('stokOpname'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStokOpnameRequest $request, StokOpname $stokOpname)
    {

        StokOpname::find($stokOpname->id)->update($request->only(['tanggal', 'created_by']));

        return $this->sendResponse(data: $stokOpname, message: __('http-response.success.update', ['Attribute' => 'Stok Opname']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StokOpname $stokOpname)
    {
        $stokOpname->delete();

        return $this->sendResponse(message: __('http-response.success.delete', ['Attribute' => 'Stok Opname']));
    }
}
