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
        <div class="card-actions">
        </div>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-12">
            <a href="javascript:;" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#modal-icd10">
              <div class="ti ti-search"></div> Cari Diagnosa
            </a>
          </div>
        </div>

        <div class="table-responsive col-sm-12">
          <table id="diagnosa-pasien-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm" style="width: 100%;">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama ICD</th>
                <th class="text-center">Nama Indonesia</th>
                <th class="text-center">Act</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header bg-primary text-white">
        <h3 class="card-title">Procedure (ICD 9)</h3>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-12">
            <a href="javascript:;" class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#modal-icd9">
              <div class="ti ti-search"></div> Cari Prosedure
            </a>
          </div>
        </div>

        <div class="table-responsive col-sm-12">
          <table id="prosedure-pasien-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm" style="width: 100%;">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama ICD</th>
                <th class="text-center">Nama Indonesia</th>
                <th class="text-center">Act</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
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

<div class="modal modal-blur fade" id="modal-icd10" tabindex="-1" data-bs-keyboard="false" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" x-text="title">Pencarian ICD 10 </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive col-sm-12">
          <table id="diagnosa-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm" style="width: 100%;">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama ICD</th>
                <th class="text-center">Nama Indonesia</th>
                <th class="text-center">Act</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal modal-blur fade" id="modal-icd9" tabindex="-1" data-bs-keyboard="false" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" x-text="title">Pencarian ICD 9 </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive col-sm-12">
          <table id="prosedure-table" class="table table-bordered table-striped table-sm" style="width: 100%;">
            <thead>
              <tr>
                <th class="text-center">No.</th>
                <th class="text-center">Kode</th>
                <th class="text-center">Nama ICD</th>
                <th class="text-center">Nama Indonesia</th>
                <th class="text-center">Act</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@push('pemeriksaan-js')
  <script>
    const tableDiagnosa = new DataTable('#diagnosa-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.icd10.dt'),
      order: [
        [
          1, 'asc'
        ]
      ],
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          "data": "code"
        },
        {
          "data": "display_en"
        },
        {
          "data": "display_id"
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "10%"
        },
      ]
    });

    const tableProsedure = new DataTable('#prosedure-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.icd9.dt'),
      order: [
        [
          1, 'asc'
        ]
      ],
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          "data": "code"
        },
        {
          "data": "display_en"
        },
        {
          "data": "display_id"
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "10%"
        },
      ]
    });

    const diagnosaPasienTable = new DataTable('#diagnosa-pasien-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.pemeriksaan.get.diagnosa-pasien', {
        pasien_id: pasien.id,
        kunjungan_id: kunjungan.id
      }),
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          "data": "code"
        },
        {
          "data": "display_en"
        },
        {
          "data": "display_id"
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "10%"
        },
      ]
    });

    const prosedurePasienTable = new DataTable('#prosedure-pasien-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.pemeriksaan.get.prosedure-pasien', {
        pasien_id: pasien.id,
        kunjungan_id: kunjungan.id
      }),
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          "data": "code"
        },
        {
          "data": "display_en"
        },
        {
          "data": "display_id"
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "10%"
        },
      ]
    });


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

          if (asesmenPerawat) {
            this.form.berat = asesmenPerawat.berat;
            this.form.tinggi = asesmenPerawat.tinggi;
            this.form.nadi = asesmenPerawat.nadi;
            this.form.suhu = asesmenPerawat.suhu;
            this.form.respirasi = asesmenPerawat.respirasi;
            this.form.tekanan_darah = asesmenPerawat.tekanan_darah;
          }

          if (asesmenMedis) {
            this.form.keluhan_utama = asesmenMedis.keluhan_utama;
            this.form.penyakit_dahulu = asesmenMedis.penyakit_dahulu;
            this.form.penyakit_sekarang = asesmenMedis.penyakit_sekarang;
            this.form.keadaan_umum = asesmenMedis.keadaan_umum;
            this.form.diagnosis_sementara = asesmenMedis.diagnosis_sementara;
            this.form.indikasi_medis = asesmenMedis.indikasi_medis;
            this.form.tindak_lanjut = asesmenMedis.tindak_lanjut;
            this.form.tindak_lanjut_ket = asesmenMedis.tindak_lanjut_ket;
          }
        },

        selectIcd10(row) {
          let button = $(`#diagnosa-${row.id}`);
          let originalButtonElement = button.html();

          button.html(`<span class="spinner-border spinner-border-sm"></span>`)
          button.prop('disabled', true);

          $.ajax({
            url: route('api.pemeriksaan.store.diagnosa-pasien'),
            method: 'POST',
            data: {
              pasien_id: pasien.id,
              kunjungan_id: kunjungan.id,
              icd10_id: row.id,
              created_by: kunjungan.dokter_id
            },
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: () => {
              button.prop('disabled', false);
              button.html(originalButtonElement);
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

        selectIcd9(row) {
          let button = $(`#prosedure-${row.id}`);
          let originalButtonElement = button.html();

          button.html(`<span class="spinner-border spinner-border-sm"></span>`)
          button.prop('disabled', true);

          $.ajax({
            url: route('api.pemeriksaan.store.prosedure-pasien'),
            method: 'POST',
            data: {
              pasien_id: pasien.id,
              kunjungan_id: kunjungan.id,
              icd9_id: row.id,
              created_by: kunjungan.dokter_id
            },
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: () => {
              button.prop('disabled', false);
              button.html(originalButtonElement);
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
      }))
    })

    const selectIcd10 = (row) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="AsesmenMedis"]'));
      alpineComponent.selectIcd10(row);
    }

    const selectIcd9 = (row) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="AsesmenMedis"]'));
      alpineComponent.selectIcd9(row);
    }

    $('#modal-icd10').on('hidden.bs.modal', () => {
      diagnosaPasienTable.ajax.reload()
    })

    $('#modal-icd9').on('hidden.bs.modal', () => {
      prosedurePasienTable.ajax.reload()
    })
  </script>
@endpush
