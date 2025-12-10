@extends('layouts.app')

@section('title', 'Tindakan')
@section('subtitle', 'Master Data')

@push('css')
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="#" class="btn btn-primary btn-5" onclick="handleModal('create', 'Tambah Tindakan')">
    <div class="ti ti-plus me-1"></div>
    Tindakan
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="tindakan-table">
          <thead>
            <tr>
              <td class="text-center" rowspan="2">#</td>
              <td class="text-center" rowspan="2">Nama</td>
              <td class="text-center" colspan="2">Tarif</td>
              <td class="text-center" rowspan="2">Aksi</td>
            </tr>
            <tr>
              <th class="text-center">RJ</th>
              <th class="text-center">RI</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div x-data="form">
    <div class="modal modal-blur fade" id="modal-tindakan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" x-text="title">Modal Title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmit" autocomplete="off">
            <input type="hidden" x-model="form.tarif">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">Nama Tindakan</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.name" :class="{ 'is-invalid': errors.name }">
                <div class="invalid-feedback" x-text="errors.name"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Tarif Tindakan Rawat Jalan</label>
                <input type="text" x-model="tarif_view" x-bind:input="formatUangTindakan()" class="form-control" autocomplete="off" :class="{ 'is-invalid': errors.tarif }" placeholder="0">
                <div class="invalid-feedback" x-text="errors.tarif"></div>
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
  <script src="{{ asset('libs/datatables/dataTables.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.bootstrap5.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.fixedHeader.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.responsive.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/responsive.bootstrap5.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/jquery.mask.min.js') }}?{{ config('app.version') }}"></script>

  <script>
    const table = new DataTable('#tindakan-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.produk.dt', {
        jenis: 'tindakan'
      }),
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
          width: '2%'
        },
        {
          data: 'name',
          name: 'name',
          sClass: 'text-start'
        },
        {
          data: 'tarif',
          name: 'tarif',
          sClass: 'text-end',
          width: "15%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'tarif_rawat_inap',
          name: 'tarif_rawat_inap',
          sClass: 'text-start',
          width: "15%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "15%"
        },
      ]
    });

    document.addEventListener('alpine:init', () => {
      Alpine.data('form', () => ({
        title: '',
        tarif_view: '',
        form: {
          id: null,
          name: '',
          tarif: '',
          jenis: 'tindakan'
        },
        endPoint: '',
        errors: {},
        loading: false,

        modalControl(action, title, data = null) {
          this.resetForm();
          this.title = title;

          if (action == 'create') {
            delete this.form._method;
            this.endPoint = route('api.master.produk.store')
          }

          if (action == 'edit') {
            this.form = {
              ...data,
              _method: 'PUT'
            };

            this.tarif_view = data.tarif.toLocaleString('en-US');

            this.endPoint = route('api.master.produk.update', data.id);
          }

          $('#modal-tindakan').modal('show');
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
            $('#modal-tindakan').modal('hide');
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

        formatUangTindakan() {
          // Hilangkan karakter non-digit (misal koma, titik, huruf)
          let angka = this.tarif_view.replace(/[^0-9]/g, '');

          if (angka === '') {
            this.form.tarif = '';
            this.$nextTick(() => {
              this.tarif_view = '';
            });
            return;
          }

          // Convert ke number
          if (angka === '') angka = '0';
          const number = parseInt(angka, 10);

          // Format ribuan

          this.$nextTick(() => {
            this.tarif_view = number.toLocaleString('en-US');
          });

          // Simpan nilai mentah ke form.tarif (biar dikirim ke backend)
          this.form.tarif = number;
        },

        resetForm() {
          this.tarif_view = '';
          this.form = {
            name: '',
            tarif: '',
            jenis: 'tindakan'
          };
          this.errors = {};
        }
      }))
    })

    const handleModal = (action, title, data = null) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.modalControl(action, title, data);
    }
  </script>
@endpush
