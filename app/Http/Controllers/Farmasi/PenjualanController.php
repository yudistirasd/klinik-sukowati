<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\Penjualan;
use Auth;
use Illuminate\Http\Request;

class PenjualanController extends Controller
{
    public function index()
    {
        return view('farmasi.penjualan.index');
    }

    public function create(Request $request)
    {

        if (!in_array($request->jenis, ['bebas', 'apotek']) || empty($request->jenis)) {
            abort(403, 'Jenis penjualan tidak ditemukan');
        }

        $currentUser = Auth::user();
        $penjualan = Penjualan::where('created_by', $currentUser->id)
            ->where('tanggal', date('Y-m-d'))
            ->where('status', 'belum')
            ->where('jenis', $request->jenis)
            ->first();

        if (empty($penjualan)) {
            $penjualan = Penjualan::create([
                'tanggal' => date('Y-m-d'),
                'jenis' => $request->jenis,
                'created_by' => $currentUser->id,
            ]);
        }

        return view('farmasi.penjualan.create', compact('penjualan'));
    }
}
