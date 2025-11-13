<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\AturanPakaiObat;
use App\Models\SatuanDosisObat;
use App\Models\SediaanObat;
use App\Models\TakaranObat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Str;

class FarmasiController extends Controller
{
    public function satuanDosis(Request $request)
    {
        $data = SatuanDosisObat::select([
            'name as value',
            'name as text'
        ])
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
            ->get();

        return $this->sendResponse(data: $data);
    }

    public function storeSatuanDosis(Request $request)
    {
        SatuanDosisObat::updateOrCreate(['name' => Str::upper($request->name)]);

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Satuan dosis obat']));
    }

    public function sediaan(Request $request)
    {
        $data = SediaanObat::select([
            'name as value',
            'name as text'
        ])
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
            ->get();

        return $this->sendResponse(data: $data);
    }

    public function storeSediaan(Request $request)
    {
        DB::transaction(function () use ($request) {
            SediaanObat::updateOrCreate(['name' => Str::upper($request->name)]);
        });

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Sediaan obat']));
    }

    public function takaran(Request $request)
    {
        $data = TakaranObat::select([
            'id',
            'name as text'
        ])
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
            ->get();

        return $this->sendResponse(data: $data);
    }

    public function storeTakaran(Request $request)
    {
        $takaran = DB::transaction(function () use ($request) {
            return TakaranObat::updateOrCreate(['name' => Str::ucfirst($request->name)]);
        });

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Takaran']), data: $takaran);
    }

    public function aturanPakai(Request $request)
    {
        $data = AturanPakaiObat::select([
            'id',
            'name as text'
        ])
            ->when($request->filled('keyword'), fn($q) => $q->where('name', 'ilike', "%{$request->keyword}%"))
            ->limit(30)
            ->get();

        return $this->sendResponse(data: $data);
    }

    public function storeAturanPakai(Request $request)
    {
        $aturanPakai = DB::transaction(function () use ($request) {
            return AturanPakaiObat::updateOrCreate(['name' => Str::ucfirst($request->name)]);
        });

        return $this->sendResponse(message: __('http-response.success.store', ['Attribute' => 'Aturan Pakai']), data: $aturanPakai);
    }
}
