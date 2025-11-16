<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Pembelian;
use App\Http\Requests\StorePembelianRequest;
use App\Http\Requests\UpdatePembelianRequest;
use Auth;
use DataTables;

class PembelianController extends Controller
{

    public function dt()
    {
        $data = Pembelian::query()->with(['suplier']);


        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = "<a class='btn btn-primary btn-icon' href='" . route('transaksi.pembelian.show', $row->id) . "'>
                                    <i class='ti ti-search'></i>
                                </a>";

                if ($row->insert_stok == 'belum') {
                    $btn .= "
                                <button class='btn btn-warning btn-icon' onclick='handleModal(`edit`, `Ubah Pembelian`, " . json_encode($row) . ")'>
                                    <i class='ti ti-edit'></i>
                                </button>
                                <button class='btn btn-danger btn-icon' onclick='confirmDelete(`" . route('api.transaksi.pembelian.destroy', $row->id) . "`, table.ajax.reload)'>
                                    <i class='ti ti-trash'></i>
                                </button>
                            ";
                }
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
        return view('transaksi.pembelian.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePembelianRequest $request)
    {
        $request->merge(['created_by' => Auth::id()]);

        $pembelian = Pembelian::create($request->only(['tanggal', 'suplier_id', 'created_by']));

        return $this->sendResponse(data: $pembelian, message: __('http-response.success.store', ['Attribute' => 'Pembelian']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembelian $pembelian)
    {
        $pembelian->load('suplier');

        return view('transaksi.pembelian.show', compact('pembelian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePembelianRequest $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembelian $pembelian)
    {
        //
    }
}
