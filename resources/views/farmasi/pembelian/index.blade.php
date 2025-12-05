@extends('layouts.app')

@section('title', 'Pembelian Obat')
@section('subtitle', 'Farmasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="#" class="btn btn-primary btn-5" onclick="handleModal('create', 'Tambah Pembelian')">
    <div class="ti ti-plus me-1"></div>
    Pembelian
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="pembelian-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nomor</th>
              <th class="text-center">Tanggal Pembelian</th>
              <th class="text-center">PBF / Suplier</th>
              <th class="text-center">No Faktur</th>
              <th class="text-center">Tgl Faktur</th>
              <th class="text-center">Tgl Obat Datang</th>
              <th class="text-center">Ditambahkan Ke Stok</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
  <div x-data="form">
    <div class="modal modal-blur fade" id="modal-pembelian" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" x-text="title">Modal Title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmit" autocomplete="off">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">No Pembelian</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" x-model="form.nomor" :class="{ 'is-invalid': errors.nomor }">
                <div class="invalid-feedback" x-text="errors.nomor"></div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label required">Tanggal Pembelian</label>
                    <input type="date" class="form-control" autocomplete="off" x-model="form.tanggal" :class="{ 'is-invalid': errors.tanggal }">
                    <div class="invalid-feedback" x-text="errors.tanggal"></div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label required">Tanggal Obat Datang</label>
                    <input type="date" class="form-control" autocomplete="off" x-model="form.tanggal_obat_datang" :class="{ 'is-invalid': errors.tanggal_obat_datang }">
                    <div class="invalid-feedback" x-text="errors.tanggal_obat_datang"></div>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">PBF / Suplier</label>
                <select class="form-control" id="suplier_id" :class="{ 'is-invalid': errors.suplier_id }">
                  <option value=""></option>
                </select>
                <div class="invalid-feedback" x-text="errors.suplier_id"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">No Faktur</label>
                <input type="text" class="form-control" autocomplete="off" x-model="form.no_faktur" :class="{ 'is-invalid': errors.no_faktur }">
                <div class="invalid-feedback" x-text="errors.no_faktur"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">Tanggal Faktur</label>
                <input type="date" class="form-control" autocomplete="off" x-model="form.tgl_faktur" :class="{ 'is-invalid': errors.tgl_faktur }">
                <div class="invalid-feedback" x-text="errors.tgl_faktur"></div>
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
    const table = new DataTable('#pembelian-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.farmasi.pembelian.dt'),
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
          data: 'suplier.name',
          name: 'suplier.name',
          sClass: 'text-start'
        },
        {
          data: 'no_faktur',
          name: 'no_faktur',
          sClass: 'text-center'
        },
        {
          data: 'tgl_faktur',
          name: 'tgl_faktur',
          sClass: 'text-center'
        },
        {
          data: 'tanggal_obat_datang',
          name: 'tanggal_obat_datang',
          sClass: 'text-center'
        },
        {
          data: 'insert_stok',
          name: 'insert_stok',
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
          tanggal_obat_datang: '{{ date('Y-m-d') }}',
          suplier_id: '',
          no_faktur: '',
          tgl_faktur: '',
          created_by: ''
        },
        actionForm: '',
        endPoint: '',
        errors: {},
        loading: false,

        init() {
          let searchResultsSuplier = [];
          let selectSuplier = $('#suplier_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih PBF / Suplier",
            searchInputPlaceholder: 'Cari PBF / Suplier',
            allowClear: true,
            dropdownParent: $('#modal-pembelian'),
            tags: true,
            ajax: {
              url: route('api.master.suplier.json'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultsSuplier = response.data;
                return {
                  results: response.data
                }
              }
            },
            createTag: function(params) {
              var term = $.trim(params.term);

              if (term === '') {
                return null;
              }

              if (searchResultsSuplier.length > 0) {
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
            this.form.suplier_id = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.suplier.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text,
                  alamat: '-',
                  telp: '-'
                },
                dataType: 'json'
              }).done((response) => {
                // ✅ Update option dengan ID dari backend
                const newId = response.data.id; // misal backend return {data: {id: 123, name: "mg"}}

                // Update option yang baru dibuat dengan ID asli
                const $option = $('#suplier_id option[value="' + e.params.data.text + '"]');
                $option.val(newId); // Ganti value dari text ke ID

                // Update form value juga
                this.form.suplier_id = newId;

                // Trigger change agar select2 update
                $('#suplier_id').val(newId).trigger('change');
              })
            }

            $('#aturan_pakai_id').select2('open');

          })
        },

        modalControl(action, title, data = null) {
          this.resetForm();
          this.title = title;

          this.actionForm = action;

          if (action == 'create') {
            delete this.form._method;
            this.endPoint = route('api.farmasi.pembelian.store')
          }

          if (action == 'edit') {
            this.form = {
              ...data,
              _method: 'PUT'
            };

            let suplier = new Option(data.suplier.name, data.suplier.id, true, true);

            $('#suplier_id').append(suplier).trigger('change');

            this.endPoint = route('api.farmasi.pembelian.update', data.id);
          }

          $('#modal-pembelian').modal('show');

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
            $('#modal-pembelian').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            if (this.actionForm == 'edit') {
              table.ajax.reload();
            } else {
              setTimeout(() => {
                window.location.href = route('farmasi.pembelian.show', response.data.id);
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
            tanggal_obat_datang: '{{ date('Y-m-d') }}',
            suplier_id: '',
            no_faktur: '',
            tgl_faktur: ''
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
