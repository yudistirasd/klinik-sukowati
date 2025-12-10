@extends('layouts.app')

@section('title', 'Kunjungan Pasien')
@section('subtitle', 'Registrasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="{{ route('registrasi.pasien.index') }}" class="btn btn-icon btn-dark btn-5">
    <div class="ti ti-arrow-left me-1"></div>
  </a>
@endsection

@section('content')
  <div class="card mb-3">
    <div class="card-header">
      <h3 class="card-title">Identitas Pasien</h3>
      <div class="card-actions">
        <a href="{{ route('registrasi.pasien.edit', $pasien->id) }}" class="btn btn-warning btn-sm">
          <i class='ti ti-edit me-1 fs-2'></i>
          Pasien
        </a>
      </div>
    </div>
    <div class="card-body">
      <div class="row gap-2">
        <div class="col-auto">
          @php
            $avatar = $pasien->jenis_kelamin == 'L' ? 'avatar_male.jpg' : 'avatar_female.jpg';
          @endphp
          <span class="avatar avatar-xl" style="background-image: url(/img/{{ $avatar }})"> </span>
        </div>
        <div class="col-md-10 col-sm-12">
          <div class="datagrid">
            <div class="datagrid-item">
              <div class="datagrid-title">NIK</div>
              <div class="datagrid-content">{{ $pasien->nik }}</div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">No RM</div>
              <div class="datagrid-content">{{ $pasien->norm }}</div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Tempat & Tgl Lahir</div>
              <div class="datagrid-content">{{ $pasien->tempat_lahir }}, {{ $pasien->tanggal_lahir }}</div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Usia</div>
              <div class="datagrid-content">{{ $pasien->usia }}</div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Jenis Kelamin</div>
              <div class="datagrid-content">
                {{ $pasien->jenis_kelamin_text }}
              </div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Agama</div>
              <div class="datagrid-content">{{ $pasien->agama->name }}</div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Pekerjaan</div>
              <div class="datagrid-content">
                {{ $pasien->pekerjaan->name }}
              </div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">No. HP</div>
              <div class="datagrid-content">
                {{ $pasien->nohp }}
              </div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Alamat</div>
              <div class="datagrid-content">
                {{ $pasien->alamat }}, {{ $pasien->kelurahan->name }}, {{ $pasien->kecamatan->name }}, {{ $pasien->kabupaten->name }}, {{ $pasien->provinsi->name }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="card" x-data="form" x-cloak>
    <form @submit.prevent="handleSubmit" autocomplete="off">
      <div class="card-header">
        <h3 class="card-title">Kunjungan Pasien</h3>
      </div>
      <div class="card-body">
        <input type="hidden" name="pasien_id" x-model="form.pasien_id">
        <input type="hidden" name="icd10_id" x-model="form.icd10_id">
        <div class="row">
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label">No Kunjungan</label>
              <input type="text" class="form-control" disabled placeholder="Otomatis dari sistem">
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Tanggal Registrasi</label>
              <input type="text" class="form-control" id="tanggal-registrasi" x-model="form.tanggal_registrasi" :class="{ 'is-invalid': errors.tanggal_registrasi }">
              <div class="invalid-feedback" x-text="errors.tanggal_registrasi"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Dokter</label>
              <select x-model="form.dokter_id" id="" class="form-select" :class="{ 'is-invalid': errors.dokter_id }">
                <option value=""></option>
                @foreach ($dokter as $item)
                  <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback" x-text="errors.dokter_id"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Jenis Pembayaran</label>
              <select x-model="form.jenis_pembayaran" id="" class="form-select" :class="{ 'is-invalid': errors.jenis_pembayaran }">
                <option value=""></option>
                @foreach ($jenis_pembayaran as $item)
                  <option value="{{ $item }}">{{ $item }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback" x-text="errors.jenis_pembayaran"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Jenis Layanan</label>
              <select x-model="form.jenis_layanan" id="" class="form-select" :class="{ 'is-invalid': errors.jenis_layanan }">
                <option value=""></option>
                @foreach ($jenis_layanan as $item)
                  <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
              </select>
              <div class="invalid-feedback" x-text="errors.jenis_layanan"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Ruang / Klinik</label>
              <select x-model="form.ruangan_id" id="ruangan_id" class="form-select" :class="{ 'is-invalid': errors.ruangan_id }">
                <option value=""></option>
              </select>
              <div class="invalid-feedback" x-text="errors.ruangan_id"></div>
            </div>
          </div>
          <div class="col-md-2 col-sm-12" x-show="form.jenis_layanan == 'RI'">
            <div class="mb-3">
              <label class="form-label">Tempat Tidur</label>
              <select x-model="form.tempat_tidur_last_id" id="tempat_tidur_last_id" class="form-select" :class="{ 'is-invalid': errors.tempat_tidur_last_id }">
                <option value=""></option>
              </select>
              <div class="invalid-feedback" x-text="errors.tempat_tidur_last_id"></div>
            </div>
          </div>
          <div class="col-md-4 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Jenis Penyakit</label>
              <div class="row g-2">
                <div class="col">
                  <input type="text" class="form-control" x-model="icd10_selected" readonly placeholder="Pilih Jenis Penyakit" :class="{ 'is-invalid': errors.icd10_id }">
                  <div class="invalid-feedback" x-text="errors.icd10_id"></div>

                </div>
                <div class="col-auto">
                  <a href="#" class="btn btn-2 btn-icon" aria-label="Button" data-bs-toggle="modal" data-bs-target="#modal-icd10">
                    <!-- Download SVG icon from http://tabler.io/icons/icon/search -->
                    <div class="ti ti-search"></div>
                  </a>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="row">


          <div class="col-md-2 col-sm-12">
            <label for="" class="form-label text-white">x</label>
            <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
              <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
              Simpan
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>

  <div class="modal modal-blur fade" id="modal-icd10" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" x-text="title">Pencarian ICD 10 </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive col-sm-12">
            <table id="diagnosa-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm" style="width: 100%;">
              <thead>
                <tr>
                  <th class="text-center">No.</th>
                  <th class="text-center">Kode</th>
                  <th class="text-center">Nama ICD</th>
                  <th class="text-center">Nama Indonesia</th>
                  <th class="text-center">Act</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
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
    let pasien = {!! json_encode($pasien) !!};

    const table = new DataTable('#diagnosa-table', {
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.master.icd10.dt'),
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
          "data": "code"
        },
        {
          "data": "display_en"
        },
        {
          "data": "display_id"
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
        icd10_selected: '',
        form: {
          id: null,
          pasien_id: pasien.id,
          icd10_id: '',
          tanggal_registrasi: '{{ date('Y-m-d h:i') }}',
          jenis_pembayaran: 'UMUM',
          jenis_layanan: '',
          dokter_id: '',
          ruangan_id: '',
          tempat_tidur_last_id: '',
          created_by: '{{ Auth::id() }}'
        },
        endPoint: '',
        errors: {},
        loading: false,
        selectRuangan: {},
        initTempatTidur() {

        },

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.registrasi.kunjungan.store'),
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
            // this.resetForm();
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            setTimeout(() => {
              window.location.href = route('registrasi.kunjungan.index')
            }, 500);


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


        selectIcd10(row) {
          this.form.icd10_id = row.id;
          this.icd10_selected = `${row.code} - ${row.display_en}`;

          Toast.fire({
            icon: 'success',
            title: this.icd10_selected + ' berhasil dipilih'
          });

          $('#modal-icd10').modal('hide');
        },

        init() {
          let tanggal_registrasi = document.getElementById('tanggal-registrasi');
          new tempusDominus.TempusDominus(document.getElementById('tanggal-registrasi'), {
            display: {
              icons: {
                type: 'icons',
                time: 'ti ti-clock',
                date: 'ti ti-calendar',
                up: 'ti ti-arrow-up',
                down: 'ti ti-arrow-down',
                previous: 'ti ti-chevron-left',
                next: 'ti ti-chevron-right',
                today: 'ti ti-calendar-check',
                clear: 'ti ti-trash',
                close: 'ti ti-xmark'
              },
              viewMode: 'calendar',
              toolbarPlacement: 'bottom',
              theme: 'light',
            },
            localization: {
              format: 'yyyy-MM-dd HH:mm',
              hourCycle: 'h23',
            },
            restrictions: {
              maxDate: new Date()
            }
          });

          tanggal_registrasi.addEventListener('change.td', (e) => {
            let selected = e.detail.date.format('yyyy-MM-dd HH:mm')

            this.form.tanggal_registrasi = e.detail.date ?
              e.detail.date.format('yyyy-MM-dd HH:mm') :
              '';
          });

          this.$watch('form.jenis_layanan', (value, oldValue) => {
            this.form.ruangan_id = null;
            $('#ruangan_id').val(null).trigger('change');
          })

          $('#ruangan_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Ruangan/Klinik",
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.ruangan.select2'),
              data: (params) => {
                var query = {
                  layanan: this.form.jenis_layanan,
                }

                return query;
              },
              processResults: function(response) {
                return {
                  results: response.data
                }
              }
            },
          }).on('change', (e) => {
            let value = e.target.value;
            this.form.ruangan_id = value;
          })

          $('#tempat_tidur_last_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Tempat Tidur",
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.tempat-tidur.select2'),
              data: (params) => {
                var query = {
                  ruangan_id: this.form.ruangan_id,
                }
                return query;
              },
              processResults: function(response) {
                return {
                  results: response.data
                }
              }
            },
          }).on('change', (e) => {
            let value = e.target.value;
            this.form.tempat_tidur_last_id = value;
          })
        },


        resetForm() {
          this.form = {
            name: '',
            nik: '',
            tempat_lahir: '',
            tanggal_lahir: '',
            jenis_kelamin: '',
            agama_id: '',
            pekerjaan_id: '',
            nohp: '',
            alamat: '',
            provinsi_id: '',
            kabupaten_id: '',
            kelurahan_id: '',
            kecamatan_id: '',
          };
          this.errors = {};
        }
      }))
    })

    const selectIcd10 = (row) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.selectIcd10(row);
    }
  </script>
@endpush
