@extends('layouts.app')

@section('title', 'Ruangan')
@section('subtitle', 'Master Data')

@push('css')
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="#" class="btn btn-primary btn-5" onclick="handleModal('create', 'Tambah Ruang')">
    <div class="ti ti-plus me-1"></div>
    Ruang
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="ruangan-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nama Ruang</th>
              <th class="text-center">Layanan</th>
              <th class="text-center">Departemen</th>
              <th class="text-center" style="width: 24%">IHS ID</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div x-data="form" x-cloak>
    <div class="modal modal-blur fade" id="modal-ruangan" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" x-text="title">Modal Title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmit" autocomplete="off">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">Departemen</label>
                <select class="form-select" x-model="form.departemen_id" :class="{ 'is-invalid': errors.departemen_id }">
                  <option value="">Pilih Departemen</option>
                  @foreach ($departemen as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback" x-text="errors.departemen_id"></div>
              </div>
              <div class="mb-3">
                <div class="form-label">Layanan</div>
                <div>
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" x-model="form.layanan" name="radios-inline" value="RJ" :class="{ 'is-invalid': errors.layanan }">
                    <span class="form-check-label">Rawat Jalan</span>
                  </label>
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" x-model="form.layanan" name="radios-inline" value="RI" :class="{ 'is-invalid': errors.layanan }">
                    <span class="form-check-label">Rawat Inap</span>
                  </label>
                  <div class="invalid-feedback d-block" x-text="errors.layanan"></div>

                </div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Nama Ruangan</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.name" :class="{ 'is-invalid': errors.name }">
                <div class="invalid-feedback" x-text="errors.name"></div>
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
  <script>
    const table = new DataTable('#ruangan-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.ruangan.dt'),
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
          data: 'name',
          name: 'name',
          sClass: 'text-start'
        },
        {
          data: 'layanan',
          name: 'layanan',
          sClass: 'text-center'
        },
        {
          data: 'departemen.name',
          name: 'departemen.name',
          sClass: 'text-center'
        },
        {
          data: 'ihs_id',
          name: 'ihs_id',
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
        title: '',
        form: {
          id: null,
          name: '',
          layanan: '',
          ihs_id: '',
          departemen_id: '',
        },
        endPoint: '',
        errors: {},
        loading: false,

        modalControl(action, title, data = null) {
          this.resetForm();
          this.title = title;

          if (action == 'create') {
            delete this.form._method;
            this.endPoint = route('api.master.ruangan.store')
          }

          if (action == 'edit') {
            this.form = {
              ...data,
              _method: 'PUT'
            };

            this.endPoint = route('api.master.ruangan.update', data.id);
          }

          $('#modal-ruangan').modal('show');

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
            $('#modal-ruangan').modal('hide');
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
            name: '',
            layanan: '',
            ihs_id: '',
            departemen_id: '',
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
