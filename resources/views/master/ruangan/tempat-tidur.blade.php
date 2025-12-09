@extends('layouts.app')

@section('title', 'Tempat Tidur - ' . $ruangan->name)
@section('subtitle', 'Master Data')

@push('css')
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('content')
  <!-- Table -->
  <div class="card" x-data="form" x-cloak>
    <div class="card-body">
      <form @submit.prevent="handleSubmit" autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Nama Kamar/Bed</label>
          <div class="row g-2">
            <div class="col">
              <input type="text" class="form-control" x-model="form.name" :class="{ 'is-invalid': errors.name }">
              <div class="invalid-feedback" x-text="errors.name"></div>
            </div>
            <div class="col-auto">
              <button type="submit" :disabled="loading" class="btn btn-2 btn-icon" aria-label="Button">
                <span x-show="loading" class="spinner-border spinner-border-sm"></span>
                <div class="ti ti-plus" x-show="!loading"></div>
              </button>
            </div>
          </div>
        </div>
      </form>
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="ruangan-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nama Kamar/Bed</th>
              <th class="text-center">Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
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
    const ruangan = @json($ruangan);

    const table = new DataTable('#ruangan-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.ruangan.tempat-tidur.dt', ruangan.id),
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
          data: 'status',
          name: 'status',
          sClass: 'text-center text-uppercase'
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
          ruangan_id: ruangan.id,
          name: '',
        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.master.ruangan.tempat-tidur.store', ruangan.id),
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
            ruangan_id: ruangan.id,
            name: '',
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
