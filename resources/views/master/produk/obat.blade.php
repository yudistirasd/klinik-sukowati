@extends('layouts.app')

@section('title', 'Obat')
@section('subtitle', 'Master Data')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="#" class="btn btn-primary btn-5" onclick="handleModal('create', 'Tambah Obat')">
    <div class="ti ti-plus me-1"></div>
    Obat
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="obat-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nama</th>
              <th class="text-center">Dosis</th>
              <th class="text-center">Sediaan</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div x-data="form" x-init="init()">
    <div class="modal modal-blur fade" id="modal-obat" tabindex="-1" data-bs-backdrop="static" role="dialog" aria-hidden="true">
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
                <label class="form-label required">Nama Obat</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.name" :class="{ 'is-invalid': errors.name }">
                <div class="invalid-feedback" x-text="errors.name"></div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label required">Dosis</label>
                    <input type="text" class="form-control" autocomplete="off" x-model="form.dosis" :class="{ 'is-invalid': errors.dosis }">
                    <div class="invalid-feedback" x-text="errors.dosis"></div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label required">Satuan</label>
                    <select class="form-control" name="satuan" :class="{ 'is-invalid': errors.satuan }">
                      <option value=""></option>
                    </select>
                    <div class="invalid-feedback" x-text="errors.satuan"></div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label required">Sediaan</label>
                    <select class="form-control" name="sediaan" :class="{ 'is-invalid': errors.sediaan }">
                      <option value=""></option>
                    </select>
                    <div class="invalid-feedback" x-text="errors.sediaan"></div>
                  </div>
                </div>
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
  <script src="{{ asset('libs/jquery.mask.min.js') }}?{{ config('app.version') }}"></script>

  <script>
    const table = new DataTable('#obat-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.produk.dt', {
        jenis: 'obat'
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
          width: '5%'
        },
        {
          data: 'name',
          name: 'name',
          sClass: 'text-start'
        },
        {
          data: 'dosis',
          name: 'dosis',
          sClass: 'text-center'
        },
        {
          data: 'sediaan',
          name: 'sediaan',
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
        tarif_view: '',
        form: {
          id: null,
          name: '',
          dosis: '',
          satuan: '',
          sediaan: '',
          jenis: 'obat'
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

            const optionSatuan = new Option(data.satuan, data.satuan, true, true);
            const optionSediaan = new Option(data.sediaan, data.sediaan, true, true);

            $('select[name=satuan]').append(optionSatuan).trigger('change');
            $('select[name=sediaan]').append(optionSediaan).trigger('change');


            this.endPoint = route('api.master.produk.update', data.id);
          }

          $('#modal-obat').modal('show');
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
            $('#modal-obat').modal('hide');
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

        init() {
          let searchResultsSatuan = [];
          $('select[name=satuan]').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Satuan",
            searchInputPlaceholder: 'Cari Satuan',
            allowClear: true,
            dropdownParent: $('#modal-obat'),
            tags: true,
            ajax: {
              url: route('api.master.farmasi.satuan-dosis.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultsSatuan = response.data;
                return {
                  results: response.data.map(item => ({
                    id: item.value,
                    text: item.text
                  }))
                }
              }
            },
            createTag: function(params) {
              console.log(params);
              var term = $.trim(params.term);

              if (term === '') {
                return null;
              }

              if (searchResultsSatuan.length > 0) {
                return null;
              }

              return {
                id: term,
                text: term,
                newTag: true // add additional parameters
              }
            },
          }).on('change', (e) => {
            let value = e.target.value;
            this.form.satuan = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.satuan-dosis.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                console.log('Response new tag', response);
              })
            }

            $('select[name=sediaan]').select2('open');
          })

          let searchResultsSediaan = [];
          $('select[name=sediaan]').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Sediaan",
            searchInputPlaceholder: 'Cari Sediaan',
            allowClear: true,
            dropdownParent: $('#modal-obat'),
            tags: true,
            ajax: {
              url: route('api.master.farmasi.sediaan.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {

                searchResultsSediaan = response.data;

                return {
                  results: response.data.map(item => ({
                    id: item.value,
                    text: item.text
                  }))
                }
              }
            },
            createTag: function(params) {
              var term = $.trim(params.term);

              if (term === '') {
                return null;
              }

              if (searchResultsSediaan.length > 0) {
                return null;
              }

              return {
                id: term,
                text: term,
                newTag: true // add additional parameters
              }
            },
          }).on('change', (e) => {
            let value = e.target.value;
            this.form.sediaan = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.sediaan.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                console.log('Response new tag', response);
              })
            }
          })
        },
        resetForm() {
          this.form = {
            id: null,
            name: '',
            dosis: '',
            satuan: '',
            sediaan: '',
            jenis: 'obat'
          };
          this.errors = {};

          $('select[name=satuan]').val(null).trigger('change');
          $('select[name=sediaan]').val(null).trigger('change');
        }
      }))
    })

    const handleModal = (action, title, data = null) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.modalControl(action, title, data);
    }
  </script>
@endpush
