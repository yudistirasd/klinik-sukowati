<div x-data="CPPT" x-init="init()">

  <form @submit.prevent="handleSubmit" autocomplete="off" id="cppt">
    <div class="mb-3 row">
      <label class="col-3 col-form-label">Petugas Pelaksana</label>
      <div class="col-3">
        <input type="text" disabled x-model="form.jenis_user" class="form-control text-uppercase"></input>
      </div>
      <div class="col">
        <input type="text" disabled value="{{ Auth::user()->name }}" class="form-control"></input>
      </div>
    </div>
    <div class="mb-3 row">
      <label class="col-3 col-form-label">Subjektif</label>
      <div class="col">
        <textarea type="text" x-model="form.subjective" class="form-control"></textarea>
      </div>
    </div>
    <div class="mb-3 row">
      <label class="col-3 col-form-label">Objektif</label>
      <div class="col">
        <textarea type="text" x-model="form.objective" class="form-control"></textarea>
      </div>
    </div>
    <div class="mb-3 row">
      <label class="col-3 col-form-label">Anamnesa</label>
      <div class="col">
        <textarea type="text" x-model="form.asesmen" class="form-control"></textarea>
      </div>
    </div>
    <div class="mb-3 row">
      <label class="col-3 col-form-label">Plan</label>
      <div class="col">
        <textarea type="text" x-model="form.plan" class="form-control"></textarea>
      </div>
    </div>
    @if (Auth::user()->role == 'dokter')
      <div class="mb-3 row">
        <label class="col-3 col-form-label">Edukasi Pasien</label>
        <div class="col">
          <textarea type="text" x-model="form.edukasi" class="form-control"></textarea>
        </div>
      </div>
    @endif
    <div class="mb-3 text-end">
      <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
        <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
        Simpan
      </button>
    </div>
  </form>

  <table id="cppt-pasien-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm mt-3" style="width: 100%;">
    <thead>
      <tr>
        <th class="text-center">No.</th>
        <th class="text-center">Tanggal</th>
        <th class="text-center">Keterangan</th>
        <th class="text-center">Act</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
@push('pemeriksaan-js')
  <script>
    const cpptPasienTable = new DataTable('#cppt-pasien-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.pemeriksaan.get.cppt', {
        pasien_id: pasien.id
      }),
      order: [
        [
          1, 'desc'
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
          data: "created_at",
          searchable: false,
          sClass: 'text-center',
        },
        {
          data: "keterangan",
          width: '70%'
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
      Alpine.data('CPPT', () => ({
        form: {
          id: '',
          pasien_id: pasien.id,
          kunjungan_id: kunjungan.id,
          created_by: '',
          jenis_user: '',
          subjective: '',
          objective: '',
          asesmen: '',
          plan: '',
          edukasi: '',
        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.pemeriksaan.store.cppt'),
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

            cpptPasienTable.ajax.reload();

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

        editCppt(row) {
          this.form.id = row.id;
          this.form.created_by = row.created_by;
          this.form.jenis_user = row.jenis_user;
          this.form.subjective = row.subjective;
          this.form.objective = row.objective;
          this.form.asesmen = row.asesmen;
          this.form.plan = row.plan;
          this.form.edukasi = row.edukasi;

          document.getElementById('cppt').scrollIntoView({
            behavior: 'smooth',
          })
        },

        init() {
          this.form.pasien_id = pasien.id;
          this.form.kunjungan_id = kunjungan.id;
          this.form.created_by = '{{ Auth::user()->id }}';
          this.form.jenis_user = '{{ Auth::user()->role }}';
        },

        resetForm() {
          this.form = {
            id: '',
            pasien_id: pasien.id,
            kunjungan_id: kunjungan.id,
            created_by: '{{ Auth::user()->id }}',
            jenis_user: '{{ Auth::user()->role }}',
            subjective: '',
            objective: '',
            asesmen: '',
            plan: '',
            edukasi: '',
          };
          this.errors = {};
        }
      }))
    })

    const editCppt = (row) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="CPPT"]'));
      alpineComponent.editCppt(JSON.parse(row));
    }
  </script>
@endpush
