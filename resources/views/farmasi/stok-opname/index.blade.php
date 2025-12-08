@extends('layouts.app')

@section('title', 'Stok Opname Obat')
@section('subtitle', 'Farmasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="#" class="btn btn-primary btn-5" onclick="handleModal('create', 'Tambah Stok Opname')">
    <div class="ti ti-plus me-1"></div>
    Stok Opname
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="stok_opname-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nomor</th>
              <th class="text-center">Tanggal Stok Opname</th>
              <th class="text-center">Created By</th>
              <th class="text-center">Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div x-data="form">
    <div class="modal modal-blur fade" id="modal-stok-opname" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" x-text="title">Modal Title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmit" autocomplete="off">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">No Stok Opname</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" x-model="form.nomor" :class="{ 'is-invalid': errors.nomor }">
                <div class="invalid-feedback" x-text="errors.nomor"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Tanggal Stok Opname</label>
                <input type="date" max="{{ date('Y-m-d') }}" class="form-control" autocomplete="off" x-model="form.tanggal" :class="{ 'is-invalid': errors.tanggal }">
                <div class="invalid-feedback" x-text="errors.tanggal"></div>
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
  <script src="{{ asset('libs/datatables/dataTables.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.bootstrap5.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.fixedHeader.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/dataTables.responsive.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/datatables/responsive.bootstrap5.js') }}?{{ config('app.version') }}"></script>
  <script>
    const table = new DataTable('#stok_opname-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.farmasi.stok-opname.dt'),
      order: [
        [
          2, 'desc'
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
          data: 'nomor',
          name: 'nomor',
          sClass: 'text-center'
        },
        {
          data: 'tanggal',
          name: 'tanggal',
          sClass: 'text-center'
        },
        {
          data: 'user.name',
          name: 'user.name',
          sClass: 'text-center'
        },
        {
          data: 'status',
          name: 'status',
          sClass: 'text-center'
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
        form: {
          id: null,
          nomor: '',
          tanggal: '{{ date('Y-m-d') }}',
          created_by: ''
        },
        actionForm: '',
        endPoint: '',
        errors: {},
        loading: false,

        modalControl(action, title, data = null) {
          this.resetForm();
          this.title = title;

          this.actionForm = action;

          if (action == 'create') {
            delete this.form._method;
            this.endPoint = route('api.farmasi.stok-opname.store')
          }

          if (action == 'edit') {
            this.form = {
              ...data,
              _method: 'PUT'
            };

            this.endPoint = route('api.farmasi.stok-opname.update', data.id);
          }

          $('#modal-stok-opname').modal('show');

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
            $('#modal-stok-opname').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            if (this.actionForm == 'edit') {
              table.ajax.reload();
            } else {
              setTimeout(() => {
                window.location.href = route('farmasi.stok-opname.show', response.data.id);
              }, 500);
            }

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
            nomor: '',
            tanggal: '{{ date('Y-m-d') }}',
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
