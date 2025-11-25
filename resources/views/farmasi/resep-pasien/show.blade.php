@extends('layouts.app')

@section('title', 'Resep ' . $resep->nomor)

@section('subtitle', 'Rincian')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
@endpush

@section('content')
  <div x-data="Resep" x-cloak>
    <div class="card mb-3">
      <div class="card-header">
        <h3 class="card-title">Identitas Pasien</h3>
      </div>
      <div class="card-body">
        <div class="row gap-2">
          <div class="col-auto">
            @php
              $avatar = $pasien->jenis_kelamin == 'L' ? 'avatar_male.jpg' : 'avatar_female.jpg';
            @endphp
            <span class="avatar avatar-xl" style="background-image: url(/img/{{ $avatar }})"> </span>
          </div>
          <div class="col-md-10 col-sm-12">
            <div class="datagrid">
              <div class="datagrid-item">
                <div class="datagrid-title">No RM</div>
                <div class="datagrid-content">{{ $pasien->norm }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Nama</div>
                <div class="datagrid-content">{{ $pasien->nama }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Tempat & Tgl Lahir</div>
                <div class="datagrid-content">{{ $pasien->tempat_lahir }}, {{ $pasien->tanggal_lahir }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Usia</div>
                <div class="datagrid-content">{{ $pasien->usia }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Jenis Kelamin</div>
                <div class="datagrid-content">
                  {{ $pasien->jenis_kelamin_text }}
                </div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Agama</div>
                <div class="datagrid-content">{{ $pasien->agama->name }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Pekerjaan</div>
                <div class="datagrid-content">
                  {{ $pasien->pekerjaan->name }}
                </div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">No. HP</div>
                <div class="datagrid-content">
                  {{ $pasien->nohp }}
                </div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Alamat</div>
                <div class="datagrid-content">
                  {{ $pasien->alamat }}, {{ $pasien->kelurahan->name }}, {{ $pasien->kecamatan->name }}, {{ $pasien->kabupaten->name }}, {{ $pasien->provinsi->name }}
                </div>
              </div>
              @if ($resep->asal_resep == 'IN')
                <div class="datagrid-item">
                  <div class="datagrid-title">No Registrasi</div>
                  <div class="datagrid-content">{{ $kunjungan->tanggal_registrasi }}</div>
                </div>
                <div class="datagrid-item">
                  <div class="datagrid-title">No Registrasi</div>
                  <div class="datagrid-content">{{ $kunjungan->noregistrasi }}</div>
                </div>

                <div class="datagrid-item">
                  <div class="datagrid-title">Ruang/Klinik</div>
                  <div class="datagrid-content">
                    {{ $kunjungan->ruangan->name }}
                  </div>
                </div>
              @endif
              <div class="datagrid-item">
                <div class="datagrid-title">Dokter</div>
                <div class="datagrid-content">
                  {{ $resep->dokter->name }}
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="card mb-3">
      <div class="card-body">
        @if (Auth::user()->hasRole('apoteker'))
          <form @submit.prevent="handleSubmit" autocomplete="off" id="resep">
            <div class="row">
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Tgl Resep</label>
                  <input type="text" class="form-control" autocomplete="off" id="tanggal" x-model="form.tanggal">
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Jenis Resep</label>
                  <select name="jenis_kemasan" x-model="form.jenis_resep" id="" class="form-control" :class="{ 'is-invalid': errors.jenis_resep }">
                    <option value="">-- Pilih --</option>
                    <option value="non_racikan">Non Racikan</option>
                    <option value="racikan">Racikan</option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.jenis_resep"></div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">No Resep</label>
                  <input type="text" disabled class="form-control" autocomplete="off" placeholder="Otomatis dari sistem" x-model="form.nomor" :class="{ 'is-invalid': errors.nomor }">
                  <div class="invalid-feedback" x-text="errors.nomor"></div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">DPJP</label>
                  <input type="text" disabled class="form-control" autocomplete="off" x-model="dokter">
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-3 col-sm-12" x-show="form.jenis_resep == 'racikan'">
                <div class="mb-3">
                  <label class="form-label">Tipe Racikan</label>
                  <select class="form-select" x-model="form.tipe_racikan" x-on:change="resetKomposisi()" :class="{ 'is-invalid': errors.tipe_racikan }">
                    <option value="">-- Pilih Tipe Racikan --</option>
                    <option value="dtd">DTD</option>
                    <option value="non_dtd">Non DTD</option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.tipe_racikan"></div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12" x-show="form.jenis_resep == 'racikan'">
                <div class="mb-3">
                  <label class="form-label">Kemasan Racikan</label>
                  <select class="form-select" x-model="form.kemasan_racikan" :class="{ 'is-invalid': errors.kemasan_racikan }">
                    <option value="">-- Pilih Racikan --</option>
                    <option value="puyer">Puyer (Serbuk)</option>
                    <option value="kapsul">Kapsul</option>
                    <option value="tube">Tube (Salep)</option>
                    <option value="pot">Pot (Krim)</option>
                    <option value="botol">Botol (Sirup)</option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.kemasan_racikan"></div>
                </div>
              </div>

              <div class="col-md-12 col-sm-12" x-show="form.jenis_resep == 'non_racikan'">
                <div class="mb-3">
                  <label class="form-label">Obat</label>
                  <select class="form-control" id="obat" name="obat_id" :class="{ 'is-invalid': errors.produk_id }" style="width: 100%">
                    <option value=""></option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.produk_id"></div>
                </div>
              </div>
              <div class="col-md-2 col-sm-12" x-show="form.jenis_resep">
                <div class="form-label">Waktu Pemberian</div>
                <div class="row row-cols-2">
                  @foreach (waktuPemberianObat() as $item)
                    <div class="col">
                      <label class="form-check fs-5">
                        <input class="form-check-input" x-on:change="hitungSigna" x-model="form.waktu_pemberian_obat" :class="{ 'is-invalid': errors.waktu_pemberian_obat }" type="checkbox" value="{{ $item }}">
                        <span class="form-check-label fw-bolder">{{ $item }}</span>
                      </label>
                    </div>
                  @endforeach
                  <div class="invalid-feedback d-block" x-text="errors.waktu_pemberian_obat"></div>
                </div>
              </div>
              <div class="col-md-1 col-sm-12" x-show="form.jenis_resep">
                <div class="mb-3">
                  <label class="form-label">Signa</label>
                  <input type="text" id="frekuensi" class="form-control p-2 text-center" x-on:input="hitungJumlahObat" autocomplete="off" :class="{ 'is-invalid': errors.signa }">
                  <div class="invalid-feedback" x-text="errors.signa"></div>
                </div>
              </div>
              <div class="col-md-1 col-sm-12" x-show="form.jenis_resep && form.tipe_racikan != 'dtd'">
                <div class="mb-3">
                  <label class="form-label">Lama Hari</label>
                  <input type="number" min="1" class="form-control" x-on:input="hitungJumlahObat" autocomplete="off" x-model="form.lama_hari" :class="{ 'is-invalid': errors.lama_hari }">
                  <div class="invalid-feedback" x-text="errors.lama_hari"></div>
                </div>
              </div>
              <div class="col-md-2 col-sm-12" x-show="form.jenis_resep == 'racikan'">
                <label class="form-label">Jumlah Racikan</label>
                <div class="input-group mb-2">
                  <input type="number" class="form-control" autocomplete="off" x-model="form.jumlah_racikan" :class="{ 'is-invalid': errors.jumlah_racikan }" :disabled="form.tipe_racikan == 'non_dtd'">
                  <span class="input-group-text" x-text="getJumlahRacikan()"></span>
                </div>
                <div class="invalid-feedback d-block" x-text="errors.jumlah_racikan"></div>
              </div>
              <div class="col-md-2 col-sm-12" x-show="form.jenis_resep == 'non_racikan'">
                <div class="mb-3">
                  <label class="form-label">Jumlah Obat</label>
                  <div class="input-group mb-2">
                    <input type="number" disabled class="form-control" autocomplete="off" x-model="form.qty" :class="{ 'is-invalid': errors.qty }">
                    <span class="input-group-text" x-text="sediaan"></span>
                  </div>
                  <div class="invalid-feedback" x-text="errors.qty"></div>
                </div>
              </div>

              <div class="col-md-3 col-sm-12" x-show="form.jenis_resep">
                <div class="mb-3">
                  <label class="form-label">Cara Pakai</label>
                  <select class="form-control" id="aturan_pakai_id" :class="{ 'is-invalid': errors.aturan_pakai_id }" style="width: 100%">
                    <option value=""></option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.aturan_pakai_id"></div>
                </div>
              </div>

              <div class="col-md-3 col-sm-12" x-show="form.jenis_resep">
                <div class="mb-3">
                  <label class="form-label">Kondisi Pemberian Obat</label>
                  <select class="form-control" id="kondisi_pemberian_obat_id" :class="{ 'is-invalid': errors.kondisi_pemberian_obat_id }" style="width: 100%">
                    <option value=""></option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.kondisi_pemberian_obat_id"></div>
                </div>
              </div>

              <div class="col-md-3 col-sm-12" x-show="form.jenis_resep">
                <div class="mb-3">
                  <label class="form-label">Keterangan : </label>
                  <textarea class="form-control" data-bs-toggle="autosize" x-model="form.catatan" rows='1' placeholder="Type something…"></textarea>
                </div>
              </div>

              <div class="col-md-3 col-sm-12" x-show="form.jenis_resep != ''">
                <div class="d-flex flex-row gap-3">
                  <div class="mb-3">
                    <label class="form-label">Embalase</label>
                    <input type="number" min="0" class="form-control" x-on:input="hitungJumlahObat" autocomplete="off" x-model="form.embalase" :class="{ 'is-invalid': errors.embalase }">
                    <div class="invalid-feedback" x-text="errors.embalase"></div>
                  </div>

                  <div class="mb-3">
                    <label class="form-label">Jasa Resep</label>
                    <input type="number" min="0" class="form-control" x-on:input="hitungJumlahObat" autocomplete="off" x-model="form.jasa_resep" :class="{ 'is-invalid': errors.jasa_resep }">
                    <div class="invalid-feedback" x-text="errors.jasa_resep"></div>
                  </div>
                </div>
              </div>

            </div>


            <div class="row" x-show="form.jenis_resep === 'racikan' && form.tipe_racikan">
              <div class="col-md-12 col-sm-12">
                <label class="form-label fw-bold">Komposisi Obat Racikan <span x-text="getTipeRacikan()"></span> </label>
                <div class="table-responsive komposisi-table">
                  <table class="table table-bordered mb-0">
                    <thead class="table-light">
                      <tr>
                        <th class="text-center" style="width: 5%">No</th>
                        <th class="text-center" style="width: 35%">Nama Obat</th>
                        <th class="text-center" style="width: 15%">Dosis per Satuan</th>
                        <th class="text-center" style="width: 15%" x-show="form.tipe_racikan === 'non_dtd'">Total Dosis Diberikan</th>
                        <th class="text-center" style="width: 15%" x-show="form.tipe_racikan === 'dtd'">Dosis Dibutuhkan</th>
                        <th class="text-center" style="width: 20%">Qty</th>
                        <th class="text-center" style="width: 5%"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <template x-for="(komposisi, index) in form.komposisi_racikan" :key="index">
                        <tr>
                          <td class="text-center" x-text="index + 1"></td>
                          <td>
                            <select class="form-select form-select-sm" :class="{ 'is-invalid': errors[`komposisi_racikan.${index}.produk_id`] }" x-model="komposisi.produk_id" x-init="initSelect2($el, index)" :id="'komposisi-racikan-' + index" style="width: 100%">
                              <option value=""></option>
                            </select>
                            <div class="invalid-feedback" x-text="errors[`komposisi_racikan.${index}.produk_id`]"></div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <input type="number" step="any" class="form-control" x-model="komposisi.dosis_per_satuan" x-on:input="hitungQtyKomposisi(index)" :class="{ 'is-invalid': errors[`komposisi_racikan.${index}.dosis_per_satuan`] }">
                              <span class="input-group-text" x-text="komposisi.satuan_dosis_obat"></span>
                            </div>
                            <div class="invalid-feedback d-block" x-text="errors[`komposisi_racikan.${index}.dosis_per_satuan`]"></div>
                          </td>
                          <td x-show="form.tipe_racikan === 'non_dtd'">
                            <div class="input-group input-group-sm">
                              <input type="number" step="any" class="form-control" x-model="komposisi.total_dosis_obat" x-on:input="hitungQtyKomposisi(index)" :class="{ 'is-invalid': errors[`komposisi_racikan.${index}.total_dosis_obat`] }">
                              <span class="input-group-text" x-text="komposisi.satuan_dosis_obat"></span>
                            </div>
                            <div class="invalid-feedback d-block" x-text="errors[`komposisi_racikan.${index}.total_dosis_obat`]"></div>
                          </td>
                          <td x-show="form.tipe_racikan === 'dtd'">
                            <div class="input-group input-group-sm">
                              <input type="number" step="any" class="form-control" x-model="komposisi.dosis_per_racikan" x-on:input="hitungQtyKomposisi(index)" :class="{ 'is-invalid': errors[`komposisi_racikan.${index}.dosis_per_racikan`] }">
                              <span class="input-group-text" x-text="komposisi.satuan_dosis_obat"></span>
                            </div>
                            <div class="invalid-feedback d-block" x-text="errors[`komposisi_racikan.${index}.dosis_per_racikan`]"></div>
                          </td>
                          <td>
                            <div class="input-group input-group-sm">
                              <input type="number" step="any" class="form-control" x-model="komposisi.qty" :class="{ 'is-invalid': errors[`komposisi_racikan.${index}.qty`] }">
                              <span class="input-group-text" x-text="komposisi.sediaan_obat"></span>
                            </div>
                            <div class="invalid-feedback d-block" x-text="errors[`komposisi_racikan.${index}.qty`]"></div>
                          </td>
                          <td class="text-center">
                            <button type="button" class="btn btn-sm btn-danger" x-on:click="hapusKomposisi(index)">X</button>
                          </td>
                        </tr>
                      </template>
                    </tbody>
                  </table>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary btn-add-bahan mt-2" x-on:click="tambahKomposisi()">
                  + Tambah Komposisi Obat
                </button>
              </div>
            </div>
            <div class="mb-3 text-end">
              <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                Simpan
              </button>
            </div>
          </form>
        @endif
        <div id="resep-container">

        </div>
      </div>
    </div>


    <div class="modal modal-blur fade" id="modal-jasa-resep" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Ubah Jasa Resep & Embalase</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmitJasaResep" autocomplete="off">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">Embalase</label>
                <input type="text" class="form-control" autocomplete="off" x-model="formJasa.embalase" :class="{ 'is-invalid': errors.embalase }">
                <div class="invalid-feedback" x-text="errors.embalase"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Jasa Resep</label>
                <input type="text" class="form-control" autocomplete="off" x-model="formJasa.jasa_resep" :class="{ 'is-invalid': errors.jasa_resep }">
                <div class="invalid-feedback" x-text="errors.jasa_resep"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                Simpan
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
@push('js')
  <script src="{{ asset('libs/select2/select2.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/select2/select2-searchInputPlaceholder.js') }}?{{ config('app.version') }}"></script>
  <script>
    let kunjungan = @json($kunjungan);
    let pasien = @json($pasien);
    let resep = @json($resep);
    const resepObat = function() {
      container = $('#resep-container');
      $.ajax({
        url: route('api.farmasi.resep-pasien.obat', {
          resep: resep.id,
        }),
        method: 'GET',
      }).done((response) => {
        container.html(response.data);
      })
    };

    document.addEventListener('alpine:init', () => {
      Alpine.data('Resep', () => ({
        datePicker: {},
        dokter: '',
        mask: {},
        form: {
          id: '',
          nomor: '',
          tanggal: resep.tanggal,
          asal_resep: resep.asal_resep,
          pasien_id: pasien.id,
          kunjungan_id: null,
          dokter_id: resep.dokter_id,
          produk_id: '',
          signa: '',
          unit_dosis: '',
          frekuensi: '',
          lama_hari: '',
          qty: 0,
          takaran_id: '',
          aturan_pakai_id: '',
          catatan: null,
          kondisi_pemberian_obat_id: null,
          waktu_pemberian_obat: [],
          jenis_resep: '',
          tipe_racikan: '',
          kemasan_racikan: '',
          jumlah_racikan: 0,
          embalase: 0,
          jasa_resep: 0,
          komposisi_racikan: []
        },
        formJasa: {
          jasa_resep: 0,
          embalase: 0,
        },
        sediaan: '',
        satuan: '',
        dosis: '',
        endPoint: '',
        errors: {},
        loading: false,

        // untuk hitung jumlah obat non racikan & racikan non dtd
        hitungJumlahObat() {
          let signa = this.mask.value;

          const [freq, dose] = signa.split('X');

          let jumlahObat = 0;
          let jumlahRacikan = 0;

          if (this.form.jenis_resep == 'non_racikan' && this.form.lama_hari) {
            jumlahObat = freq * dose * this.form.lama_hari;
            this.form.qty = jumlahObat;
          }

          if (this.form.jenis_resep == 'racikan' && this.form.tipe_racikan == 'non_dtd' && this.form.lama_hari) {
            jumlahRacikan = freq * dose * this.form.lama_hari;
            this.form.jumlah_racikan = jumlahRacikan;
          }

          this.form.signa = signa;
          this.form.frekuensi = freq;
          this.form.unit_dosis = dose;
        },

        tambahKomposisi() {
          this.form.komposisi_racikan.push({
            produk_id: '',
            dosis_per_satuan: '',
            dosis_per_racikan: '',
            total_dosis_obat: '',
            satuan_dosis_obat: '-',
            qty: '',
            sediaan_obat: '-',
          });
        },

        hapusKomposisi(index) {
          let $select = $(`#komposisi-racikan-${index}`);
          if ($select.data('select2')) {
            $select.select2('destroy'); // Clean up semua resources
          }
          this.form.komposisi_racikan.splice(index, 1);
        },

        hitungQtyKomposisi(index) {
          if (!this.validasiHitungQtyKomposisi(index)) {
            return;
          }
          const komposisi = this.form.komposisi_racikan[index];
          const dosisSatuan = parseFloat(komposisi.dosis_per_satuan) || 0;
          const dosisPeracikan = parseFloat(komposisi.dosis_per_racikan) || 0;
          const dosisTotalObat = parseFloat(komposisi.total_dosis_obat) || 0;
          const jumlahRacikan = parseFloat(this.form.jumlah_racikan) || 0;

          let qty = 0;
          if (this.form.tipe_racikan === 'non_dtd' && dosisSatuan > 0 && dosisTotalObat > 0 && jumlahRacikan > 0) {
            // Qty = dosisTotalObat / Dosis satuan
            qty = (dosisTotalObat / dosisSatuan).toFixed(2);
          } else if (this.form.tipe_racikan === 'dtd' && dosisSatuan > 0 && dosisPeracikan > 0 && jumlahRacikan > 0) {
            // Qty = (Dosis Dibutuhkan / Dosis Satuan) × Jumlah Racikan
            qty = ((dosisPeracikan * jumlahRacikan) / dosisSatuan).toFixed(2);
          }
          komposisi.qty = Math.ceil(qty);

          console.log("komposisi : ", komposisi);
        },

        validasiHitungQtyKomposisi(index) {
          const komposisi = this.form.komposisi_racikan[index];

          if (!komposisi.produk_id) {
            Toast.fire({
              icon: 'warning',
              title: 'Pilih obat terlebih dahulu'
            });
            return false;
          }

          if (!this.form.jumlah_racikan) {
            Toast.fire({
              icon: 'warning',
              title: 'Jumlah racikan harus diisi terlebih dahulu'
            });
            return false;
          }


          return true;
        },

        resetQtyKomposisi(index) {
          let komposisi = this.form.komposisi_racikan[index];

          komposisi.total_dosis_obat = '';
          komposisi.dosis_per_racikan = '';
          komposisi.dosis_per_satuan = '';
          komposisi.qty = '';
        },

        resetKomposisi() {
          this.form.komposisi_racikan.forEach((bahan, index) => {
            this.hapusKomposisi(index);
          });

          setTimeout(() => {
            this.tambahKomposisi();
          }, 100);
        },

        hitungSigna() {
          let waktuPemberian = this.form.waktu_pemberian_obat.length
          this.form.signa = `${waktuPemberian} X 1.0`;
          this.form.frekuensi = waktuPemberian;
          this.form.unit_dosis = 1;
          this.mask.value = this.form.signa;
        },


        handleSubmit() {
          if (!this.form.jenis_resep) {
            return Toast.fire({
              icon: 'error',
              title: 'Jenis resep tidak boleh kosong'
            });
          }

          if (this.form.jenis_resep == 'racikan' && !this.form.tipe_racikan) {
            return Toast.fire({
              icon: 'error',
              title: 'Tipe racikan tidak boleh kosong'
            });
          }

          this.loading = true;
          this.errors = {};


          $.ajax({
            url: route('api.pemeriksaan.store.resep'),
            method: 'POST',
            data: this.form,
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: () => {
              this.loading = false;
            }
          }).done((response) => {
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            this.resetForm();

            this.form.id = response.data.id;
            this.form.nomor = response.data.nomor;
            this.datePicker.dates.setValue(new tempusDominus.DateTime(response.data.tanggal));

            resepObat();

          }).fail((error) => {
            if (error.status === 422) {
              this.errors = error.responseJSON.errors;

              Toast.fire({
                icon: 'error',
                title: error.responseJSON.message
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan !',
                text: error.responseJSON.message
              });
            }
          })
        },

        resetForm() {
          this.form = {
            id: '',
            nomor: '',
            tanggal: '',
            asal_resep: resep.asal_resep,
            pasien_id: pasien.id,
            kunjungan_id: kunjungan ? kunjungan.id : null,
            dokter_id: resep.dokter_id,
            produk_id: '',
            signa: '',
            unit_dosis: '',
            frekuensi: '',
            lama_hari: '',
            catatan: null,
            qty: 0,
            takaran_id: '',
            aturan_pakai_id: '',
            kondisi_pemberian_obat_id: null,
            waktu_pemberian_obat: [],
            jenis_resep: null,
            tipe_racikan: null,
            kemasan_racikan: '',
            jumlah_racikan: 0,
            komposisi_racikan: []
          };
          this.errors = {};

          this.mask.value = '';
          $('#aturan_pakai_id').val(null).trigger('change');
          $('#takaran_id').val(null).trigger('change');
          $('#obat').val(null).trigger('change');
        },

        init() {
          this.tambahKomposisi();
          resepObat();
          this.dokter = resep.dokter.name;

          let selectProduk = $('#obat').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Obat",
            searchInputPlaceholder: 'Cari Obat',
            allowClear: true,
            ajax: {
              url: route('api.master.produk.json', {
                jenis: 'obat'
              }),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }

                // Query parameters will be ?search=[term]&type=public
                return query;
              },
              processResults: function(response) {
                return {
                  results: response.data.map(item => ({
                    id: item.id,
                    text: `${item.name} ${item.dosis} ${item.satuan} ${item.sediaan}`,
                    dosis: item.dosis,
                    satuan: item.satuan,
                    sediaan: item.sediaan
                  }))
                }
              },
            },
            templateSelection: function(data, container) {
              let json = JSON.stringify({
                dosis: data.dosis,
                satuan: data.satuan,
                sediaan: data.sediaan
              });
              $(data.element).attr('data-json', json);
              return data.text;
            }
          }).on('change', (e) => {
            let target = e.target;
            let value = e.target.value;
            let item = $('#obat').find(':selected').data('json');

            this.sediaan = item?.sediaan;
            this.satuan = item?.satuan;
            this.dosis = item?.dosis;
            this.form.produk_id = value;
          })

          let searchResultsAturanPakai = [];
          let selectAturanPakai = $('#aturan_pakai_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Cara Pakai",
            searchInputPlaceholder: 'Cari Cara Pakai',
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.farmasi.aturan-pakai.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultsAturanPakai = response.data;
                return {
                  results: response.data
                }
              }
            },
            createTag: function(params) {
              var term = $.trim(params.term);

              if (term === '') {
                return null;
              }

              if (searchResultsAturanPakai.length > 0) {
                return null;
              }

              return {
                id: term,
                text: term,
                newTag: true // add additional parameters
              }
            },
          }).on('change', (e) => {
            let value = e.target.value;
            this.form.aturan_pakai_id = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.aturan-pakai.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                console.log('Response new tag', response);

                // ✅ Update option dengan ID dari backend
                const newId = response.data.id; // misal backend return {data: {id: 123, name: "mg"}}

                // Update option yang baru dibuat dengan ID asli
                const $option = $('#aturan_pakai_id option[value="' + e.params.data.text + '"]');
                $option.val(newId); // Ganti value dari text ke ID

                // Update form value juga
                this.form.aturan_pakai_id = newId;

                // Trigger change agar select2 update
                $('#aturan_pakai_id').val(newId).trigger('change');
              })
            }

            $('#kondisi_pemberian_obat_id').select2('open');

          })

          let searchResultsKondisiPemberianObat = [];
          let selectKondisiPemberianObat = $('#kondisi_pemberian_obat_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kondisi Pemberian Obat",
            searchInputPlaceholder: 'Cari Kondisi Pemberian Obat',
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.farmasi.kondisi-pemberian-obat.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultsKondisiPemberianObat = response.data;
                return {
                  results: response.data
                }
              }
            },
            createTag: function(params) {
              var term = $.trim(params.term);

              if (term === '') {
                return null;
              }

              if (searchResultsKondisiPemberianObat.length > 0) {
                return null;
              }

              return {
                id: term,
                text: term,
                newTag: true // add additional parameters
              }
            },
          }).on('change', (e) => {
            let value = e.target.value;
            this.form.kondisi_pemberian_obat_id = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.kondisi-pemberian-obat.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                // ✅ Update option dengan ID dari backend
                const newId = response.data.id; // misal backend return {data: {id: 123, name: "mg"}}

                // Update option yang baru dibuat dengan ID asli
                const $option = $('#kondisi_pemberian_obat_id option[value="' + e.params.data.text + '"]');
                $option.val(newId); // Ganti value dari text ke ID

                // Update form value juga
                this.form.kondisi_pemberian_obat_id = newId;

                // Trigger change agar select2 update
                $('#kondisi_pemberian_obat_id').val(newId).trigger('change');
              })
            }
          })

          const input = document.getElementById('frekuensi');
          this.mask = IMask(input, {
            mask: 'num1 X num2',
            lazy: false,
            placeholderChar: '_',
            blocks: {
              num1: {
                mask: Number,
                scale: 0,
                min: 0,
                max: 9
              },
              num2: {
                mask: Number,
                scale: 1,
                radix: '.',
                mapToRadix: ['.'],
                min: 0,
                max: 9.9,
                normalizeZeros: true,
                padFractionalZeros: true
              }
            }
          });

          let tanggal_resep = document.getElementById('tanggal');
          this.datePicker = new tempusDominus.TempusDominus(document.getElementById('tanggal'), {
            display: {
              icons: {
                type: 'icons',
                time: 'ti ti-clock',
                date: 'ti ti-calendar',
                up: 'ti ti-arrow-up',
                down: 'ti ti-arrow-down',
                previous: 'ti ti-chevron-left',
                next: 'ti ti-chevron-right',
                today: 'ti ti-calendar-check',
                clear: 'ti ti-trash',
                close: 'ti ti-xmark'
              },
              components: {
                calendar: true,
                date: true,
                month: true,
                year: true,
                decades: true,
                clock: false,
                hours: false,
                minutes: false,
                seconds: false,
                useTwentyfourHour: undefined
              },
              viewMode: 'calendar',
              toolbarPlacement: 'bottom',
              theme: 'light',
            },
            localization: {
              format: 'yyyy-MM-dd',
            },
            restrictions: {
              maxDate: new Date()
            }
          });

          tanggal_resep.addEventListener('change.td', (e) => {
            let selected = e.detail.date.format('yyyy-MM-dd')

            this.form.tanggal = e.detail.date ?
              e.detail.date.format('yyyy-MM-dd') :
              '';
          });

          if (resep) {
            this.form.tanggal = resep.tanggal;
            this.form.nomor = resep.nomor;
          } else {
            this.form.nomor = '';
            this.form.tanggal = kunjungan.tanggal_registrasi;
          }
          this.datePicker.dates.setValue(new tempusDominus.DateTime(this.form.tanggal));
        },

        initSelect2(element, index) {
          this.$nextTick(() => {
            $(element).select2({
              theme: 'bootstrap-5',
              placeholder: "Pilih Obat",
              searchInputPlaceholder: 'Cari Obat',
              allowClear: true,
              ajax: {
                url: route('api.master.produk.json', {
                  jenis: 'obat'
                }),
                data: function(params) {
                  var query = {
                    keyword: params.term,
                  }

                  // Query parameters will be ?search=[term]&type=public
                  return query;
                },
                processResults: function(response) {
                  return {
                    results: response.data.map(item => ({
                      id: item.id,
                      text: `${item.name} ${item.dosis} ${item.satuan} ${item.sediaan}`,
                      dosis: item.dosis,
                      satuan: item.satuan,
                      sediaan: item.sediaan
                    }))
                  }
                },
              },
              templateSelection: function(data, container) {
                let json = JSON.stringify({
                  dosis: data.dosis,
                  satuan: data.satuan,
                  sediaan: data.sediaan
                });
                $(data.element).attr('data-json', json);
                return data.text;
              }
            }).on('change', (e) => {
              let value = e.target.value;
              let $selected = $(element).find(':selected');
              let item = $selected.data('json');

              if (item) {
                // Update semua field yang diperlukan
                this.form.komposisi_racikan[index].produk_id = value;
                this.form.komposisi_racikan[index].sediaan_obat = item.sediaan ?? '-';
                this.form.komposisi_racikan[index].satuan_dosis_obat = item.satuan ?? '-';
                this.form.komposisi_racikan[index].dosis_per_satuan = parseFloat(item.dosis ?? 0);
              }
            });
          })
        },

        getJumlahRacikan() {
          const takaran = {
            'puyer': 'bungkus',
            'kapsul': 'kapsul',
            'tube': 'tube',
            'pot': 'pot',
            'botol': 'botol'
          };
          return takaran[this.form.kemasan_racikan] || '-';
        },

        getTipeRacikan() {
          const tipe = {
            'dtd': 'DTD',
            'non_dtd': 'Non DTD'
          };
          return tipe[this.form.tipe_racikan] || '-';
        },


        modalControl(data) {
          this.endPoint = route('api.farmasi.resep-pasien.jasa-resep', {
            resep: data.resep_id,
            receipt_number: data.receipt_number
          });

          this.formJasa.embalase = data.embalase;
          this.formJasa.jasa_resep = data.jasa_resep;

          $('#modal-jasa-resep').modal('show');
        },

        handleSubmitJasaResep() {

          this.loading = true;
          this.errors = {};


          $.ajax({
            url: this.endPoint,
            method: 'POST',
            data: this.formJasa,
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: () => {
              this.loading = false;
            }
          }).done((response) => {
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            this.formJasa.jasa_resep = 0;
            this.formJasa.embalase = 0;

            $('#modal-jasa-resep').modal('hide');


            resepObat();

          }).fail((error) => {
            if (error.status === 422) {
              this.errors = error.responseJSON.errors;

              Toast.fire({
                icon: 'error',
                title: error.responseJSON.message
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan !',
                text: error.responseJSON.message
              });
            }
          })
        },
      }))
    })

    const handleVerifikasiResep = (e) => {
      e.preventDefault();
      Swal.fire({
        title: `Apakah anda yakin akan <br> verifikasi Resep ${resep.nomor}?`,
        html: "Resep yang sudah diverifikasi tidak dapat dibatalkan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya!",
        cancelButtonText: "Tidak, batalkan",
        showLoaderOnConfirm: true,
        preConfirm: async (login) => {
          return $.ajax({
            url: route('api.farmasi.resep-pasien.verifikasi', {
              resep: resep.id
            }),
            method: 'POST',
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          }).done((response) => {
            resepObat();
            Toast.fire({
              icon: 'success',
              title: response.message
            });
          }).fail((error) => {
            let response = error.responseJSON;

            Swal.fire({
              icon: 'error',
              title: 'Terjadi kesalahan !',
              text: response.message
            })
          })
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then(async (result) => {
        if (!result.value) {
          Swal.fire({
            icon: 'info',
            title: 'Aksi dibatalkan !',
          })
        }
      });
    }

    const handleModalJasaResep = (data) => {
      console.log(data.detail_resep_id);
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="Resep"]'));
      alpineComponent.modalControl(data);
    }
  </script>
@endpush
