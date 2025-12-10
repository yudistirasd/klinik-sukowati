@extends('layouts.app')

@section('title', 'Pemeriksaan Pasien')
{{-- @section('subtitle', 'Ubah') --}}

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">

  @stack('pemeriksaan-css')
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
              <div class="datagrid-title">No Registrasi</div>
              <div class="datagrid-content">{{ $kunjungan->noregistrasi }}</div>
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
            <div class="datagrid-item">
              <div class="datagrid-title">Ruang/Klinik</div>
              <div class="datagrid-content">
                {{ $kunjungan->ruangan->name }}
              </div>
            </div>
            <div class="datagrid-item">
              <div class="datagrid-title">Dokter</div>
              <div class="datagrid-content">
                {{ $kunjungan->dokter->name }}
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="row">
    <div class="col-md-3 col-sm-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Riwayat Pemeriksaan</h3>
        </div>
        <div class="card-body">
          <ul class="steps steps-vertical">
            <li class="step-item">
              <div class="h4 m-0 text-secondary">Belum ada riwayat</div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <div class="col-md-9 col-sm-12">
      <div class="card">
        <div class="card-header">
          <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
            <li class="nav-item" role="presentation">
              <a href="#tabs-asesmen-medis" class="nav-link active" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Asesmen Medis</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-asesmen-perawat" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Asesmen Keperawatan</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-cppt" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">CPPT</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-tindakan" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Tindakan</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-resep" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Resep</a>
            </li>
            <li class="nav-item" role="presentation">
              <a href="#tabs-laborat" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1">Laboratorium</a>
            </li>
            <li class="nav-item ms-auto" role="presentation">
              <a href="#tabs-settings-1" class="nav-link" title="Settings" data-bs-toggle="tab" aria-selected="false" tabindex="-1" role="tab"><!-- Download SVG icon from http://tabler.io/icons/icon/settings -->
                <i class="ti ti-settings"></i>
              </a>
            </li>
          </ul>
        </div>
        <div class="card-body">
          <div class="tab-content">
            <div class="tab-pane active show" id="tabs-asesmen-medis" role="tabpanel">
              @include('pemeriksaan.tabs._asesmen_medis')
            </div>
            <div class="tab-pane" id="tabs-asesmen-perawat" role="tabpanel">
              @include('pemeriksaan.tabs._asesmen_keperawatan')
            </div>
            <div class="tab-pane" id="tabs-cppt" role="tabpanel">
              @include('pemeriksaan.tabs._cppt')
            </div>
            <div class="tab-pane" id="tabs-tindakan" role="tabpanel">
              @include('pemeriksaan.tabs._tindakan')
            </div>
            <div class="tab-pane" id="tabs-resep" role="tabpanel">
              @include('pemeriksaan.tabs._resep')
            </div>
            <div class="tab-pane" id="tabs-laborat" role="tabpanel">
              @include('pemeriksaan.tabs._laborat')
            </div>
            <div class="tab-pane" id="tabs-settings-1" role="tabpanel">
              <h4>Settings tab</h4>
              <div>
                Donec ac vitae diam amet vel leo egestas consequat rhoncus in luctus amet, facilisi sit mauris accumsan nibh habitant senectus
              </div>
            </div>
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
    let currentUser = {!! Auth::user() !!};
    let pasien = {!! json_encode($pasien) !!};
    let kunjungan = {!! json_encode($kunjungan) !!}
    let asesmenPerawat = {!! json_encode($asesmenKeperawatan) !!};
    let asesmenMedis = {!! json_encode($asesmenMedis) !!};
    let resep = {!! json_encode($resep) !!}

    document.addEventListener('alpine:init', () => {
      Alpine.data('form', () => ({
        icd10_selected: '',
        form: {
          id: null,
          pasien_id: pasien.id,
          icd10_id: '',
          tanggal_registrasi: '',
          jenis_pembayaran: '',
          jenis_layanan: '',
          dokter_id: '',
          ruangan_id: '',
          created_by: '',
          _method: 'PUT'

        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.registrasi.kunjungan.update', kunjungan.id),
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
              //   sideBySide: true,
              //   calendarWeeks: false,
              viewMode: 'calendar',
              toolbarPlacement: 'bottom',
              //   keepOpen: false,
              //   buttons: {
              //     today: false,
              //     clear: false,
              //     close: false
              //   },
              //   components: {
              //     calendar: true,
              //     date: true,
              //     month: true,
              //     year: true,
              //     decades: true,
              //     clock: true,
              //     hours: true,
              //     minutes: true,
              //     seconds: false,
              //   },
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

          this.id = kunjungan.id;
          this.form.icd10_id = kunjungan.icd10_id;
          this.form.tanggal_registrasi = kunjungan.tanggal_registrasi;
          this.form.jenis_pembayaran = kunjungan.jenis_pembayaran;
          this.form.jenis_layanan = kunjungan.jenis_layanan;
          this.form.dokter_id = kunjungan.dokter_id;
          this.form.ruangan_id = kunjungan.ruangan_id;
          this.form.created_by = kunjungan.created_by;
          this.icd10_selected = `${kunjungan.jenis_penyakit.code} - ${kunjungan.jenis_penyakit.display_en}`;

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
  </script>

  @stack('pemeriksaan-js')
@endpush
