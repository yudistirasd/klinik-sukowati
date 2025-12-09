<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRuanganRequest extends FormRequest
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
            'name' => 'required',
            'layanan' => 'required',
            'departemen_id' => 'required',
            'kelas' => 'required_if:layanan,RI',
            'tarif_inap' => 'required_if:layanan,RI'
        ];
    }

    public function attributes()
    {
        return [
            'departemen_id' => 'departemen'
        ];
    }
}
