<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenjualanDetailRequest extends FormRequest
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
            'qty' => 'required|integer|min:1',
            'jenis' => 'required|in:apotek,bebas'
        ];
    }

    public function attributes()
    {
        return [
            'produk_id' => 'obat',
            'qty' => 'jumlah obat'
        ];
    }
}
