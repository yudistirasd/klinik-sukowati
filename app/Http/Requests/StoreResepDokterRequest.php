<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResepDokterRequest extends FormRequest
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
        $ruleSets = config('constant.resep_rules');

        $mandatoryRules = [
            'tanggal' => 'required',
            'dokter_id' => 'required',
            'pasien_id' => 'required',
            'kunjungan_id' => 'required_if:asal_resep,IN',
            'metode_penulisan' => 'required|in:manual,master_obat',
            'resep_detail_manual' => 'required_if:metode_penulisan,manual',
            'embalase' => 'nullable',
            'jasa_resep' => 'nullable',
            'kondisi_pemberian_obat_id' => 'required_if:metode_penulisan,master_obat',
            'waktu_pemberian_obat' => 'required_if:metode_penulisan,master_obat',
            'catatan' => 'nullable'
        ];


        if ($this->metode_penulisan == 'master_obat') {
            $ruleResep = $ruleSets[$this->getRuleGroupName()];

            $rules =  array_merge($mandatoryRules, $ruleResep);
        } else {
            $rules = $mandatoryRules;
        }

        return $rules;
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

    public function messages()
    {
        return [
            'waktu_pemberian_obat.required_if' => 'Waktu pemberian obat wajib diisi jika metode penulisan adalah Pilih Obat.',
        ];
    }

    public function attributes()
    {
        return config('constant.resep_validation.attributes');
    }
}
