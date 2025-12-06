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
        $ruleSets = [
            // ----------------------------------------------------------
            // 🟩 NON RACIKAN
            // ----------------------------------------------------------
            'non_racikan' => [
                'jenis_resep'      => 'required|in:non_racikan,racikan',
                'produk_id'        => 'required|uuid',
                'signa'            => 'required|string',
                'frekuensi'        => 'required|numeric|min:1',
                'unit_dosis'       => 'required|numeric|min:0.1',
                'lama_hari'        => 'required|numeric|min:1',
                'aturan_pakai_id'  => 'required|integer',
                'catatan'          => 'nullable|string',
            ],

            // ----------------------------------------------------------
            // 🟦 RACIKAN NON-DTD
            // ----------------------------------------------------------
            'racikan_dtd' => [
                'jenis_resep'      => 'required|in:non_racikan,racikan',
                'tipe_racikan'     => 'required|in:dtd',
                'kemasan_racikan'  => 'required|string',
                'signa'            => 'required|string',
                'frekuensi'        => 'required|numeric|min:1',
                'unit_dosis'       => 'required|numeric|min:0.1',

                // komposisi_racikan array
                'komposisi_racikan'                        => 'required|array|min:1',
                'komposisi_racikan.*.produk_id'            => 'required|uuid',
                'komposisi_racikan.*.dosis_per_satuan'     => 'required|numeric|min:0.1',
                'komposisi_racikan.*.dosis_per_racikan'    => 'required|numeric|min:0.1',

                'aturan_pakai_id'  => 'required|integer',
                'catatan'          => 'nullable|string',
            ],

            // ----------------------------------------------------------
            // 🟥 RACIKAN DTD
            // ----------------------------------------------------------
            'racikan_non_dtd' => [
                'jenis_resep'      => 'required|in:non_racikan,racikan',
                'tipe_racikan'     => 'required|in:non_dtd',
                'kemasan_racikan'  => 'required|string',
                'lama_hari'        => 'required|numeric|min:1',

                // input dokter
                'jumlah_racikan'   => 'required|numeric|min:1',

                // komposisi_racikan array
                'komposisi_racikan'                        => 'required|array|min:1',
                'komposisi_racikan.*.produk_id'            => 'required|uuid',
                'komposisi_racikan.*.dosis_per_satuan'     => 'required|numeric|min:0.1',
                'komposisi_racikan.*.total_dosis_obat'     => 'required|numeric|min:0.1',

                // note: signa tetap wajib meskipun tidak dipakai menghitung jumlah racikan
                'signa'            => 'required|string',
                'aturan_pakai_id'  => 'required|integer',
                'catatan'          => 'nullable|string',
            ],
        ];

        $mandatoryRules = [
            'tanggal' => 'required',
            'dokter_id' => 'required',
            'pasien_id' => 'required',
            'kunjungan_id' => 'required_if:asal_resep,IN',
            'metode_penulisan' => 'required|in:manual,master_obat',
            'resep_detail_manual' => 'required_if:metode_penulisan,manual',
            'embalase' => 'nullable',
            'jasa_resep' => 'nullable',
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
        return [
            'dokter_id' => 'DPJP',
            'produk_id' => 'obat',
            'qty' => 'jumlah obat',
            'takaran_id' => 'takaran',
            'aturan_pakai_id' => 'cara pakai',
            'resep_detail_manual' => 'resep manual',

            'komposisi_racikan'                                => 'komposisi racikan',
            'komposisi_racikan.*.produk_id'                    => 'nama obat',
            'komposisi_racikan.*.dosis_per_satuan'             => 'dosis per satuan obat',
            'komposisi_racikan.*.dosis_per_racikan'            => 'dosis per racikan',
            'komposisi_racikan.*.total_dosis_obat'             => 'total dosis obat',
            'catatan' => 'keterangan'
        ];
    }
}
