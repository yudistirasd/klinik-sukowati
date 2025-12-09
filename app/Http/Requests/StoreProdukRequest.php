<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProdukRequest extends FormRequest
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
        $rules = [
            'tindakan' => [
                'name' => 'required',
                'tarif' => 'required'
            ],
            'obat' => [
                'name' => 'required',
                'dosis' => 'required',
                'satuan' => 'required',
                'sediaan' => 'required'
            ],
            'laborat' => [
                'name' => 'required',
                'tarif' => 'required',
                'nilai_normal_laki_laki' => 'required',
                'nilai_normal_perempuan' => 'required',
            ]
        ];

        return $rules[$this->jenis];
    }
}
