<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\Produk;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        $view = 'dashboard.' . $role;

        if (!view()->exists($view)) {
            $view = 'dashboard.admin';
        }

        return view($view);
    }

    public function scoreCardAdmin()
    {
        $nakes = User::whereIn('role', ['dokter', 'perawat', 'apoteker'])->count();
        $pasien = Pasien::count();
        $obat = Produk::where('jenis', 'obat')->count();
        $tindakan = Produk::where('jenis', 'tindakan')->count();

        return $this->sendResponse(data: [
            'nakes' => $nakes,
            'pasien' => $pasien,
            'obat' => $obat,
            'tindakan' => $tindakan
        ]);
    }
}
