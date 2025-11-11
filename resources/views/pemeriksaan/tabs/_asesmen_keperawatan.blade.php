<div x-data="AsesmenKeperawatan" x-init="init()">
  <form @submit.prevent="handleSubmit" autocomplete="off">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Keadaan Umum</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Berat Badan</label>
              <input type="number" min="0" x-model="form.berat" class="form-control" :class="{ 'is-invalid': errors.berat }">
              <div class="invalid-feedback" x-text="errors.berat"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Tinggi Badan</label>
              <input type="number" min="0" x-model="form.tinggi" class="form-control" :class="{ 'is-invalid': errors.tinggi }">
              <div class="invalid-feedback" x-text="errors.tinggi"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Tekanan Darah</label>
              <input type="text" x-model="form.tekanan_darah" class="form-control" :class="{ 'is-invalid': errors.tekanan_darah }">
              <div class="invalid-feedback" x-text="errors.tekanan_darah"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Nadi</label>
              <input type="number" min="0" x-model="form.nadi" class="form-control" :class="{ 'is-invalid': errors.nadi }">
              <div class="invalid-feedback" x-text="errors.nadi"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Suhu Badan</label>
              <input type="number" min="0" x-model="form.suhu" class="form-control" :class="{ 'is-invalid': errors.suhu }">
              <div class="invalid-feedback" x-text="errors.suhu"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Respirasi</label>
              <input type="number" min="0" x-model="form.respirasi" class="form-control" :class="{ 'is-invalid': errors.respirasi }">
              <div class="invalid-feedback" x-text="errors.respirasi"></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Keluhan Saat Ini</label>
              <input type="text" x-model="form.keluhan" class="form-control" :class="{ 'is-invalid': errors.keluhan }">
              <div class="invalid-feedback" x-text="errors.keluhan"></div>
            </div>
          </div>
          <div class="col-md-4 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Riwayat Rawat Inap</label>
              <div>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-ranap" x-model="form.riwayat_ranap" value="N">
                  <span class="form-check-label">Tidak Pernah</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-ranap" x-model="form.riwayat_ranap" value="Y">
                  <span class="form-check-label">Pernah</span>
                </label>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Riwayat Penyakit Keluarga</label>
              <input type="text" x-model="form.riwayat_penyakit_keluarga" class="form-control">
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Alergi</label>
              <div>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-alergi" x-model="form.alergi" value="N">
                  <span class="form-check-label">Tidak Ada</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-alergi" x-model="form.alergi" value="Y">
                  <span class="form-check-label">Ada</span>
                </label>
              </div>
            </div>
          </div>

          <div class="col-md-8 col-sm-12" x-show="form.alergi == 'Y'">
            <div class="mb-3">
              <label class="form-label">Keterangan Alergi</label>
              <input type="text" x-model="form.alergi_ket" class="form-control">
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Asesmen Resiko Jatuh</h3>
      </div>
      <div class="card-body">
        <div class="mb-3 row">
          <label class="col-10 col-form-label">Apakah pasien tampak tidak seimbang (sempoyongan/limbung) ?</label>
          <div class="col">
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-sempoyongan" x-model="form.sempoyongan" value="N">
              <span class="form-check-label">Tidak</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-sempoyongan" x-model="form.sempoyongan" value="Y">
              <span class="form-check-label">Ya</span>
            </label>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="col-10 col-form-label">Apakah pasien memegang pinggiran kursi atau meja atau benda lain sebagai penopang saat akan duduk ?</label>
          <div class="col">
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-pegangan_kursi" x-model="form.pegangan_kursi" value="N">
              <span class="form-check-label">Tidak</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-pegangan_kursi" x-model="form.pegangan_kursi" value="Y">
              <span class="form-check-label">Ya</span>
            </label>
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Asesmen Nyeri</h3>
      </div>
      <div class="card-body">
        <div class="mb-3 row">
          <label class="col-10 col-form-label">Skrining Nyeri</label>
          <div class="col">
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-skrining_nyeri" x-model="form.skrining_nyeri" value="N">
              <span class="form-check-label">Tidak</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-skrining_nyeri" x-model="form.skrining_nyeri" value="Y">
              <span class="form-check-label">Ya</span>
            </label>
          </div>
        </div>

        <div class="mb-3 row" x-show="form.skrining_nyeri == 'Y'">
          <label class="col-3 col-form-label">Penyebab</label>
          <div class="col">
            <input type="text" x-model="form.penyebab_nyeri" class="form-control">
          </div>
        </div>

        <div class="mb-3 row" x-show="form.skrining_nyeri == 'Y'">
          <label class="col-3 col-form-label">Kualitas</label>
          <div class="col">
            <input type="text" x-model="form.kualitas_nyeri" class="form-control">
          </div>
        </div>

        <div class="mb-3 row" x-show="form.skrining_nyeri == 'Y'">
          <label class="col-3 col-form-label">Lokasi</label>
          <div class="col">
            <input type="text" x-model="form.lokasi_nyeri" class="form-control">
          </div>
        </div>

        <div class="mb-3 row" x-show="form.skrining_nyeri == 'Y'">
          <label class="col-3 col-form-label">Skala</label>
          <div class="col">
            <input type="text" x-model="form.skala_nyeri" class="form-control">
          </div>
        </div>

        <div class="mb-3 row" x-show="form.skrining_nyeri == 'Y'">
          <label class="col-3 col-form-label">Waktu</label>
          <div class="col">
            <input type="text" x-model="form.waktu_nyeri" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Asesmen Nutrisi Gizi</h3>
      </div>
      <div class="card-body">
        <div class="mb-3 row">
          <label class="col-4 col-form-label">Apakah pasien kehilangan berat badan secara tidak sengaja? ?</label>
          <div class="col">
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-kehilangan_bb" x-model="form.kehilangan_bb" value="N">
              <span class="form-check-label">Tidak</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-kehilangan_bb" x-model="form.kehilangan_bb" value="1">
              <span class="form-check-label">Ya (ragu-ragu)</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-kehilangan_bb" x-model="form.kehilangan_bb" value="2">
              <span class="form-check-label">Ya, 1 - 5 kg</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-kehilangan_bb" x-model="form.kehilangan_bb" value="3">
              <span class="form-check-label">Ya, 6 - 10 kg</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-kehilangan_bb" x-model="form.kehilangan_bb" value="4">
              <span class="form-check-label">Ya, 11 - 15 kg</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-kehilangan_bb" x-model="form.kehilangan_bb" value="5">
              <span class="form-check-label">Ya, lebih dari 15 kg</span>
            </label>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="col-10 col-form-label">Apakah asupan makan menurun yg dikarenakan adanya penurunan nafsu makan? ?</label>
          <div class="col">
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-penurunan_nafsu_makan" x-model="form.penurunan_nafsu_makan" value="N">
              <span class="form-check-label">Tidak</span>
            </label>
            <label class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="radio-pegangan_kupenurunan_nafsu_makanrsi" x-model="form.penurunan_nafsu_makan" value="Y">
              <span class="form-check-label">Ya</span>
            </label>
          </div>
        </div>
      </div>
      <div class="card-footer">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="mb-3 row">
              <label class="col-3 col-form-label">Petugas</label>
              <div class="col">
                <input type="text" x-model="petugas" disabled class="form-control">
              </div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12 text-end">
            <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
              <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </div>

  </form>
</div>

@push('pemeriksaan-js')
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('AsesmenKeperawatan', () => ({
        petugas: '',
        form: {
          pasien_id: pasien.id,
          kunjungan_id: kunjungan.id,
          created_by: '',
          berat: '',
          tinggi: '',
          nadi: '',
          suhu: '',
          respirasi: '',
          tekanan_darah: '',
          riwayat_ranap: 'N',
          riwayat_penyakit_keluarga: '',
          alergi: 'N',
          alergi_ket: '',
          keluhan: '',
          sempoyongan: 'N',
          pegangan_kursi: 'N',
          skrining_nyeri: 'N',
          penyebab_nyeri: '',
          lokasi_nyeri: '',
          kualitas_nyeri: '',
          skala_nyeri: '',
          waktu_nyeri: '',
          kehilangan_bb: 'N',
          penurunan_nafsu_makan: 'N',
        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.pemeriksaanstore.asesmen-keperawatan'),
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

        init() {
          this.petugas = asesmenPerawat.petugas.name ?? '{{ Auth::user()->name }}';
          this.form.pasien_id = pasien.id;
          this.form.kunjungan_id = kunjungan.id;
          this.form.created_by = asesmenPerawat.created_by ?? '{{ Auth::id() }}';
          this.form.berat = asesmenPerawat.berat;
          this.form.tinggi = asesmenPerawat.tinggi;
          this.form.nadi = asesmenPerawat.nadi;
          this.form.suhu = asesmenPerawat.suhu;
          this.form.respirasi = asesmenPerawat.respirasi;
          this.form.tekanan_darah = asesmenPerawat.tekanan_darah;
          this.form.riwayat_ranap = asesmenPerawat.riwayat_ranap;
          this.form.riwayat_penyakit_keluarga = asesmenPerawat.riwayat_penyakit_keluarga;
          this.form.alergi = asesmenPerawat.alergi;
          this.form.alergi_ket = asesmenPerawat.alergi_ket;
          this.form.keluhan = asesmenPerawat.keluhan;
          this.form.sempoyongan = asesmenPerawat.sempoyongan;
          this.form.pegangan_kursi = asesmenPerawat.pegangan_kursi;
          this.form.skrining_nyeri = asesmenPerawat.skrining_nyeri;
          this.form.penyebab_nyeri = asesmenPerawat.penyebab_nyeri;
          this.form.lokasi_nyeri = asesmenPerawat.lokasi_nyeri;
          this.form.kualitas_nyeri = asesmenPerawat.kualitas_nyeri;
          this.form.skala_nyeri = asesmenPerawat.skala_nyeri;
          this.form.waktu_nyeri = asesmenPerawat.waktu_nyeri;
          this.form.kehilangan_bb = asesmenPerawat.kehilangan_bb;
          this.form.penurunan_nafsu_makan = asesmenPerawat.penurunan_nafsu_makan;
        }
      }))
    })
  </script>
@endpush
