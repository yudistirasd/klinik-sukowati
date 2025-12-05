@extends('layouts.app')

@section('title', 'Rincian Pembelian Obat')
@section('subtitle', 'Farmasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="{{ route('farmasi.pembelian.index') }}" class="btn btn-dark btn-5 btn-icon">
    <div class="ti ti-arrow-left me-1"></div>
  </a>
@endsection

@section('content')
  <div class="row" x-data="form" x-cloak>
    <div class="col-md-12 col-sm-12">
      <!-- Table -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Rincian Pembelian Obat</h3>
          <div class="card-actions">
            @if ($pembelian->insert_stok == 'belum')
              <button type="button" class="btn btn-primary" onclick="insertStok()">
                <i class="ti ti-device-floppy me-1"></i> Tambahkan ke stok
              </button>
            @endif
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-2 col-sm-12">
              <div class="mb-3">
                <label class="form-label">No Pembelian</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $pembelian->nomor }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="mb-3">
                <label class="form-label">Tanggal Pembelian</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $pembelian->tanggal }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="mb-3">
                <label class="form-label">PBF / Suplier</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $pembelian->suplier->name }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="mb-3">
                <label class="form-label">No Faktur</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $pembelian->no_faktur }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="mb-3">
                <label class="form-label">Tgl Faktur</label>
                <input type="text" disabled class="form-control" placeholder="Otomatis dari sistem" autocomplete="off" value="{{ $pembelian->tgl_faktur }}">
              </div>
            </div>
            <div class="col-md-2 col-sm-12">
              <div class="mb-3">
                <label class="form-label">Ditambahkan Ke Stok</label>
                @php
                  $status = $pembelian->insert_stok;
                  $color = $status == 'belum' ? 'bg-orange text-orange-fg' : 'bg-green text-green-fg';
                @endphp
                <span class="badge {{ $color }} text-uppercase">{{ $status }}</span>
              </div>
            </div>
          </div>
          @if ($pembelian->insert_stok == 'belum')
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
                    <div class="row g-2">
                      <div class="col">
                        <input type="text" class="form-control form-control-sm" id="barcode" x-model="form.barcode">
                      </div>
                      <div class="col-auto align-self-center">
                        <span class="form-help bg-warning text-warning-fg" data-bs-toggle="popover" data-bs-placement="top" data-bs-trigger="hover" data-bs-content="Kode Batch / Barcode Obat jika kosong, otomatis dari system">?</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="mb-3">
                    <label class="form-label">Expired Date</label>
                    <input type="date" class="form-control form-control-sm" autocomplete="off" x-model="form.expired_date">
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-3 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Jumlah Kemasan</label>
                    <div class="row g-2">
                      <div class="col-6">
                        <input type="number" min="1" class="form-control form-control-sm" x-on:input="hitungHargaBeli()" autocomplete="off" x-model="form.jumlah_kemasan" :class="{ 'is-invalid': errors.jumlah_kemasan }">
                        <div class="invalid-feedback" x-text="errors.jumlah_kemasan"></div>

                      </div>
                      <div class="col-6">
                        <select class="form-control" id="satuan_kemasan" name="satuan_kemasan" :class="{ 'is-invalid': errors.satuan_kemasan }">
                          <option value=""></option>
                        </select>
                        <div class="invalid-feedback" x-text="errors.satuan_kemasan"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Isi Per Kemasan</label>
                    <input type="number" min="1" class="form-control form-control-sm" id="isi_per_kemasan" x-on:input="hitungHargaBeli()" autocomplete="off" x-model="form.isi_per_kemasan" :class="{ 'is-invalid': errors.isi_per_kemasan }">
                    <div class="invalid-feedback" x-text="errors.isi_per_kemasan"></div>
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Total Stok</label>
                    <input type="text" min="1" disabled class="form-control form-control-sm" autocomplete="off" x-model="qty_view" :class="{ 'is-invalid': errors.qty }">
                    <div class="invalid-feedback" x-text="errors.qty"></div>
                  </div>
                </div>
                <div class="col-md-1 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Sediaan</label>
                    <input type="text" min="1" disabled class="form-control form-control-sm fw-bold" autocomplete="off" x-model="sediaan">
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Harga Beli per kemasan</label>
                    <input type="text" min="1" class="form-control form-control-sm" x-on:input="hitungHargaBeli()" id="harga_beli_kemasan" autocomplete="off" :class="{ 'is-invalid': errors.harga_beli_kemasan }">
                    <div class="invalid-feedback" x-text="errors.harga_beli_kemasan"></div>
                  </div>
                </div>
                <div class="col-md-2 col-sm-12">
                  <div class="mb-3">
                    <label class="form-label">Harga Beli per satuan</label>
                    <input type="text" disabled min="1" class="form-control form-control-sm" autocomplete="off" x-model="harga_beli_satuan_view" :class="{ 'is-invalid': errors.harga_beli_satuan }">
                    <div class="invalid-feedback" x-text="errors.isi_per_kemasan"></div>
                  </div>
                </div>
              </div>

              <div class="row mb-5 mt-3">
                <div class="col-md-4 col-sm-12">
                  <div class="card shadow-lg">
                    <div class="card-header bg-primary text-primary-fg">
                      <h3 class="card-title">Harga Resep</h3>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">Margin (%)</label>
                        <input type="text" disabled class="form-control form-control-sm" x-model="form.margin_resep" autocomplete="off" :class="{ 'is-invalid': errors.margin_resep }">
                        <div class="invalid-feedback" x-text="errors.margin_resep"></div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Harga Jual - per satuan</label>
                        <div class="input-icon mb-3">
                          <input type="text" class="form-control form-control-sm" disabled id="harga_jual_resep" autocomplete="off" :class="{ 'is-invalid': errors.harga_jual_resep }">
                          <span class="input-icon-addon" x-show="isLoadingHitungHargaJual">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                          </span>
                        </div>
                        <div class="invalid-feedback" x-text="errors.harga_jual_resep"></div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Keuntungan - per satuan</label>
                        <input type="text" disabled class="form-control form-control-sm" x-model="keuntungan_resep_view" autocomplete="off" :class="{ 'is-invalid': errors.keuntungan_satuan }">
                        <div class="invalid-feedback" x-text="errors.keuntungan_satuan"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                  <div class="card shadow-lg">
                    <div class="card-header bg-indigo text-indigo-fg">
                      <h3 class="card-title">Harga Obat Bebas</h3>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">Margin (%)</label>
                        <input type="text" disabled class="form-control form-control-sm" x-model="form.margin_bebas" autocomplete="off" :class="{ 'is-invalid': errors.margin_bebas }">
                        <div class="invalid-feedback" x-text="errors.margin_bebas"></div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Harga Jual - per satuan</label>
                        <div class="input-icon mb-3">
                          <input type="text" class="form-control form-control-sm" disabled id="harga_jual_bebas" autocomplete="off" :class="{ 'is-invalid': errors.harga_jual_bebas }">
                          <span class="input-icon-addon" x-show="isLoadingHitungHargaJual">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                          </span>
                        </div>
                        <div class="invalid-feedback" x-text="errors.harga_jual_resep"></div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Keuntungan - per satuan</label>
                        <input type="text" disabled class="form-control form-control-sm" x-model="keuntungan_bebas_view" autocomplete="off" :class="{ 'is-invalid': errors.keuntungan_satuan }">
                        <div class="invalid-feedback" x-text="errors.keuntungan_satuan"></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4 col-sm-12">
                  <div class="card shadow-lg">
                    <div class="card-header bg-cyan text-cyan-fg">
                      <h3 class="card-title">Harga Sesama Apotek</h3>
                    </div>
                    <div class="card-body">
                      <div class="mb-3">
                        <label class="form-label">Margin (%)</label>
                        <input type="text" disabled class="form-control form-control-sm" x-model="form.margin_apotek" autocomplete="off" :class="{ 'is-invalid': errors.margin_apotek }">
                        <div class="invalid-feedback" x-text="errors.margin_apotek"></div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Harga Jual - per satuan</label>
                        <div class="input-icon mb-3">
                          <input type="text" class="form-control form-control-sm" disabled id="harga_jual_apotek" autocomplete="off" :class="{ 'is-invalid': errors.harga_jual_apotek }">
                          <span class="input-icon-addon" x-show="isLoadingHitungHargaJual">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                          </span>
                        </div>
                        <div class="invalid-feedback" x-text="errors.harga_jual_resep"></div>
                      </div>

                      <div class="mb-3">
                        <label class="form-label">Keuntungan - per satuan</label>
                        <input type="text" disabled class="form-control form-control-sm" x-model="keuntungan_apotek_view" autocomplete="off" :class="{ 'is-invalid': errors.keuntungan_satuan }">
                        <div class="invalid-feedback" x-text="errors.keuntungan_satuan"></div>
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
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="pembelian-detail-table">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Uraian</th>
                  <th class="text-center">Harga Beli</th>
                  <th class="text-center">Qty</th>
                  <th class="text-center">Harga Jual</th>
                  <th class="text-center">Keuntungan</th>
                  <th class="text-center">Total</th>
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
    const pembelian = {!! $pembelian !!};
    const hargaJual = @json(config('constant.harga_jual'));

    const table = new DataTable('#pembelian-detail-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.farmasi.pembelian.detail.dt', pembelian.id),
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
          data: 'produk.name',
          name: 'produk.name',
          sClass: 'text-start',
          width: "35%"
        },
        {
          data: 'harga_beli_group',
          name: 'harga_beli_group',
          sClass: 'text-end',
          orderable: false,
          searchable: false,
        },
        {
          data: 'qty_group',
          name: 'qty_group',
          sClass: 'text-end',
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
          data: 'keuntungan_group',
          name: 'keuntungan_group',
          sClass: 'text-end',
          width: "10%",
          orderable: false,
          searchable: false,
        },
        {
          data: 'total',
          name: 'total',
          sClass: 'text-end',
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

    document.addEventListener('alpine:init', () => {
      Alpine.data('form', () => ({
        title: '',
        form: {
          pembelian_id: pembelian.id,
          produk_id: '',
          barcode: '',
          expired_date: '',
          jumlah_kemasan: '',
          satuan_kemasan: '',
          isi_per_kemasan: '',
          qty: '',
          harga_beli_kemasan: '',
          harga_beli_satuan: '',
          harga_jual_resep: '',
          harga_jual_bebas: '',
          harga_jual_apotek: '',
          margin_resep: hargaJual.resep,
          margin_bebas: hargaJual.bebas,
          margin_apotek: hargaJual.apotek,
        },
        sediaan: '',
        mask_harga_beli_kemasan: {},
        mask_harga_jual_resep: {},
        mask_harga_jual_bebas: {},
        mask_harga_jual_apotek: {},
        harga_beli_satuan_view: '',
        qty_view: '',
        keuntungan_resep_view: '',
        keuntungan_bebas_view: '',
        keuntungan_apotek_view: '',
        endPoint: '',
        errors: {},
        loading: false,
        pembelian: {},
        isLoadingHitungHargaJual: false,

        init() {
          this.form.pembelian_id = pembelian.id;

          let selectProduk = $('#obat').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Obat",
            searchInputPlaceholder: 'Cari Obat',
            allowClear: true,
            ajax: {
              url: route('api.master.produk.json', {
                jenis: 'obat'
              }),
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
                    id: item.id,
                    text: `${item.name} ${item.dosis} ${item.satuan} ${item.sediaan}`,
                    dosis: item.dosis,
                    satuan: item.satuan,
                    sediaan: item.sediaan
                  }))
                }
              },
            },
            templateSelection: function(data, container) {
              let json = JSON.stringify({
                dosis: data.dosis,
                satuan: data.satuan,
                sediaan: data.sediaan
              });
              $(data.element).attr('data-json', json);
              return data.text;
            }
          }).on('change', (e) => {
            let target = e.target;
            let value = e.target.value;
            let item = $('#obat').find(':selected').data('json');

            this.sediaan = item?.sediaan;
            this.form.produk_id = value;
          }).on('select2:select', () => {
            $('#barcode').focus();
          })

          let searchResultSatuanKemasan = [];
          $('select[name=satuan_kemasan]').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Satuan",
            searchInputPlaceholder: 'Cari Satuan Kemasan',
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.farmasi.satuan-kemasan.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultSatuanKemasan = response.data;
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

              if (searchResultSatuanKemasan.length > 0) {
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
            this.form.satuan_kemasan = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.satuan-kemasan.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                console.log('Response new tag', response);
              })
            }

            $('#isi_per_kemasan').focus();
          })

          this.mask_harga_beli_kemasan = IMask(document.getElementById('harga_beli_kemasan'), {
            mask: Number,
            scale: 2,
            thousandsSeparator: ',',
            radix: '.',
            padFractionalZeros: true,
            normalizeZeros: true
          });

          this.mask_harga_jual_resep = IMask(document.getElementById('harga_jual_resep'), {
            mask: Number,
            thousandsSeparator: ',',
          });

          this.mask_harga_jual_bebas = IMask(document.getElementById('harga_jual_bebas'), {
            mask: Number,
            thousandsSeparator: ',',
          });

          this.mask_harga_jual_apotek = IMask(document.getElementById('harga_jual_apotek'), {
            mask: Number,
            thousandsSeparator: ',',
          });
        },

        modalControl(action, title, data = null) {
          this.resetForm();
          this.title = title;

          if (action == 'create') {
            delete this.form._method;
            this.endPoint = route('api.farmasi.pembelian.store')
          }

          if (action == 'edit') {
            this.form = {
              ...data,
              _method: 'PUT'
            };

            this.endPoint = route('api.farmasi.pembelian.update', data.id);
          }

          $('#modal-pembelian').modal('show');

        },

        handleSubmit() {

          if (this.form.margin < 0) {
            return Toast.fire({
              icon: 'warning',
              title: 'Margin tidak boleh 0'
            })
          }

          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.farmasi.pembelian.detail.store', pembelian.id),
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

        hitungHargaBeli() {
          this.form.qty = this.form.jumlah_kemasan * this.form.isi_per_kemasan;
          this.qty_view = formatUang(this.form.qty);
          this.form.harga_beli_kemasan = Number(this.mask_harga_beli_kemasan.unmaskedValue);
          this.form.harga_beli_satuan = ceil(this.form.harga_beli_kemasan / this.form.isi_per_kemasan);
          this.harga_beli_satuan_view = formatUang(this.form.harga_beli_satuan);


          if (this.form.harga_beli_satuan) {
            this.isLoadingHitungHargaJual = true;

            setTimeout(() => {
              this.hitungHargaJual();
            }, 700);
          }

        },

        hitungHargaJual() {
          let keuntunganResep = ceil(this.form.harga_beli_satuan * this.form.margin_resep / 100);
          let keuntunganBebas = ceil(this.form.harga_beli_satuan * this.form.margin_bebas / 100);
          let keuntunganApotek = ceil(this.form.harga_beli_satuan * this.form.margin_apotek / 100);

          let hargaJualResep = this.form.harga_beli_satuan + keuntunganResep;
          let hargaJualBebas = this.form.harga_beli_satuan + keuntunganBebas;
          let hargaJualApotek = this.form.harga_beli_satuan + keuntunganApotek;


          this.keuntungan_resep_view = formatUang(keuntunganResep);
          this.keuntungan_bebas_view = formatUang(keuntunganBebas);
          this.keuntungan_apotek_view = formatUang(keuntunganApotek);

          this.form.harga_jual_resep = hargaJualResep;
          this.form.harga_jual_bebas = hargaJualBebas;
          this.form.harga_jual_apotek = hargaJualApotek;

          console.log("Harga jual resep", hargaJualResep)
          console.log("Harga jual bebas", hargaJualBebas)
          console.log("Harga jual apotek", hargaJualApotek);

          console.log("Harga jual resep view", formatUang(hargaJualResep))
          console.log("Harga jual bebas view", formatUang(hargaJualBebas))
          console.log("Harga jual apotek view", formatUang(hargaJualApotek))

          this.mask_harga_jual_resep.value = formatUang(hargaJualResep, 0);
          this.mask_harga_jual_bebas.value = formatUang(hargaJualBebas, 0);
          this.mask_harga_jual_apotek.value = formatUang(hargaJualApotek, 0);


          setTimeout(() => {
            this.isLoadingHitungHargaJual = false
          }, 500)

        },

        resetForm() {
          this.form = {
            pembelian_id: pembelian.id,
            produk_id: '',
            barcode: '',
            expired_date: '',
            jumlah_kemasan: '',
            satuan_kemasan: '',
            isi_per_kemasan: '',
            qty: '',
            harga_beli_kemasan: '',
            harga_beli_satuan: '',
            harga_jual_resep: '',
            harga_jual_bebas: '',
            harga_jual_apotek: '',
            margin_resep: hargaJual.resep,
            margin_bebas: hargaJual.bebas,
            margin_apotek: hargaJual.apotek,
          };

          this.sediaan = '';
          this.harga_beli_satuan_view = '';
          this.keuntungan_resep_view = '';
          this.keuntungan_bebas_view = '';
          this.keuntungan_apotek_view = '';
          this.qty_view = '';
          this.errors = {};

          $('#obat').val(null).trigger('change');
          $('#satuan_kemasan').val(null).trigger('change');
          this.mask_harga_beli_kemasan.value = '';
          this.mask_harga_jual_resep.value = '';
          this.mask_harga_jual_bebas.value = '';
          this.mask_harga_jual_apotek.value = '';
        }
      }))
    })

    const handleModal = (action, title, data = null) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="form"]'));
      alpineComponent.modalControl(action, title, data);
    }

    const insertStok = () => {
      Swal.fire({
        title: "Apakah anda yakin menambahkan data pembelian obat ke stok?",
        html: "Ketika Proses sedang berjalan. Jangan menutup atau berpindah tab/browser hingga selesai.",
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
            url: route('api.farmasi.pembelian.store-stok', pembelian.id),
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
  </script>
@endpush
