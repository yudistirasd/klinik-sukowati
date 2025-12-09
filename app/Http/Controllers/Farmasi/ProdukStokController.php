<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProdukStokController extends Controller
{
    public function dt()
    {
        $data = DB::table('produk_stok as ps')
            ->join('produk as pr', 'pr.id', '=', 'ps.produk_id')
            ->select([
                DB::raw("pr.name || ' ' || pr.dosis || ' ' || pr.satuan || ' ' || pr.sediaan as name"),
                'expired_date',
                'barcode',
                'harga_beli',
                'harga_jual_resep',
                'harga_jual_bebas',
                'harga_jual_apotek',
                DB::raw('sum(ps.ready) as ready')
            ])
            ->groupBy([
                'pr.name',
                'pr.dosis',
                'pr.satuan',
                'pr.sediaan',
                'harga_beli',
                'harga_jual_resep',
                'harga_jual_bebas',
                'harga_jual_apotek',
                'expired_date',
                'barcode',
            ]);

        return DataTables::of($data)
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('pr.name', 'ilike', $keyword . '%');
            })
            ->editColumn('harga_beli', fn($row) => formatUang($row->harga_beli, true))
            ->editColumn('harga_jual_resep', fn($row) => formatUang($row->harga_jual_resep, true))
            ->editColumn('harga_jual_bebas', fn($row) => formatUang($row->harga_jual_bebas, true))
            ->editColumn('harga_jual_apotek', fn($row) => formatUang($row->harga_jual_apotek, true))
            ->editColumn('expired_date', fn($row) => empty($row->expired_date) ? '-' : $row->expired_date)
            ->addIndexColumn()
            ->make(true);
    }

    public function select2Bebas(Request $request)
    {
        $data = DB::table('produk_stok as ps')
            ->join('produk as pr', 'pr.id', '=', 'ps.produk_id')
            ->select([
                'pr.id',
                'pr.name',
                'pr.dosis',
                'pr.satuan',
                'pr.sediaan',
                DB::raw('coalesce(ps.harga_jual_bebas, 0) as harga_jual_bebas'),
                DB::raw("coalesce(sum(ps.ready), 0) as ready"),
            ])
            ->groupBy(['pr.id', 'pr.name', 'pr.dosis', 'pr.satuan', 'pr.sediaan', 'ps.harga_jual_bebas'])
            ->when($request->filled('keyword'), fn($q) => $q->where('pr.name', 'ilike', "{$request->keyword}%"))
            ->orderBy('pr.name', 'asc')
            ->limit(30)
            ->get()
            ->map(function ($row) {
                $row->harga_jual_bebas_view =  formatUang($row->harga_jual_bebas);

                return $row;
            });

        return $this->sendResponse(data: $data);
    }

    public function select2StokOpname(Request $request)
    {
        $data = DB::table('produk as pro')
            ->join('produk_stok as pso', 'pso.produk_id', '=', 'pro.id')
            ->select(
                'pro.id as produk_id',
                'pro.sediaan',
                'pso.barcode',
                'pso.harga_beli',
                'pso.harga_jual_bebas',
                'pso.harga_jual_resep',
                'pso.harga_jual_apotek',
                DB::raw("coalesce(pso.expired_date::text, 'ED Kosong') as expired_date"),
                DB::raw("pro.name || ' ' || pro.dosis || ' ' || pro.satuan || ' ' || pro.sediaan as obat"),
                DB::raw("sum(pso.ready) as ready")
            )
            ->where('jenis', 'obat')
            ->whereNull('deleted_at')
            ->where('pso.ready', '>', '0')
            ->when($request->filled('keyword'), fn($query) => $query->whereRaw("pro.name || ' ' || pro.dosis || ' ' || pro.satuan || ' ' || pro.sediaan ilike ?", [$request->keyword . '%']))
            ->groupBy(
                'pro.id',
                'pro.name',
                'pro.dosis',
                'pro.satuan',
                'pro.sediaan',
                'pso.barcode',
                'pso.expired_date',
                'pso.harga_beli',
                'pso.harga_jual_bebas',
                'pso.harga_jual_resep',
                'pso.harga_jual_apotek'
            )
            ->get();

        return $this->sendResponse(data: $data);
    }


    public function index()
    {
        return view('farmasi.stok.index');
    }
}
