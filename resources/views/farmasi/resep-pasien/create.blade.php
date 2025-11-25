@extends('layouts.app')

@section('title', 'Buat Resep Luar')

@section('subtitle', 'Resep Pasien')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
@endpush

@section('content')
  <div x-data="Resep" x-cloak>
    <div class="card mb-3">
      <div class="card-header">
        <h3 class="card-title">Identitas Pasien</h3>
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
                <div class="datagrid-title">No RM</div>
                <div class="datagrid-content">{{ $pasien->norm }}</div>
              </div>
              <div class="datagrid-item">
                <div class="datagrid-title">Nama</div>
                <div class="datagrid-content">{{ $pasien->nama }}</div>
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

    <div class="card mb-3">
      <div class="card-body">
        @if (Auth::user()->hasRole('apoteker'))
          <form @submit.prevent="handleSubmit" autocomplete="off" id="resep">
            <div class="row">
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Tgl Resep</label>
                  <input type="text" class="form-control" autocomplete="off" id="tanggal" x-model="form.tanggal">
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">No Resep</label>
                  <input type="text" disabled class="form-control" autocomplete="off" placeholder="Otomatis dari sistem" x-model="form.nomor" :class="{ 'is-invalid': errors.nomor }">
                  <div class="invalid-feedback" x-text="errors.nomor"></div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Dokter External</label>
                  <div class="row">
                    <div class="col">
                      <select x-model="form.dokter_id" id="dokter" class="form-select" :class="{ 'is-invalid': errors.dokter_id }">
                        <option value=""></option>
                        @foreach ($dokter as $item)
                          <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                      </select>
                      <div class="invalid-feedback" x-text="errors.dokter_id"></div>
                    </div>
                    <div class="col-auto">
                      <button type="button" data-bs-toggle="modal" data-bs-target="#modal-dokter-external" class="btn btn-2 btn-icon" aria-label="Button">
                        <i class="ti ti-plus"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="mb-3 text-end">
              <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                Simpan
              </button>
            </div>
          </form>
        @endif
      </div>
    </div>

    <div class="modal modal-blur fade" id="modal-dokter-external" tabindex="-1" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Dokter External</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmiDokterExternal" autocomplete="off">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label required">Nama Dokter</label>
                <input type="text" class="form-control" autocomplete="off" x-model="formDokterExternal.name" :class="{ 'is-invalid': errorsDokterExternal.name }">
                <div class="invalid-feedback" x-text="errorsDokterExternal.name"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">No HP Dokter</label>
                <input type="text" class="form-control" autocomplete="off" x-model="formDokterExternal.nohp" :class="{ 'is-invalid': errorsDokterExternal.nohp }">
                <div class="invalid-feedback" x-text="errorsDokterExternal.nohp"></div>
              </div>
              <div class="mb-3">
                <label class="form-label required">SIP Dokter</label>
                <input type="text" class="form-control" autocomplete="off" x-model="formDokterExternal.sip" :class="{ 'is-invalid': errorsDokterExternal.sip }">
                <div class="invalid-feedback" x-text="errorsDokterExternal.sip"></div>
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
  <script>
    let pasien = @json($pasien);

    document.addEventListener('alpine:init', () => {
      Alpine.data('Resep', () => ({
        datePicker: {},
        dokter: '',
        mask: {},
        form: {
          tanggal: '{{ date('Y-m-d') }}',
          pasien_id: pasien.id,
          dokter_id: null,
        },
        formDokterExternal: {
          name: '',
        },
        errorsDokterExternal: {},
        endPoint: '',
        errors: {},
        loading: false,
        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.farmasi.resep-pasien.external.store'),
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
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            window.location.href = route('farmasi.resep-pasien.show', response.data.id);

          }).fail((error) => {
            if (error.status === 422) {
              this.errors = error.responseJSON.errors;

              Toast.fire({
                icon: 'error',
                title: error.responseJSON.message
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan !',
                text: error.responseJSON.message
              });
            }
          })
        },
        handleSubmiDokterExternal() {
          this.loading = true;
          this.errorsDokterExternal = {};

          $.ajax({
            url: route('api.master.pengguna.dokter-external.store'),
            method: 'POST',
            data: this.formDokterExternal,
            dataType: 'json',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            complete: () => {
              this.loading = false;
            }
          }).done((response) => {
            Toast.fire({
              icon: 'success',
              title: response.message
            });

            $('#modal-dokter-external').modal('hide');

            this.updateOptionDokter(response.data);

            this.resetFormDokterExternal();

            this.form.dokter_id = response.data.dokter_external.id;

          }).fail((error) => {
            if (error.status === 422) {
              this.errorsDokterExternal = error.responseJSON.errors;

              Toast.fire({
                icon: 'error',
                title: error.responseJSON.message
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan !',
                text: error.responseJSON.message
              });
            }
          })
        },
        resetFormDokterExternal() {
          this.formDokterExternal = {
            name: '',
          };
          this.errorsDokterExternal = {};
        },

        updateOptionDokter(data) {
          let options = '';
          $.each(data.options_dokter, function(index, item) {
            let selected = data.dokter_external.id == item.id ? 'selected' : '';
            options += `<option value="${item.id}" ${selected}>${item.name}</option>`;
          });

          $('#dokter').html(options);
        },

        init() {
          let tanggal_resep = document.getElementById('tanggal');
          this.datePicker = new tempusDominus.TempusDominus(document.getElementById('tanggal'), {
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
              components: {
                calendar: true,
                date: true,
                month: true,
                year: true,
                decades: true,
                clock: false,
                hours: false,
                minutes: false,
                seconds: false,
                useTwentyfourHour: undefined
              },
              viewMode: 'calendar',
              toolbarPlacement: 'bottom',
              theme: 'light',
            },
            localization: {
              format: 'yyyy-MM-dd',
            },
            restrictions: {
              maxDate: new Date()
            }
          });

          tanggal_resep.addEventListener('change.td', (e) => {
            let selected = e.detail.date.format('yyyy-MM-dd')

            this.form.tanggal = e.detail.date ?
              e.detail.date.format('yyyy-MM-dd') :
              '';
          });
          this.datePicker.dates.setValue(new tempusDominus.DateTime(this.form.tanggal));
        },


      }))
    })
  </script>
@endpush
