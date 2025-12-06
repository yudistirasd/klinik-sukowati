<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResepApotekerRequest extends FormRequest
{
    /**
     * Apoteker tidak dapat membuat resep manual
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
        $ruleSets = config('constant.resep_validation.rules');

        $mandatoryRules = [
            'embalase' => 'nullable',
            'jasa_resep' => 'nullable',
            'kondisi_pemberian_obat_id' => 'required',
            'waktu_pemberian_obat' => 'required',
            'catatan' => 'nullable'
        ];


        $ruleResep = $ruleSets[$this->getRuleGroupName()];

        return  array_merge($mandatoryRules, $ruleResep);
    }

    private function getRuleGroupName()
    {
        if ($this->jenis_resep === 'non_racikan') {
            return 'non_racikan';
        }

        if ($this->jenis_resep === 'racikan' && $this->tipe_racikan === 'non_dtd') {
            return 'racikan_non_dtd';
        }

        if ($this->jenis_resep === 'racikan' && $this->tipe_racikan === 'dtd') {
            return 'racikan_dtd';
        }
    }

    public function attributes()
    {
        return config('constant.resep_validation.attributes');
    }
}
