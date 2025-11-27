@extends('layouts.app')
@php
  $title = $penjualan->jenis == 'bebas' ? 'Bebas' : 'Sesama Apotek';
@endphp
@section('title', 'Penjualan Obat ' . $title)
@section('subtitle', 'Farmasi')

@section('action-page')
  <a class="btn btn-animate-icon btn-animate-icon-rotate" onclick="handleModalSettingPrinter()" href="javascript:;">
    <i class="ti ti-settings icon icon-start icon-2"></i> Setting Printer
  </a>

  <a class="btn btn-animate-icon" href="{{ route('farmasi.penjualan.index') }}">
    <i class="ti ti-arrow-narrow-left icon icon-start icon-2"></i> Kembali
  </a>
@endsection

@push('css')
  <script type="text/javascript" src="{{ asset('libs/qz/qz-tray.js') }}"></script>
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
@endpush

@section('content')
  <div x-data="Penjualan" x-cloak>
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Penjualan</h3>
        <div class="card-actions">
          <span>
            <strong>Printer : </strong>
            <span class="badge text-uppercase" :class="statusQz == 'disconnected' ? 'bg-red text-red-fg' : 'bg-green text-green-fg'">
              <span x-text="statusQz == 'connected' ? 'QZ Connected' : 'QZ Disconnected'"></span>
            </span>
            (<span x-text="currentPrinter ?? 'Belum setting printer'"></span>)
          </span>
        </div>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleSubmit()">
          @if ($penjualan->status == 'belum')
            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label for="" class="form-label">No Penjualan</label>
                  <input type="text" disabled value="{{ $penjualan->nomor }}" id="" class="form-control">
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label for="" class="form-label">Tgl Penjualan</label>
                  <input type="text" disabled value="{{ $penjualan->tanggal }}" id="" class="form-control">
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label for="" class="form-label">Petugas</label>
                  <input type="text" disabled value="{{ $penjualan->user->name }}" id="" class="form-control">
                </div>
              </div>

              <div class="col-md-4 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Obat</label>
                  <select class="form-control" id="obat" name="obat_id" :class="{ 'is-invalid': errors.produk_id }" style="width: 100%">
                    <option value=""></option>
                  </select>
                  <div class="invalid-feedback" x-text="errors.produk_id"></div>
                </div>
              </div>
              <div class="col-md-3 col-sm-12">
                <div class="mb-3">
                  <label class="form-label">Jumlah Obat</label>
                  <div class="row g-2">
                    <div class="col">
                      <div class="input-group mb-2">
                        <input type="number" id="qty" class="form-control" autocomplete="off" x-model="form.qty" :class="{ 'is-invalid': errors.qty }">
                        <span class="input-group-text" x-text="sediaan ? sediaan : '-'"></span>
                      </div>
                      <div class="invalid-feedback d-block" x-text="errors.qty"></div>
                    </div>
                    <div class="col-auto">
                      <button type="submit" href="#" class="btn btn-2 btn-icon" :disabled="loading" aria-label="Button">
                        <i class="ti ti-plus" x-show="!loading"></i>
                        <div class="spinner-border spinner-border-sm text-secondary" x-show="loading" role="status"></div>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endif
          <div class="table-responsive">
            <table class="table table-hover table-sm" id="penjualan-detail-table">
              <thead>
                <tr>
                  <th class="text-center">#</th>
                  <th class="text-center">Obat</th>
                  <th class="text-center">Barcode / Batch</th>
                  <th class="text-center">ED</th>
                  <th class="text-center">Jumlah</th>
                  <th class="text-center">Harga Jual</th>
                  <th class="text-center">Total</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </form>
      </div>
    </div>

    <div class="modal modal-blur fade" id="modal-bayar" tabindex="-1" role="dialog" aria-hidden="true" data-bs-backdrop="static">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Bayar Tagihan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form @submit.prevent="handleSubmitBayarTagihan" autocomplete="off">
            <div class="modal-body">
              <div class="row">
                <div class="col">
                  <div class="mb-3">
                    <label class="form-label">Total Tagihan</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="total_tagihan_view">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label class="form-label">Diskon</label>
                    <input type="text" class="form-control" autocomplete="off" id="diskon">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label class="form-label">Total Bayar</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="total_bayar_view">
                  </div>
                </div>
                <div class="col">
                  <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <div class="row row-cols-2">
                      @foreach (metodePembayaran() as $item)
                        <div class="col">
                          <label class="form-check fs-5">
                            <input class="form-check-input" x-model="formBayarTagihan.metode_pembayaran" :class="{ 'is-invalid': errors.metode_pembayaran }" type="radio" value="{{ $item }}">
                            <span class="form-check-label fw-bolder">{{ $item }}</span>
                          </label>
                        </div>
                      @endforeach
                      <div class="invalid-feedback d-block" x-text="errors.metode_pembayaran"></div>
                    </div>
                  </div>
                </div>
                <div class="col" x-show="formBayarTagihan.metode_pembayaran == 'Tunai'">
                  <div class="mb-3">
                    <label class="form-label">Uang Tunai</label>
                    <input type="text" class="form-control" id="cash" autocomplete="off">
                  </div>
                </div>
                <div class="col" x-show="formBayarTagihan.metode_pembayaran == 'Tunai'">
                  <div class="mb-3">
                    <label class="form-label">Kembalian</label>
                    <input type="text" disabled class="form-control" autocomplete="off" x-model="kembalian_view">
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary ms-auto" x-bind:disabled="loading">
                <span x-show="loading" class="spinner-border spinner-border-sm me-2"></span>
                Bayar
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
    const penjualan = @json($penjualan);
    const resepObat = function() {
      container = $('#penjualan-detail-table tbody');
      $.ajax({
        url: route('api.farmasi.penjualan.detail.dt', {
          penjualan: penjualan.id,
        }),
        method: 'GET',
      }).done((response) => {
        container.html(response.data);
      })
    };
    document.addEventListener('alpine:init', () => {
      Alpine.data('Penjualan', () => ({
        currentPrinter: null,
        statusQz: 'disconnect',
        printers: [],
        errors: {},
        form: {
          produk_id: null,
          qty: null,
          jenis: penjualan.jenis
        },
        formBayarTagihan: {
          total_tagihan: null,
          diskon: null,
          total_bayar: null,
          metode_pembayaran: 'Tunai',
          cash: null,
          kembalian: null
        },
        loading: false,
        sediaan: '',
        harga_jual: null,
        selectProduk: {},
        total_tagihan_view: null,
        total_bayar_view: null,
        kembalian_view: null,
        mask_diskon_view: {},
        mask_uang_tunai_view: {},
        init() {
          qz.security.setCertificatePromise(function(resolve, reject) {
            //Preferred method - from server
            fetch("{{ asset('libs/qz/override.crt') }}", {
                cache: 'no-store',
                headers: {
                  'Content-Type': 'text/plain'
                }
              })
              .then(function(data) {
                data.ok ? resolve(data.text()) : reject(data.text());
              });
          });

          qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
          qz.security.setSignaturePromise(function(toSign) {
            return function(resolve, reject) {
              $.post("/libs/qz/sign-message.php", {
                request: toSign
              }).then(resolve, reject);
            };
          });

          qz.websocket.connect().then(() => {
            $('.btn-print').attr('disabled', false);
            this.statusQz = 'connected';
            this.findPrinters();
            this.currentPrinter = this.getPrinter();
          });


          this.selectProduk = $('#obat').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Obat",
            searchInputPlaceholder: 'Cari Obat',
            allowClear: true,
            ajax: {
              url: route('api.farmasi.stok-obat.select2-bebas'),
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
                    text: `${item.name} ${item.dosis} ${item.satuan} ${item.sediaan} - ${item.harga_jual_bebas_view}`,
                    dosis: item.dosis,
                    satuan: item.satuan,
                    sediaan: item.sediaan,
                    harga_jual: item.harga_jual_bebas
                  }))
                }
              },
            },
            templateSelection: function(data, container) {
              let json = JSON.stringify({
                dosis: data.dosis,
                satuan: data.satuan,
                sediaan: data.sediaan,
                harga_jual: data.harga_jual
              });
              $(data.element).attr('data-json', json);
              return data.text;
            }
          }).on('change', (e) => {
            let target = e.target;
            let value = e.target.value;
            let item = $('#obat').find(':selected').data('json');

            this.sediaan = item?.sediaan;
            this.satuan = item?.satuan;
            this.dosis = item?.dosis;
            this.harga_jual = item?.harga_jual;
            this.form.produk_id = value;
          }).off('select2:select').on('select2:select', () => {
            $('#qty').focus()
          });

          resepObat();
        },

        findPrinters() {
          qz.printers.find().then((data) => {

            this.printers = data;

          }).catch(function(e) {
            console.log(e);
            Swal.fire({
              icon: 'error',
              title: 'Gagal mencari printer',
              message: e
            })
          });
        },
        getPrinter() {
          return localStorage.getItem('print-selected');
        },

        setPrinter(printer) {
          localStorage.setItem('print-selected', printer);
        },

        modalSettingPrinter() {
          let printerOptions = "<option value=''>-- Pilih Printer --</option>";

          this.printers.forEach(printer => {
            printerOptions += `<option value="${printer}" ${this.currentPrinter == printer ? 'selected' : ''}>${printer}</option>`;
          });


          Swal.fire({
            title: 'Setting Printer',
            html: `
                <div style="text-align:left">
                <select id="list-printer" class="form-select">
                    ${printerOptions}
                </select>
                </div>
            `,
            showCancelButton: true, // tombol Batal
            cancelButtonText: 'Batal',

            showDenyButton: true, // kita pakai denyButton sebagai 'Print Test'
            denyButtonText: 'Print Test',

            showConfirmButton: true, // tombol Simpan
            confirmButtonText: 'Simpan',

            didOpen: () => {
              const printer = this.getPrinter();

              if (!printer) {
                // Initially disable "Print Test"
                const denyBtn = Swal.getDenyButton();
                denyBtn.setAttribute("disabled", "disabled");
                denyBtn.style.opacity = "0.5";

                // Enable "Print Test" only when printer selected
                const printerSelect = document.getElementById('list-printer');
                printerSelect.addEventListener('change', () => {
                  if (printerSelect.value) {
                    denyBtn.removeAttribute("disabled");
                    denyBtn.style.opacity = "1";
                  } else {
                    denyBtn.setAttribute("disabled", "disabled");
                    denyBtn.style.opacity = "0.5";
                  }
                });
              }
            },

            preConfirm: () => {
              const printer = document.getElementById('list-printer').value;
              if (!printer) {
                Swal.showValidationMessage('Silakan pilih printer');
                return false;
              }
              return {
                printer,
                action: 'save'
              };
            },

            preDeny: () => {
              const printer = this.getPrinter();
              if (!printer) {
                Swal.showValidationMessage('Silakan pilih & simpan printer, baru print test');
                return false;
              }
              return {
                printer,
                action: 'test'
              };
            }
          }).then((result) => {

            if (result.isDismissed) {
              // Click Batal
              console.log("Dibatalkan");
              return;
            }

            if (result.isConfirmed) {
              this.setPrinter(result.value.printer);

              $.get(route('api.cetak.penjualan.print-test'), (response) => {
                this.doPrint(response.data);
              })
            }

            if (result.isDenied) {
              // Klik PRINT TEST
              $.get(route('api.cetak.penjualan.print-test'), (response) => {
                this.doPrint(response.data);
              })

              // ---- contoh panggil print test QZ ----
              // printTestQZ(result.value.printer);
            }

          });

        },

        doPrint(raw) {
          if (!this.currentPrinter) {
            return Swal.fire({
              icon: 'error',
              title: 'Printer belum disetting'
            });
          }
          var printData = [{
            type: 'raw',
            format: 'command',
            flavor: 'base64',
            data: raw
          }];
          let cfg = this.buildConfigPrinter();
          qz.print(cfg, printData).catch((e) => {
            console.error('Error when printing : ', e);
            Toast.fire({
              icon: 'error',
              message: 'Gagal print'
            })
          });
        },

        buildConfigPrinter() {
          let printer = this.getPrinter();
          return qz.configs.create(printer);
        },

        handleSubmit() {

          if (!this.harga_jual || parseInt(this.harga_jual) == 0) {
            return Toast.fire({
              icon: 'warning',
              title: 'Harga jual belum disetting'
            });
          }

          this.loading = true;
          this.errors = {};

          $.ajax({
            url: route('api.farmasi.penjualan.detail.store', penjualan.id),
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
            resepObat();
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
          this.form.produk_id = null;
          this.form.qty = null;
          this.harga_jual = null;
          this.sediaan = null;
          this.selectProduk.val(null).trigger('change');
        },

        modalControlBayarTagihan(row) {
          console.log(row);
          this.resetFormBayarTagihan();

          this.formBayarTagihan.asal_resep = row.asal_resep;

          this.formBayarTagihan.total_tagihan = row.total_tagihan;
          this.formBayarTagihan.total_bayar = row.total_tagihan;
          this.total_bayar_view = formatUang(row.total_tagihan);

          this.mask_diskon_view = IMask(document.getElementById('diskon'), {
            mask: Number,
            scale: 0,
            thousandsSeparator: ',',
          }).on('accept', () => {
            this.hitungTotalBayar();
          });


          this.mask_uang_tunai_view = IMask(document.getElementById('cash'), {
            mask: Number,
            thousandsSeparator: ',',
          }).on('accept', () => {
            this.hitungKembalian();
          });

          // view rupiah
          this.total_tagihan_view = row.total_tagihan_view;

          this.endPointBayarTagihan = route('api.farmasi.resep-pasien.bayar-tagihan', {
            resep: row.id
          });

          $('#modal-bayar').modal('show');
        },

        resetFormBayarTagihan() {
          this.formBayarTagihan = {
            total_tagihan: null,
            diskon: null,
            total_bayar: null,
            metode_pembayaran: 'Tunai',
            cash: null,
            kembalian: null
          };

          this.total_bayar_view = '';
          this.total_tagihan_view = '';
          this.kembalian_view = '';
          this.mask_diskon_view.value = '';
          this.mask_uang_tunai_view.value = '';
        },

        hitungTotalBayar() {
          let diskon = Number(this.mask_diskon_view.unmaskedValue || 0)
          let totalBayar = Number(this.formBayarTagihan.total_tagihan - diskon || 0);

          this.formBayarTagihan.diskon = diskon;
          this.formBayarTagihan.total_bayar = totalBayar;
          this.total_bayar_view = formatUang(totalBayar);
        },

        hitungKembalian() {
          let cash = Number(this.mask_uang_tunai_view.unmaskedValue || 0);
          let kembalian = cash - this.formBayarTagihan.total_bayar;

          if (kembalian < 0) {
            kembalian = 0;
          }

          this.formBayarTagihan.cash = cash;
          this.formBayarTagihan.kembalian = kembalian;
          this.kembalian_view = formatUang(kembalian);
        },

        handleSubmitBayarTagihan() {
          this.loading = true;
          this.errors = {};


          $.ajax({
            url: this.endPointBayarTagihan,
            method: 'POST',
            dataType: 'json',
            data: this.formBayarTagihan,
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

            this.resetFormBayarTagihan();

            $('#modal-bayar').modal('hide');

            resepObat();

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
        }
      }))
    });

    const handleModalSettingPrinter = () => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="Penjualan"]'));
      alpineComponent.modalSettingPrinter();
    }

    const handleBayarTagihan = (data) => {
      const alpineComponent = Alpine.$data(document.querySelector('[x-data="Resep"]'));
      alpineComponent.modalControlBayarTagihan(data);
    }
  </script>
@endpush
