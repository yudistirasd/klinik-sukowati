<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required',
            'dokter_id' => 'required',
            'pasien_id' => 'required',
            'kunjungan_id' => 'required',
            'produk_id' => 'required',
            'unit_dosis' => 'required',
            'frekuensi' => 'required',
            'lama_hari' => 'required',
            'qty' => 'required',
            'takaran_id' => 'required',
            'aturan_pakai_id' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'dokter_id' => 'DPJP',
            'produk_id' => 'obat',
            'qty' => 'jumlah obat',
            'takaran_id' => 'takaran',
            'aturan_pakai_id' => 'aturan pakai'
        ];
    }
}
