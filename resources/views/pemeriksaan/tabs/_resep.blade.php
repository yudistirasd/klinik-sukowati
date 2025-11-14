<div x-data="Resep">

  @if (Auth::user()->hasRole('dokter'))
    <form @submit.prevent="handleSubmit" autocomplete="off" id="resep" x-show="isUserDokter">
      <div class="row">
        <div class="col-md-4 col-sm-12">
          <div class="mb-3">
            <label class="form-label">Tgl Resep</label>
            <input type="text" class="form-control" autocomplete="off" id="tanggal" x-model="form.tanggal">
          </div>
        </div>
        <div class="col-md-4 col-sm-12">
          <div class="mb-3">
            <label class="form-label">No Resep</label>
            <input type="number" disabled class="form-control" autocomplete="off" placeholder="Otomatis dari sistem" x-model="form.nomor" :class="{ 'is-invalid': errors.nomor }">
            <div class="invalid-feedback" x-text="errors.nomor"></div>
          </div>
        </div>
        <div class="col-md-4 col-sm-12">
          <div class="mb-3">
            <label class="form-label">DPJP</label>
            <input type="text" disabled class="form-control" autocomplete="off" x-model="dokter">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="mb-3">
            <label class="form-label">Obat</label>
            <select class="form-control" id="obat" name="obat_id" :class="{ 'is-invalid': errors.produk_id }">
              <option value=""></option>
            </select>
            <div class="invalid-feedback" x-text="errors.produk_id"></div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-2 col-sm-12">
          <div class="mb-3">
            <label class="form-label">Signa</label>
            <input type="text" id="frekuensi" class="form-control" x-on:input="hitungJumlahObat" autocomplete="off" :class="{ 'is-invalid': errors.signa }">
            <div class="invalid-feedback" x-text="errors.signa"></div>
          </div>
        </div>
        <div class="col-md-2 col-sm-12">
          <div class="mb-3">
            <label class="form-label">Lama Hari</label>
            <input type="number" min="1" class="form-control" x-on:input="hitungJumlahObat" autocomplete="off" x-model="form.lama_hari" :class="{ 'is-invalid': errors.lama_hari }">
            <div class="invalid-feedback" x-text="errors.lama_hari"></div>
          </div>
        </div>
        <div class="col-md-2 col-sm-12">
          <div class="mb-3">
            <label class="form-label">Jumlah Obat</label>
            <input type="number" disabled class="form-control" autocomplete="off" x-model="form.qty" :class="{ 'is-invalid': errors.qty }">
            <div class="invalid-feedback" x-text="errors.qty"></div>
          </div>
        </div>
        <div class="col-md-3 col-sm-12">
          <div class="mb-3">
            <label class="form-label">Takaran</label>
            <select class="form-control" id="takaran_id" :class="{ 'is-invalid': errors.takaran_id }">
              <option value=""></option>
            </select>
            <div class="invalid-feedback" x-text="errors.takaran_id"></div>
          </div>
        </div>
        <div class="col-md-3 col-sm-12">
          <div class="mb-3">
            <label class="form-label">Aturan Pakai</label>
            <select class="form-control" id="aturan_pakai_id" :class="{ 'is-invalid': errors.aturan_pakai_id }">
              <option value=""></option>
            </select>
            <div class="invalid-feedback" x-text="errors.aturan_pakai_id"></div>
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

  <table id="resep-pasien-table" aria-label="diagnosa" class="table table-bordered table-striped table-sm mt-3" style="width: 100%;">
    <thead>
      <tr>
        <th class="text-center">No.</th>
        <th class="text-center">Nomor Resep</th>
        <th class="text-center">Obat</th>
        <th class="text-center">Signa</th>
        <th class="text-center">Lama Hari</th>
        <th class="text-center">Jumlah Obat</th>
        <th class="text-center">Takaran</th>
        <th class="text-center">Aturan Pakai</th>
        <th class="text-center">Act</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
@push('pemeriksaan-js')
  <script>
    const resepObat = new DataTable('#resep-pasien-table', {
      dom: 'Brti',
      processing: true,
      serverSide: true,
      autoWidth: false,
      destroy: true,
      ajax: route('api.pemeriksaan.get.resep', {
        kunjungan_id: kunjungan.id
      }),
      pageLength: 50,
      columns: [{
          data: 'DT_RowIndex',
          name: 'DT_RowIndex',
          orderable: false,
          searchable: false,
          sClass: 'text-center',
          width: '5%'
        },
        {
          data: "nomor",
          sClass: 'text-center',
        },
        {
          data: "obat",
        },
        {
          data: "signa",
          sClass: 'text-center',
        },
        {
          data: "lama_hari",
          sClass: 'text-center',

        },
        {
          data: "qty",
          sClass: 'text-center',

        },
        {
          data: "takaran",
          sClass: 'text-center',
        },
        {
          data: "aturan_pakai",
          sClass: 'text-center',
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
      Alpine.data('Resep', () => ({
        datePicker: {},
        dokter: '',
        mask: {},
        form: {
          id: '',
          nomor: '',
          tanggal: kunjungan.tanggal_registrasi,
          pasien_id: pasien.id,
          kunjungan_id: kunjungan.id,
          dokter_id: kunjungan.dokter_id,
          produk_id: '',
          signa: '',
          unit_dosis: '',
          frekuensi: '',
          lama_hari: '',
          qty: 0,
          takaran_id: '',
          aturan_pakai_id: ''
        },
        endPoint: '',
        errors: {},
        loading: false,
        isUserDokter: currentUser.role == 'dokter',

        handleSubmit() {

          this.loading = true;
          this.errors = {};


          $.ajax({
            url: route('api.pemeriksaan.store.resep'),
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

            this.form.id = response.data.id;
            this.form.nomor = response.data.nomor;
            this.datePicker.dates.setValue(new tempusDominus.DateTime(response.data.tanggal));

            resepObat.ajax.reload();

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
          this.dokter = kunjungan.dokter.name;

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


            this.satuan = item?.satuan;
            this.dosis = item?.dosis;
            this.form.produk_id = value;
          }).on('select2:select', () => {
            $('#frekuensi').focus();
          })

          let searchResultsTakaran = [];
          let selectTakaran = $('#takaran_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Takaran",
            searchInputPlaceholder: 'Cari Takaran',
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.farmasi.takaran.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultsTakaran = response.data;
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

              if (searchResultsTakaran.length > 0) {
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
            this.form.takaran_id = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.takaran.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                // ✅ Update option dengan ID dari backend
                const newId = response.data.id; // misal backend return {data: {id: 123, name: "mg"}}

                // Update option yang baru dibuat dengan ID asli
                const $option = $('#takaran_id option[value="' + e.params.data.text + '"]');
                $option.val(newId); // Ganti value dari text ke ID

                // Update form value juga
                this.form.takaran_id = newId;

                // Trigger change agar select2 update
                $('#takaran_id').val(newId).trigger('change');
              })
            }

            $('#aturan_pakai_id').select2('open');

          })

          let searchResultsAturanPakai = [];
          let selectAturanPakai = $('#aturan_pakai_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Pilih Aturan Pakai",
            searchInputPlaceholder: 'Cari Aturan Pakai',
            allowClear: true,
            tags: true,
            ajax: {
              url: route('api.master.farmasi.aturan-pakai.get'),
              data: function(params) {
                var query = {
                  keyword: params.term,
                }
                return query;
              },
              processResults: function(response) {
                searchResultsAturanPakai = response.data;
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

              if (searchResultsAturanPakai.length > 0) {
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
            this.form.aturan_pakai_id = value;
          }).off('select2:select').on('select2:select', (e) => {
            // handle new option
            if (e.params.data.newTag) {
              $.ajax({
                url: route('api.master.farmasi.aturan-pakai.store'),
                method: 'POST',
                data: {
                  name: e.params.data.text
                },
                dataType: 'json'
              }).done((response) => {
                console.log('Response new tag', response);

                // ✅ Update option dengan ID dari backend
                const newId = response.data.id; // misal backend return {data: {id: 123, name: "mg"}}

                // Update option yang baru dibuat dengan ID asli
                const $option = $('#aturan_pakai_id option[value="' + e.params.data.text + '"]');
                $option.val(newId); // Ganti value dari text ke ID

                // Update form value juga
                this.form.aturan_pakai_id = newId;

                // Trigger change agar select2 update
                $('#aturan_pakai_id').val(newId).trigger('change');
              })
            }
          })

          const input = document.getElementById('frekuensi');
          this.mask = IMask(input, {
            mask: 'num1 X num2',
            lazy: false,
            placeholderChar: '_',
            blocks: {
              num1: {
                mask: Number,
                scale: 0,
                min: 0,
                max: 9
              },
              num2: {
                mask: Number,
                scale: 1,
                radix: '.',
                mapToRadix: ['.'],
                min: 0,
                max: 9.9,
                normalizeZeros: true,
                padFractionalZeros: true
              }
            }
          });

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

          if (resep) {
            this.form.tanggal = resep.tanggal;
            this.form.nomor = resep.nomor;
          } else {
            this.form.nomor = '';
            this.form.tanggal = kunjungan.tanggal_registrasi;
          }
          this.datePicker.dates.setValue(new tempusDominus.DateTime(this.form.tanggal));
        },

        hitungJumlahObat() {
          let signa = this.mask.value;

          const [freq, dose] = signa.split('X');

          let jumlahObat = 0;

          if (this.form.lama_hari) {
            jumlahObat = freq * dose * this.form.lama_hari;
          }

          this.form.signa = signa;
          this.form.frekuensi = freq;
          this.form.unit_dosis = dose;
          this.form.qty = jumlahObat;
        },

        resetForm() {
          this.form = {
            id: '',
            nomor: '',
            tanggal: '',
            pasien_id: pasien.id,
            kunjungan_id: kunjungan.id,
            dokter_id: kunjungan.dokter_id,
            produk_id: '',
            signa: '',
            unit_dosis: '',
            frekuensi: '',
            lama_hari: '',
            qty: 0,
            takaran_id: '',
            aturan_pakai_id: ''
          };
          this.errors = {};

          this.mask.value = '';
          $('#aturan_pakai_id').val(null).trigger('change');
          $('#takaran_id').val(null).trigger('change');
          $('#obat').val(null).trigger('change');
        }
      }))
    })
  </script>
@endpush
