@extends('layouts.app')

@section('title', 'Pasien Baru')
@section('subtitle', 'Registrasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
@endpush

@section('action-page')
  <a href="{{ route('registrasi.pasien.index') }}" class="btn btn-icon btn-dark btn-5">
    <div class="ti ti-arrow-left me-1"></div>
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card" x-data="form" x-init="initSelect2()">
    <form @submit.prevent="handleSubmit" autocomplete="off">
      <div class="card-body">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <div class="row mb-3">
              <div class="col-md">
                <label class="form-label">No RM</label>
                <input type="text" class="form-control" disabled placeholder="Otomatis oleh sistem">
              </div>
              <div class="col-md">
                <label class="form-label">NIK</label>
                <input type="text" class="form-control" x-model="form.nik" :class="{ 'is-invalid': errors.nik }">
                <div class="invalid-feedback" x-text="errors.nik"></div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama</label>
              <input type="text" class="form-control" x-model="form.nama" :class="{ 'is-invalid': errors.nama }">
              <div class="invalid-feedback" x-text="errors.nama"></div>

            </div>
            <div class="row mb-3">
              <div class="col-md-6 col-sm-12">
                <label class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control" x-model="form.tempat_lahir" :class="{ 'is-invalid': errors.tempat_lahir }">
                <div class="invalid-feedback" x-text="errors.tempat_lahir"></div>

              </div>
              <div class="col-md-6 col-sm-12">
                <label class="form-label">Tgl Lahir</label>
                <input type="date" class="form-control" x-model="form.tanggal_lahir" :class="{ 'is-invalid': errors.tanggal_lahir }">
                <div class="invalid-feedback" x-text="errors.tanggal_lahir"></div>

              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Jenis Kelamin</label>
                <div :class="{ 'is-invalid': errors.jenis_kelamin }">
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radios-inline" value="L" x-model="form.jenis_kelamin">
                    <span class="form-check-label">Laki Laki</span>
                  </label>
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="radios-inline" value="P" x-model="form.jenis_kelamin">
                    <span class="form-check-label">Perempuan</span>
                  </label>
                </div>
                <div class="invalid-feedback" x-text="errors.jenis_kelamin"></div>
                @error('jenis_kelamin')
                  <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
              <div class="col-md-3 col-sm-12">
                <label class="form-label">Agama</label>
                <select name="agama_id" class="form-control" x-model="form.agama_id" :class="{ 'is-invalid': errors.agama_id }">
                  <option value="">-- Pilih --</option>
                  @foreach ($agama as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback" x-text="errors.agama_id"></div>
              </div>
              <div class="col-md-3 col-sm-12">
                <label class="form-label">Pekerjaan</label>
                <select name="pekerjaan_id" class="form-control" x-model="form.pekerjaan_id" :class="{ 'is-invalid': errors.pekerjaan_id }">
                  <option value="">-- Pilih --</option>
                  @foreach ($pekerjaan->sortBy('name') as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                  @endforeach
                </select>
                <div class="invalid-feedback" x-text="errors.pekerjaan_id"></div>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">No HP</label>
              <input type="text" class="form-control" x-model="form.nohp" :class="{ 'is-invalid': errors.nohp }">
              <div class="invalid-feedback" x-text="errors.nohp"></div>
            </div>
          </div>
          <div class="col-md-6 col-sm-12">
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <input type="text" class="form-control" x-model="form.alamat" :class="{ 'is-invalid': errors.alamat }">
              <div class="invalid-feedback" x-text="errors.alamat"></div>
            </div>
            <div class="mb-3">
              <div class="form-label">Provinsi</div>
              <select class="form-control" id="provinsi" name="provinsi_id" :class="{ 'is-invalid': errors.provinsi_id }">
                <option value=""></option>
              </select>
              <div class="invalid-feedback" x-text="errors.provinsi_id"></div>
            </div>

            <div class="mb-3">
              <div class="form-label">Kabupaten</div>
              <select class="form-control" id="kabupaten" name="kabupaten_id" :class="{ 'is-invalid': errors.kabupaten_id }">
                <option value=""></option>
              </select>
              <div class="invalid-feedback" x-text="errors.kabupaten_id"></div>
            </div>

            <div class="mb-3">
              <div class="form-label">Kecamatan</div>
              <select class="form-control" id="kecamatan" name="kecamatan_id" :class="{ 'is-invalid': errors.kecamatan_id }">
                <option value=""></option>
              </select>
              <div class="invalid-feedback" x-text="errors.kecamatan_id"></div>
            </div>

            <div class="mb-3">
              <div class="form-label">Kelurahan</div>
              <select class="form-control" id="kelurahan" name="kelurahan_id" :class="{ 'is-invalid': errors.kelurahan_id }">
                <option value=""></option>
              </select>
              <div class="invalid-feedback" x-text="errors.kelurahan_id"></div>
            </div>

          </div>
        </div>
      </div>
      <div class="card-footer text-end">
        <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
          <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
          Simpan
        </button>
      </div>
    </form>
  </div>

@endsection


@push('js')
  <script src="{{ asset('libs/select2/select2.min.js') }}?{{ config('app.version') }}"></script>
  <script src="{{ asset('libs/select2/select2-searchInputPlaceholder.js') }}?{{ config('app.version') }}"></script>
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('form', () => ({
        form: {
          id: null,
          nama: '',
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
        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.registrasi.pasien.store'),
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
            Swal.fire({
              icon: 'success',
              title: 'Sukses !',
              text: response.message,
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#202020",
              confirmButtonText: "Lanjut registrasi",
              cancelButtonText: "Kembali"
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = route('registrasi.kunjungan.create', response.data.id)
              } else {
                window.location.href = route('registrasi.pasien.index');
              }
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

        initSelect2() {

          let defaultProvinsi = {
            id: 33,
            text: 'Jawa Tengah'
          };


          let selectProvinsi = $('select[name=provinsi_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Provinsi",
            searchInputPlaceholder: 'Cari Provinsi',
            allowClear: true,
            ajax: {
              url: route('api.master.wilayah.provinsi'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }

                // Query parameters will be ?search=[term]&type=public
                return query;
              },
              processResults: function(response) {
                return {
                  results: response
                }
              }
            }
          }).on('change', (e) => {
            let value = e.target.value;

            this.form.provinsi_id = value;
          })


          let selectKabupaten = $('select[name=kabupaten_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kabupaten",
            allowClear: true,
            searchInputPlaceholder: 'Cari Kabupaten',
            ajax: {
              url: route('api.master.wilayah.kabupaten'),
              data: function(params) {
                let provinsi_id = $('select[name=provinsi_id').val();
                return {
                  keyword: params.term,
                  provinsi_id
                };
              },
              processResults: function(response) {
                return {
                  results: response
                }
              }
            }
          }).on('change', (e) => {
            let value = e.target.value;

            this.form.kabupaten_id = value;
          })

          let selectKecamatan = $('select[name=kecamatan_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kecamatan",
            allowClear: true,
            searchInputPlaceholder: 'Cari Kecamatan',
            ajax: {
              url: route('api.master.wilayah.kecamatan'),
              data: function(params) {
                let kabupaten_id = $('select[name=kabupaten_id').val();
                return {
                  keyword: params.term,
                  kabupaten_id
                };
              },
              processResults: function(response) {
                return {
                  results: response
                }
              }
            }
          }).on('change', (e) => {
            let value = e.target.value;

            this.form.kecamatan_id = value;
          })

          let selectKelurahan = $('select[name=kelurahan_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Kelurahan",
            allowClear: true,
            searchInputPlaceholder: 'Cari Kelurahan',
            ajax: {
              url: route('api.master.wilayah.kelurahan'),
              data: function(params) {
                let kecamatan_id = $('select[name=kecamatan_id').val();
                return {
                  keyword: params.term,
                  kecamatan_id
                };
              },
              processResults: function(response) {
                return {
                  results: response
                }
              }
            }
          }).on('change', (e) => {
            let value = e.target.value;

            this.form.kelurahan_id = value;
          })

          selectProvinsi.on('select2:select', function(e) {
            let data = e.params.data;
            selectKabupaten.prop('disabled', false);
            selectKabupaten.select2('open');
          })

          selectProvinsi.on('select2:unselect', function(e) {
            selectKabupaten.prop('disabled', true);
            selectKabupaten.val(null).trigger('change');

            selectKecamatan.prop('disabled', true);
            selectKecamatan.val(null).trigger('change');

            selectKelurahan.prop('disabled', true);
            selectKelurahan.val(null).trigger('change');
          })

          selectKabupaten.on('select2:select', function(e) {
            let data = e.params.data;
            selectKecamatan.prop('disabled', false);
            selectKecamatan.select2('open');
          })

          selectKabupaten.on('select2:unselect', function(e) {
            selectKecamatan.prop('disabled', true);
            selectKecamatan.val(null).trigger('change');

            selectKelurahan.prop('disabled', true);
            selectKelurahan.val(null).trigger('change');
          })

          selectKecamatan.on('select2:select', function(e) {
            let data = e.params.data;
            selectKelurahan.prop('disabled', false);
            selectKelurahan.select2('open');
          })

          selectKecamatan.on('select2:unselect', function(e) {
            selectKelurahan.prop('disabled', true);
            selectKelurahan.val(null).trigger('change');
          })

          selectKelurahan.on('select2:select', function(e) {
            let data = e.params.data;
            $('input[name=kode_pos').val(data.postal_code)
          })

          selectKelurahan.on('select2:unselect', function(e) {
            $('input[name=kelurahan').val('')
            $('input[name=kode_pos').val('')
          })

          const option = new Option(defaultProvinsi.text, defaultProvinsi.id, true, true);
          selectProvinsi.append(option).trigger('change');
        },

        resetForm() {
          this.form = {
            nama: '',
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

    const handleModal = (action, title, data = null) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.modalControl(action, title, data);
    }
  </script>
@endpush
