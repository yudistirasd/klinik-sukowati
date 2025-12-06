@extends('layouts.app')

@section('title', 'Resep Pasien')
@section('subtitle', 'Farmasi')

@push('css')
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="javascript:;" onclick="cariPasien(event)" class="btn btn-primary btn-5">
    <div class="ti ti-plus me-1"></div>
    Resep Luar
  </a>
@endsection


@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="badges-list mb-3">
        <span>Keterangan : </span>
        <span class="badge bg-blue-lt text-blue-lt-fg">Resep Internal</span>
        <span class="badge bg-dark-lt text-dark-lt-fg">Resep Luar</span>
      </div>
      <div class="table-responsive">
        <table class="table dataTable table-bordered table-hover" id="pasien-table">
          <thead>
            <tr>
              <th class="text-center text-wrap">#</th>
              <th class="text-center text-wrap">Tgl Resep</th>
              <th class="text-center text-wrap">No Resep</th>
              <th class="text-center text-wrap">Dokter</th>
              <th class="text-center text-wrap">No RM</th>
              <th class="text-center text-wrap">Nama Pasien</th>
              <th class="text-center text-wrap">Alamat Pasien</th>
              <th class="text-center text-wrap">Jenis Kelamin</th>
              <th class="text-center text-wrap">Usia</th>
              <th class="text-center text-wrap">Penulisan Resep</th>
              <th class="text-center text-wrap">Status Resep</th>
              <th class="text-center text-wrap">Biaya</th>
              <th class="text-center text-wrap">Status Pembayaran</th>
              <th class="text-center text-wrap">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <div x-data="form">
    <form @submit.prevent="handleSubmit" autocomplete="off">
      <div class="modal modal-blur fade" id="modal-verifikasi" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-fullscreen" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Resep Pasien</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
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


              <div id="resep-container">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                Verifikasi Resep
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
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
      ajax: route('api.farmasi.resep-pasien.dt'),
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
      order: [
        [1, 'desc']
      ],
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '2%'
        },
        {
          data: 'tanggal',
          name: 'tanggal',
          sClass: 'text-center'
        },
        {
          data: 'nomor',
          name: 'nomor',
          sClass: 'text-center'
        },
        {
          data: 'dokter',
          name: 'dokter',
          sClass: 'text-start',
          searchable: true,
          orderable: false
        },
        {
          data: 'norm',
          name: 'norm',
          sClass: 'text-center'
        },
        {
          data: 'nama',
          name: 'nama',
          sClass: 'text-start'
        },
        {
          data: 'alamat',
          name: 'alamat',
          sClass: 'text-start'
        },
        {
          data: 'jenis_kelamin',
          name: 'jenis_kelamin',
          sClass: 'text-center',
          width: "5%"
        },
        {
          data: 'usia',
          name: 'usia',
          sClass: 'text-center'
        },
        {
          data: 'metode_penulisan',
          name: 'metode_penulisan',
          sClass: 'text-center',
          searchable: false,
          orderable: false
        },
        {
          data: 'status',
          name: 'status',
          sClass: 'text-center'
        },
        {
          data: 'total_akhir',
          name: 'total_akhir',
          sClass: 'text-end',
          width: "10%",
          searchable: false,
          orderable: false
        },
        {
          data: 'status_bayar',
          name: 'status_bayar',
          sClass: 'text-center',
          width: "5%"
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "5%"
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

          this.endPoint = route('api.farmasi.resep-pasien.verifikasi', row.resep_id);

          $('#modal-verifikasi').modal('show');

          this.resepObat(row.resep_id);

        },

        resepObat(resep_id) {
          container = $('#resep-container');

          $.ajax({
            url: route('api.farmasi.resep-pasien.detail', {
              resep: resep_id
            }),
            method: 'GET',
            beforeSend: () => {
              container.html('<div class="text-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</div>');
            },
          }).done((response) => {
            container.html(response.data);
          })
        },

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: this.endPoint,
            method: 'POST',
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: () => {
              this.loading = false;
            }
          }).done((response) => {
            $('#modal-verifikasi').modal('hide');
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
          };
          this.errors = {};
        }
      }))
    })

    const handleModalVerif = (row) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.modalControl(row);
    }

    const handleResepObat = (resep_id) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.resepObat(resep_id);
    }

    const cariPasien = (e) => {
      e.preventDefault();
      Swal.fire({
        title: "Pilih pasien untuk membuat resep luar.",
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Cari pasien",
        cancelButtonText: "Tidak, batalkan",
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading()
      }).then(async (result) => {
        if (!result.value) {
          Swal.fire({
            icon: 'info',
            title: 'Aksi dibatalkan !',
          })
        } else {
          window.location.href = route('registrasi.pasien.index');
        }
      });
    }
  </script>
@endpush
