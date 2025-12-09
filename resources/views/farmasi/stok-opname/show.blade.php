@extends('layouts.app')

@section('title', 'Rincian Stok Opname')
@section('subtitle', 'Farmasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="{{ route('farmasi.stok-opname.index') }}" class="btn btn-dark btn-5 btn-icon">
    <div class="ti ti-arrow-left me-1"></div>
  </a>
@endsection

@section('content')
  <div class="row" x-data="form" x-cloak>
    <div class="col-md-12 col-sm-12">
      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Rincian Stok Opname Obat</h3>
          <div class="card-actions">
            @if ($stokOpname->status == 'process')
              <button type="button" class="btn btn-sm btn-primary" onclick="selesai()">
                <i class="ti ti-device-floppy me-1"></i> Selesai
              </button>
            @endif
          </div>
        </div>
        <div class="card-body">
          <div class="alert alert-warning alert-dismissible" role="alert">
            <div class="alert-icon">
              <i class="ti ti-alert-triangle"></i>
            </div>
            <div>
              <h4 class="alert-heading">Mohon diperhatikan guna menghindari stok bermasalah !</h4>
              <div class="alert-description">
                <p>
                  - Pastikan <b>tidak ada transaksi (pembelian, penjualan, validasi / entri resep oleh farmasi)</b> ketika stok opname.<br>
                  - Obat yang sudah ditambahkan ke stok opname, <b>otomatis menyesuaikan stok</b>.<br>
                  - Jika sudah selesai melakukan input obat, harap klik selesai.<br>
                </p>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-3">
              <div class="mb-3">
                <label class="form-label">No Stok Opname</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $stokOpname->nomor }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="mb-3">
                <label class="form-label">Tanggal Stok Opname</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $stokOpname->tanggal }}">
              </div>

            </div>
            <div class="col-md-3">
              <div class="mb-3">
                <label class="form-label">Dibuat Oleh</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $stokOpname->user->name }}">
              </div>
            </div>
            <div class="col-md-3">
              <div class="mb-3">
                <label class="form-label">Status</label>
                @php
                  $status = $stokOpname->status;
                  $color = $status == 'process' ? 'bg-orange text-orange-fg' : 'bg-green text-green-fg';
                @endphp
                <span class="badge {{ $color }} text-uppercase">{{ $status }}</span>
              </div>
            </div>
          </div>
          @if ($stokOpname->status == 'process')
            <form @submit.prevent="handleSubmit" autocomplete="off">
              <div class="row">
                <div class="col-md-8 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label required">Obat</label>
                    <select class="form-control" id="obat" name="obat_id" :class="{ 'is-invalid': errors.produk_id }">
                      <option value=""></option>
                    </select>
                    <div class="invalid-feedback" x-text="errors.produk_id"></div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="mb-3">
                    <label class="form-label">Kode Batch / Barcode</label>
                    <input type="text" disabled class="form-control form-control-sm" autocomplete="off" x-model="form.barcode">
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="mb-3">
                    <label class="form-label">Expired Date</label>
                    <input type="text" disabled class="form-control form-control-sm" autocomplete="off" x-model="form.expired_date">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="text" disabled min="1" class="form-control form-control-sm" id="isi_per_kemasan" autocomplete="off" x-model="form.harga_beli">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Harga Jual Resep</label>
                    <input type="text" min="1" disabled class="form-control form-control-sm" x-model="form.harga_jual_resep" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Harga Jual Bebas</label>
                    <input type="text" min="1" disabled class="form-control form-control-sm fw-bold" x-model="form.harga_jual_bebas" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Harga Jual Apotek</label>
                    <input type="text" min="1" disabled class="form-control form-control-sm fw-bold" x-model="form.harga_jual_apotek" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Qty System</label>
                    <input type="text" min="1" disabled class="form-control form-control-sm fw-bold" x-model="form.qty_system" autocomplete="off">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Qty Real</label>
                    <input type="number" class="form-control form-control-sm fw-bold" x-model="form.qty_real" autocomplete="off" :class="{ 'is-invalid': errors.qty_real }">
                    <div class="invalid-feedback" x-text="errors.qty_real"></div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Alasan</label>
                    <select x-model="form.alasan" id="" class="form-select form-select-sm" :class="{ 'is-invalid': errors.alasan }">
                      <option value=""></option>
                      <option value="expired">Expired</option>
                      <option value="hilang">Hilang</option>
                      <option value="rusak">Rusak</option>
                      <option value="sisa_racikan">Sisa Racikan</option>
                      <option value="lainnya">Lainnya</option>
                    </select>
                    <div class="invalid-feedback" x-text="errors.alasan"></div>
                  </div>
                </div>
                <div class="col-md-6 col-sm-12" x-show="form.alasan == 'lainnya'">
                  <div class="mb-3">
                    <label class="form-label">Keterangan Alasan Lainnya : </label>
                    <textarea class="form-control" data-bs-toggle="autosize" x-model="form.alasan_lainnya" rows='1' placeholder="Type something…" :class="{ 'is-invalid': errors.alasan_lainnya }"></textarea>
                    <div class="invalid-feedback" x-text="errors.alasan_lainnya"></div>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Di entry oleh</label>
                    <input type="text" disabled class="form-control form-control-sm" autocomplete="off" x-model="form.created_by">
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
          <div class="badges-list mb-3">
            <span>Keterangan : </span>

            <span class="badge bg-default text-default-fg">
              <i class='ti ti-letter-r'></i>-> Harga Jual Resep
            </span>
            <span class="badge bg-default text-default-fg">
              <i class='ti ti-letter-b'></i>-> Harga Jual Bebas
            </span>
            <span class="badge bg-default text-default-fg">
              <i class='ti ti-letter-a'></i>-> Harga Jual Apotek
            </span>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-hover" id="stok-opname-detail-table">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Uraian</th>
                  <th class="text-center">Barcode</th>
                  <th class="text-center">Expired <br> Date</th>
                  <th class="text-center">Harga Beli</th>
                  <th class="text-center">Harga Jual</th>
                  <th class="text-center">Qty <br> System</th>
                  <th class="text-center">Qty <br> Real</th>
                  <th class="text-center">Selisih</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
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
    const stokOpname = {!! $stokOpname !!};

    const table = new DataTable('#stok-opname-detail-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.farmasi.stok-opname.detail.dt', stokOpname.id),
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
          width: '3%'
        },
        {
          data: 'obat',
          name: 'obat',
          sClass: 'text-start',
          width: "35%"
        },
        {
          data: 'barcode',
          name: 'barcode',
          sClass: 'text-center',
          orderable: false,
          searchable: false,
        },
        {
          data: 'expired_date',
          name: 'expired_date',
          sClass: 'text-center',
          orderable: false,
          searchable: false,
        },
        {
          data: 'harga_beli',
          name: 'harga_beli',
          sClass: 'text-end',
          width: "10%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'harga_jual_group',
          name: 'harga_jual_group',
          sClass: 'text-end',
          width: "10%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'qty_system',
          name: 'qty_system',
          sClass: 'text-center',
          width: "10%",
          orderable: true,
          searchable: false,
        },
        {
          data: 'qty_real',
          name: 'qty_real',
          sClass: 'text-center',
          width: "10%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'qty_selisih',
          name: 'qty_selisih',
          sClass: 'text-center',
          width: "10%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "5%"
        },
      ]
    });

    const selesai = () => {
      Swal.fire({
        title: "Apakah anda yakin menyelesaikan stok opname ?",
        html: "Stok Opname yang sudah diselesaikan tidak dapat diubah kembali!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak, batalkan",
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
        preConfirm: async (login) => {
          return $.ajax({
            url: route('api.farmasi.stok-opname.toggle-status', stokOpname.id),
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          }).done((response) => {
            Swal.fire({
              icon: 'success',
              title: 'Sukses !',
              text: response.message
            }).then(() => {
              window.location.reload();
            });


          }).fail((error) => {
            let response = error.responseJSON;

            Swal.fire({
              icon: 'error',
              title: 'Terjadi kesalahan !',
              message: response.message
            })
          })
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then(async (result) => {
        if (!result.value) {
          Swal.fire({
            icon: 'info',
            title: 'Aksi dibatalkan !',
          })
        }
      });
    }

    document.addEventListener('alpine:init', () => {
      Alpine.data('form', () => ({
        title: '',
        form: {
          stok_opname_id: stokOpname.id,
          produk_id: '',
          barcode: '',
          expired_date: '',
          qty_system: '',
          qty_real: '',
          harga_beli: '',
          harga_jual_resep: '',
          harga_jual_bebas: '',
          harga_jual_apotek: '',
          alasan: '',
          alasan_lainnya: '',
          created_by: '{{ Auth::user()->name }}'
        },
        sediaan: '',
        endPoint: '',
        errors: {},
        loading: false,
        stokOpname: {},
        isLoadingHitungHargaJual: false,

        init() {

          this.form.pembelian_id = stokOpname.id;

          let selectProduk = $('#obat').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Obat",
            searchInputPlaceholder: 'Cari Obat',
            allowClear: true,
            ajax: {
              url: route('api.farmasi.stok-obat.select2-stok-opname'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }

                // Query parameters will be ?search=[term]&type=public
                return query;
              },
              processResults: function(response) {
                return {
                  results: response.data.map(item => ({
                    id: item.produk_id,
                    text: `${item.barcode} - ${item.obat} - ${item.expired_date} - ${item.ready} ${item.sediaan}`,
                    ...item
                  }))
                }
              },
            },
            templateSelection: function(data, container) {
              if (!data.id) {
                return data.text;
              }

              let dataToStore = {
                ...data
              };

              // 3. PENTING: Hapus properti 'element' yang menyebabkan circular error
              delete dataToStore.element;
              delete dataToStore._resultId; // Opsional: hapus ID internal select2
              delete dataToStore.disabled; // Opsional
              delete dataToStore.selected; // Opsional

              let json = JSON.stringify(dataToStore);
              $(data.element).attr('data-json', json);
              return data.text;
            }
          }).on('change', (e) => {
            let target = e.target;
            let value = e.target.value;
            let item = $('#obat').find(':selected').data('json');
            this.form.harga_beli = formatUang(item?.harga_beli);
            this.form.harga_jual_resep = formatUang(item?.harga_jual_resep);
            this.form.harga_jual_bebas = formatUang(item?.harga_jual_bebas);
            this.form.harga_jual_apotek = formatUang(item?.harga_jual_apotek);
            this.form.qty_system = parseInt(item?.ready);

            console.log(value);
            this.form.barcode = item?.barcode;
            this.form.expired_date = item?.expired_date;
            this.form.produk_id = value;
          }).on('select2:select', () => {
            // $('#barcode').focus();
          })
        },


        handleSubmit() {


          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.farmasi.stok-opname.detail.store', stokOpname.id),
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

            this.resetForm();

            table.ajax.reload();
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
            pembelian_id: stokOpname.id,
            produk_id: '',
            barcode: '',
            expired_date: '',
            qty_system: '',
            qty_real: 0,
            harga_beli: '',
            harga_jual_resep: '',
            harga_jual_bebas: '',
            harga_jual_apotek: '',
            alasan: '',
            alasan_lainnya: '',
            created_by: '{{ Auth::user()->name }}'
          };

          this.errors = {};
        }
      }))
    })
  </script>
@endpush
