<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePembelianDetailRequest extends FormRequest
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
            'produk_id' => 'required',
            'jumlah_kemasan' => 'required',
            'satuan_kemasan' => 'required',
            'isi_per_kemasan' => 'required',
            'qty' => 'required',
            'harga_beli_kemasan' => 'required',
            'harga_beli_satuan' => 'required',
            'harga_jual_satuan' => 'required',
            'keuntungan_satuan' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'produk_id' => 'obat',
        ];
    }
}
