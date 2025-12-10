@php
  $title = 'Setting Tarif ' . ucfirst($produk->jenis) . ' Rawat Inap';
@endphp
@extends('layouts.app')

@section('title', $title)

@section('subtitle', 'Master Data')

@section('content')
  <div class="row align-items-center">
    <div class="col-auto">
      <i class="ti ti-settings-dollar" style="font-size: xxx-large;"></i>
    </div>
    <div class="col">
      <h1 class="fw-bold m-0">{{ $produk->name }}</h1>
      <div class="list-inline list-inline-dots text-secondary fs-3">
        <div class="list-inline-item text-capitalize">
          <i class="ti ti-wheelchair fs-3"></i>
          Tarif Rawat Jalan {{ formatUang($produk->tarif) }}
        </div>
      </div>
    </div>
  </div>

  <div class="row g-0">
    <div class="col-md-8 col-sm-12">
      <div class="card mt-3" x-data="Setting" x-cloak>
        <div class="card-body">
          <form @submit.prevent="handleSubmit">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label">Ruang / Klinik</label>
                  <select x-model="form.ruangan_id" id="" class="form-select" :class="{ 'is-invalid': errors.ruangan_id }">
                    <option value=""></option>
                    @foreach ($ruangan as $item)
                      <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                  </select>
                  <div class="invalid-feedback" x-text="errors.ruangan_id"></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label class="form-label required">Tarif Rawat Inap</label>
                  <div class="row">
                    <div class="col">
                      <input type="text" id="tarif_ranap" class="form-control" autocomplete="off" :class="{ 'is-invalid': errors.tarif }" placeholder="0">
                      <div class="invalid-feedback" x-text="errors.tarif"></div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" class="btn btn-2 btn-icon" :disabled="loading" aria-label="Button">
                        <span x-show="loading" class="spinner-border spinner-border-sm"></span>
                        <i class="ti ti-plus" x-show="!loading"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </form>

          <div class="table-responsive mt-3">
            <table class="table table-bordered table-hover" id="produk-tarif-inap-table">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Ruang</th>
                  <th class="text-center">Tarif</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
        <div class="card-footer bg-transparent mt-auto">
          <div class="btn-list justify-content-end">
            <a href="{{ route('master.produk.index', ['jenis' => $produk->jenis]) }}" class="btn btn-dark btn-2"> <i class="ti ti-arrow-left me-1"></i> Kembali </a>
          </div>
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
    const produk = @json($produk);
    const table = new DataTable('#produk-tarif-inap-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.produk.tarif-inap.get', produk.id),
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
          data: 'ruangan',
          name: 'ruangan',
          sClass: 'text-start',
          orderable: false,
        },
        {
          data: 'tarif',
          name: 'tarif',
          sClass: 'text-end',
          searchable: false,
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "10%",
          orderable: false,
          searchable: false,
        },
      ]
    });

    document.addEventListener('alpine:init', () => {
      Alpine.data('Setting', () => ({
        title: '',
        form: {
          produk_id: '{{ $produk->id }}',
          ruangan_id: null,
          tarif: null
        },
        mask_tarif_rawat_inap: {},
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.master.produk.tarif-inap.store', this.form.produk_id),
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
          this.mask_tarif_rawat_inap = IMask(document.getElementById('tarif_ranap'), {
            mask: Number,
            thousandsSeparator: ',',
          });

          this.mask_tarif_rawat_inap.on('complete', (value) => {
            this.form.tarif = this.mask_tarif_rawat_inap.unmaskedValue;
          })
        },

        resetForm() {
          this.form = {
            produk_id: '{{ $produk->id }}',
            ruangan_id: null,
            tarif: null
          };
          this.mask_tarif_rawat_inap.value = '';
          this.errors = {};
        }
      }))
    })
  </script>
@endpush
