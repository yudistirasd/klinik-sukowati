<div x-data="Laborat" x-init="init()">

  @if (in_array(Auth::user()->role, ['admin', 'dokter', 'loket', 'perawat']))
    <form @submit.prevent="handleSubmit" autocomplete="off" id="cppt">
      <div class="mb-3 row">
        <label class="col-2 col-form-label">Laboratorium</label>
        <div class="col">
          <div class="row">
            <div class="col">
              <select class="form-control" id="provinsi" name="produk_id" :class="{ 'is-invalid': errors.produk_id }">
                <option value=""></option>
              </select>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-2 btn-icon" :disabled="loading" aria-label="Button">
                <span x-show="loading" class="spinner-border spinner-border-sm"></span>
                <i class="ti ti-plus" x-show="!loading"></i>
              </button>
            </div>
          </div>
          <div class="invalid-feedback" x-text="errors.produk_id"></div>
        </div>
      </div>
    </form>
  @endif

  <table id="tindakan-pasien-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm mt-3" style="width: 100%;">
    <thead>
      <tr>
        <th class="text-center">No.</th>
        <th class="text-center">Keterangan</th>
        <th class="text-center">Tarif</th>
        <th class="text-center">Act</th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th colspan="2" class="text-end">Total</th>
        <th id="total"></th>
      </tr>
    </tfoot>
  </table>
</div>
@push('pemeriksaan-js')
  <script>
    const laboratPasienTable = new DataTable('#tindakan-pasien-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.pemeriksaan.get.tindakan', {
        kunjungan_id: kunjungan.id
      }),
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          data: "produk.name",
          width: '70%'
        },
        {
          data: "harga",
          sClass: 'text-end',
        },
        {
          data: 'action',
          name: 'action',
          sClass: 'text-center',
          width: "10%"
        },
      ],
      footerCallback: function(tfoot, data, start, end, display) {
        var api = this.api();
        var json = api.ajax.json();
        $('#total').html(json.total)
      }
    });


    document.addEventListener('alpine:init', () => {
      Alpine.data('Laborat', () => ({
        form: {
          pasien_id: pasien.id,
          kunjungan_id: kunjungan.id,
          produk_id: '',
          tarif: 0
        },
        endPoint: '',
        errors: {},
        loading: false,

        handleSubmit() {
          if (this.form.tarif == 0 && this.form.produk_id == '') {
            return Toast.fire({
              icon: 'warning',
              title: 'Pilih tindakan terlebih dahulu'
            });
          };

          this.loading = true;
          this.errors = {};


          $.ajax({
            url: route('api.pemeriksaan.store.tindakan'),
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

            laboratPasienTable.ajax.reload();

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

        init() {
          let selectProduk = $('select[name=produk_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Laborat",
            searchInputPlaceholder: 'Cari Laborat',
            allowClear: true,
            ajax: {
              url: route('api.master.produk.json', {
                jenis: 'tindakan'
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
                  results: response.data
                }
              },
            },
            templateSelection: function(data, container) {
              // Add custom attributes to the <option> tag for the selected option
              $(data.element).attr('data-tarif', data.tarif);
              return data.text;
            }
          }).on('change', (e) => {
            let target = e.target;
            let value = e.target.value;

            let tarif = $('select[name=produk_id').find(':selected').data('tarif');

            this.form.tarif = tarif;
            this.form.produk_id = value;
          })
        },

        resetForm() {
          this.form = {
            pasien_id: pasien.id,
            kunjungan_id: kunjungan.id,
            produk_id: '',
            tarif: 0
          };
          this.errors = {};
        }
      }))
    })
  </script>
@endpush
