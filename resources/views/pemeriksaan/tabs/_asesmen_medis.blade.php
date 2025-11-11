<div x-data="AsesmenMedis" x-init="init()">
  <form @submit.prevent="handleSubmit" autocomplete="off">
    <div class="card">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Anamnesis</h3>
      </div>
      <div class="card-body">

        <div class="mb-3 row">
          <label class="col-3 col-form-label">Keluhan Utama</label>
          <div class="col">
            <textarea type="text" x-model="form.keluhan_utama" class="form-control"></textarea>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="col-3 col-form-label">Penyakit Dahulu</label>
          <div class="col">
            <textarea type="text" x-model="form.penyakit_dahulu" class="form-control"></textarea>
          </div>
        </div>
        <div class="mb-3 row">
          <label class="col-3 col-form-label">Penyakit Sekarang</label>
          <div class="col">
            <textarea type="text" x-model="form.penyakit_sekarang" class="form-control"></textarea>
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Pemeriksaan Fisik</h3>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Berat Badan</label>
              <input type="number" min="0" x-model="form.berat" disabled class="form-control" :class="{ 'is-invalid': errors.berat }">
              <div class="invalid-feedback" x-text="errors.berat"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Tinggi Badan</label>
              <input type="number" min="0" x-model="form.tinggi" disabled class="form-control" :class="{ 'is-invalid': errors.tinggi }">
              <div class="invalid-feedback" x-text="errors.tinggi"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Tekanan Darah</label>
              <input type="text" x-model="form.tekanan_darah" disabled class="form-control" :class="{ 'is-invalid': errors.tekanan_darah }">
              <div class="invalid-feedback" x-text="errors.tekanan_darah"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Nadi</label>
              <input type="number" min="0" x-model="form.nadi" disabled class="form-control" :class="{ 'is-invalid': errors.nadi }">
              <div class="invalid-feedback" x-text="errors.nadi"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Suhu Badan</label>
              <input type="number" min="0" x-model="form.suhu" disabled class="form-control" :class="{ 'is-invalid': errors.suhu }">
              <div class="invalid-feedback" x-text="errors.suhu"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label required">Respirasi</label>
              <input type="number" min="0" x-model="form.respirasi" disabled class="form-control" :class="{ 'is-invalid': errors.respirasi }">
              <div class="invalid-feedback" x-text="errors.respirasi"></div>
            </div>
          </div>
        </div>


        <div class="mb-3 row">
          <label class="col-3 col-form-label">Keadaan Umum</label>
          <div class="col">
            <textarea type="text" x-model="form.keadaan_umum" class="form-control"></textarea>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-3 col-form-label">Diagnosis Sementara</label>
          <div class="col">
            <textarea type="text" x-model="form.diagnosis_sementara" class="form-control"></textarea>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-3 col-form-label">Indikasi Medis</label>
          <div class="col">
            <textarea type="text" x-model="form.indikasi_medis" class="form-control"></textarea>
          </div>
        </div>

        <div class="mb-3 row">
          <label class="col-3 col-form-label">Tindak Lanjut</label>
          <div class="col">
            <div class="d-flex flex-column">
              <div>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-tindak_lanjut" x-model="form.tindak_lanjut" value="rawatjalan">
                  <span class="form-check-label">Rawat Jalan</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-tindak_lanjut" x-model="form.tindak_lanjut" value="rawatinap">
                  <span class="form-check-label">Rawat Inap</span>
                </label>
                <label class="form-check form-check-inline">
                  <input class="form-check-input" type="radio" name="radio-tindak_lanjut" x-model="form.tindak_lanjut" value="rujuk">
                  <span class="form-check-label">Rujuk</span>
                </label>
              </div>
              <div x-show="form.tindak_lanjut == 'rujuk'">
                <label class="form-label">Keterangan Rujuk :</label>
                <input type="text" x-model="form.tindak_lanjut_ket" class="form-control" placeholder="Dirujuk ke ...">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Diagnosa (ICD 10)</h3>
      </div>
      <div class="card-body">

      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Procedure (ICD 9)</h3>
      </div>
      <div class="card-body">
      </div>

      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
          <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
          Simpan
        </button>
      </div>
    </div>

  </form>
</div>

@push('pemeriksaan-js')
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('AsesmenMedis', () => ({
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
          keluhan_utama: '',
          penyakit_dahulu: '',
          penyakit_sekarang: '',
          keadaan_umum: '',
          diagnosis_sementara: '',
          indikasi_medis: '',
          tindak_lanjut: 'rawatjalan',
          tindak_lanjut_ket: '',
        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.pemeriksaan.store.asesmen-medis'),
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
          this.form.pasien_id = pasien.id;
          this.form.kunjungan_id = kunjungan.id;
          this.form.created_by = kunjungan.dokter_id;
          this.form.berat = asesmenPerawat.berat;
          this.form.tinggi = asesmenPerawat.tinggi;
          this.form.nadi = asesmenPerawat.nadi;
          this.form.suhu = asesmenPerawat.suhu;
          this.form.respirasi = asesmenPerawat.respirasi;
          this.form.tekanan_darah = asesmenPerawat.tekanan_darah;

          this.form.keluhan_utama = asesmenMedis.keluhan_utama;
          this.form.penyakit_dahulu = asesmenMedis.penyakit_dahulu;
          this.form.penyakit_sekarang = asesmenMedis.penyakit_sekarang;
          this.form.keadaan_umum = asesmenMedis.keadaan_umum;
          this.form.diagnosis_sementara = asesmenMedis.diagnosis_sementara;
          this.form.indikasi_medis = asesmenMedis.indikasi_medis;
          this.form.tindak_lanjut = asesmenMedis.tindak_lanjut;
          this.form.tindak_lanjut_ket = asesmenMedis.tindak_lanjut_ket;
        }
      }))
    })
  </script>
@endpush
