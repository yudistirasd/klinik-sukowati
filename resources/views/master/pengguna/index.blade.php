@extends('layouts.app')

@section('title', 'Pengguna')
@section('subtitle', 'Master Data')

@push('css')
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="#" class="btn btn-primary btn-5" onclick="handleModal('create', 'Tambah Pengguna')">
    <div class="ti ti-plus me-1"></div>
    Pengguna
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered" id="user-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nama</th>
              <th class="text-center">Username</th>
              <th class="text-center">Level Akses</th>
              <th class="text-center">NIK</th>
              <th class="text-center">IHS ID</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div x-data="form">
    <div class="modal modal-blur fade" id="modal-user" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" x-text="title">Modal Title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmit" autocomplete="off">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">Nama</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.name" :class="{ 'is-invalid': errors.name }">
                <div class="invalid-feedback" x-text="errors.name"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Username</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.username" :class="{ 'is-invalid': errors.username }">
                <div class="invalid-feedback" x-text="errors.username"></div>
              </div>
              <div class="row">
                <div class="col-12" x-show="form.id">
                  <div class="alert alert-info" role="alert">
                    <div class="alert-icon">
                      <i class="ti ti-info-circle"></i>
                    </div>
                    Jika sedang tidak mengubah password pengguna, silahkan dikosongkan.
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label required">Password</label>
                    <input type="password" class="form-control" autocomplete="off" x-model="form.password" :class="{ 'is-invalid': errors.password }">
                    <div class="invalid-feedback" x-text="errors.password"></div>
                  </div>
                </div>
                <div class="col-6">
                  <div class="mb-3">
                    <label class="form-label required">Konfirmasi Password</label>
                    <input type="password" class="form-control" autocomplete="off" x-model="form.password_confirmation" :class="{ 'is-invalid': errors.password }">
                    <div class="invalid-feedback" x-text="errors.password"></div>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Level Akses</label>
                <select class="form-select" x-model="form.role" :class="{ 'is-invalid': errors.role }" x-on:change="onChangeRole">
                  <option value="">Pilih Level</option>
                  @foreach ($roles as $role)
                    <option value="{{ $role->id }}" data-nakes="{{ $role->nakes }}">{{ $role->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback" x-text="errors.role"></div>
              </div>
              <div class="mb-3" x-show="form.nakes" x-transition>
                <label class="form-label">NIK</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.nik" :class="{ 'is-invalid': errors.nik }">
                <div class="invalid-feedback" x-text="errors.nik"></div>
              </div>
              <div class="mb-3" x-show="form.nakes" x-transition>
                <label class="form-label">IHS ID</label>
                <input type="text" disabled class="form-control" autocomplete="off" x-model="form.ihs_id" :class="{ 'is-invalid': errors.ihs_id }">
                <small class="form-hint">
                  <i>Data akan diverifikasi ke Satu Sehat menggunakan NIK untuk mendapatkan IHS ID.</i>
                </small>
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
    const roles = {!! $roles !!}
    const table = new DataTable('#user-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.pengguna.dt'),
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
          data: 'username',
          name: 'username',
          sClass: 'text-start'
        },
        {
          data: 'role',
          name: 'role',
          sClass: 'text-center'
        },
        {
          data: 'nik',
          name: 'nik',
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
          username: '',
          password: '',
          password_confirmation: '',
          role: '',
          nakes: '',
          nik: '',
          ihs_id: ''
        },
        endPoint: '',
        errors: {},
        loading: false,

        onChangeRole(event) {
          const opt = event.target.selectedOptions[0];
          this.form.nakes = opt ? opt.dataset.nakes : '';
        },

        modalControl(action, title, data = null) {
          this.resetForm();
          this.title = title;

          if (action == 'create') {
            delete this.form._method;
            this.endPoint = route('api.master.pengguna.store')
          }

          if (action == 'edit') {
            let selectedRole = roles.find((row) => row.id == data.role);


            this.form = {
              ...data,
              nakes: selectedRole.nakes,
              _method: 'PUT'
            };

            this.endPoint = route('api.master.pengguna.update', data.id);
          }

          $('#modal-user').modal('show');

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
            $('#modal-user').modal('hide');
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
            username: '',
            password: '',
            password_confirmation: '',
            role: '',
            nakes: '',
            nik: '',
            ihs_id: ''
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
