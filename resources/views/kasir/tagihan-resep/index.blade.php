@extends('layouts.app')

@section('title', 'Tagihan Pasien')
@section('subtitle', 'Kasir')

@push('css')
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-header">
      <div class="card-actions">
        <button type="button" onclick="refreshTable()" class="btn btn-dark btn-sm">
          <i class="ti ti-refresh me-1"></i> Refresh
        </button>
      </div>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table dataTable table-striped table-sm table-hover" id="pasien-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Tanggal Registrasi</th>
              <th class="text-center">No RM</th>
              <th class="text-center">No Registrasi</th>
              <th class="text-center">Nama Pasien</th>
              <th class="text-center">Alamat</th>
              <th class="text-center">Ruangan / Klinik</th>
              <th class="text-center">Dokter</th>
              <th class="text-center">Jumlah Tagihan</th>
              <th class="text-center">Status</th>
              <th class="text-center">Aksi</th>
            </tr>
            <tr class="filter-row">
              <th></th>
              <th><input type="date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}"></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th></th>
              <th><input type="text" class="form-control form-control-sm" placeholder="Cari"></th>
              <th></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <div x-data="form">
    <div class="modal modal-blur fade" id="modal-bayar" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Bayar Tagihan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmit" autocomplete="off">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Tgl Registrasi</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.tanggal">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">No Registrasi</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.noregistrasi">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">No RM</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.norm">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Nama Pasien</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.nama">
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Alamat Pasien</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.alamat">
                  </div>
                </div>

                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Ruangan</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.ruang">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Dokter</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="form.dokter">
                  </div>
                </div>
              </div>

              <div class="row">
                <table class="table table-bordered table-striped" id="table-tagihan">
                  <thead>
                    <tr>
                      <th class="text-center fw-bolder" style="width: 5%">#</th>
                      <th class="text-center fw-bolder">Uraian</th>
                      <th class="text-center fw-bolder" style="width: 20%">Harga</th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                Bayar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection


@push('js')
  <script src="{{ asset('libs/datatables/dataTables.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.bootstrap5.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.fixedHeader.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.responsive.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/responsive.bootstrap5.js') }}?{{ config('app.version') }}"></script>
  <script>
    const table = new DataTable('#pasien-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      searchDelay: 500,
      ajax: route('api.kasir.tagihan-tindakan.dt'),
      orderCellsTop: true,
      initComplete: function() {
        this.api()
          .columns()
          .every(function() {
            const column = this;
            $('input', $('.filter-row th').eq(column.index()))
              .on('input', function() {
                column.search(this.value).draw();
              });
          });
      },
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          data: 'tanggal_registrasi',
          name: 'tanggal_registrasi',
          sClass: 'text-center'
        },
        {
          data: 'norm',
          name: 'norm',
          sClass: 'text-center'
        },
        {
          data: 'noregistrasi',
          name: 'noregistrasi',
          sClass: 'text-center'
        },
        {
          data: 'nama',
          name: 'nama',
          sClass: 'text-start'
        },
        {
          data: 'alamat_lengkap',
          name: 'alamat_lengkap',
          sClass: 'text-start',
          searchable: true,
          orderable: false
        },
        {
          data: 'ruangan',
          name: 'ruangan',
          sClass: 'text-center'
        },
        {
          data: 'dokter',
          name: 'dokter',
          sClass: 'text-start'
        },
        {
          data: 'jumlah_tagihan',
          name: 'jumlah_tagihan',
          sClass: 'text-end',
          searchable: false,
          orderable: false
        },
        {
          data: 'status_bayar',
          name: 'status_bayar',
          sClass: 'text-center'
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
      Alpine.data('form', () => ({
        form: {
          kunjungan_id: null,
          noregistrasi: '',
          tanggal: '',
          norm: '',
          nama: '',
          alamat: '',
          ruang: '',
          dokter: '',
          tagihan: {
            jumlah_tagihan: '0',
            layanan: '0',
            obat: '0',
          },
          resep_id: '',
        },
        endPoint: '',
        errors: {},
        loading: false,

        modalControl(row) {
          this.resetForm();

          this.form.kunjungan_id = row.id;
          this.form.noregistrasi = row.noregistrasi;
          this.form.tanggal = row.tanggal_registrasi;
          this.form.nama = row.nama;
          this.form.norm = row.norm;
          this.form.alamat = row.alamat_lengkap;
          this.form.ruang = row.ruangan;
          this.form.dokter = row.dokter;
          this.form.resep_id = row.resep_id;

          this.endPoint = route('api.kasir.tagihan-tindakan.bayar', row.id);

          $('#modal-bayar').modal('show');

          this.tagihan(row.id);

        },

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: this.endPoint,
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
            $('#modal-bayar').modal('hide');
            this.resetForm();
            table.ajax.reload();
            Toast.fire({
              icon: 'success',
              title: response.message
            });
          }).fail((error) => {
            if (error.status === 422) {
              this.errors = error.responseJSON.errors;
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan !',
                text: error.responseJSON.message
              });
            }
          })
        },


        tagihan(kunjungan_id) {
          container = $('#table-tagihan tbody');

          $.ajax({
            url: route('api.kasir.tagihan-tindakan.show', {
              kunjungan: kunjungan_id,
            }),
            method: 'GET',
            beforeSend: () => {
              container.html('<tr><td colspan="3" class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>');
            },
          }).done((response) => {
            container.html(response.data);
          })
        },

        resetForm() {
          this.form = {
            kunjungan_id: null,
            noregistrasi: '',
            tanggal: '',
            nama: '',
            norm: '',
            alamat: '',
            ruang: '',
            dokter: '',
            tagihan: {
              jumlah_tagihan: '0',
              layanan: '0',
              obat: '0',
            },
            resep_id: '',
          };
          this.errors = {};
        }
      }))
    })

    const handleModalBayar = (row) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.modalControl(row);
    }

    const refreshTable = () => {
      table.ajax.reload();
    }
  </script>
@endpush
