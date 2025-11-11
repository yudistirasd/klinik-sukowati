<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKunjunganRequest extends FormRequest
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
            'tanggal_registrasi' => 'required',
            'jenis_layanan' => 'required',
            'jenis_pembayaran' => 'required',
            'ruangan_id' => 'required',
            'dokter_id' => 'required',
            'icd10_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'dokter_id' => 'dokter',
            'ruangan_id' => 'ruangan',
            'icd10_id' => 'jenis penyakit'
        ];
    }
}
