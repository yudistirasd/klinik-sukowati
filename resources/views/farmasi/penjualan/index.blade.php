@extends('layouts.app')

@section('title', 'Penjualan Obat')
@section('subtitle', 'Farmasi')

@push('css')
  <link href="{{ asset('libs/select2/select2.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/select2/select2-bootstrap-5-theme.css') }}" rel="stylesheet" />
  <link href="{{ asset('libs/datatables/dataTables.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/fixedHeader.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
  <link href="{{ asset('libs/datatables/responsive.bootstrap5.min.css') }}?{{ config('app.version') }}" rel="stylesheet">
@endpush

@section('action-page')
  <a href="javascript:;" class="btn btn-primary btn-5" onclick="handleModalCreatePenjualan()">
    <div class="ti ti-plus me-1"></div>
    Penjualan
  </a>
@endsection

@section('content')
  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered table-hover" id="penjualan-table">
          <thead>
            <tr>
              <th class="text-center">#</th>
              <th class="text-center">Nomor</th>
              <th class="text-center">Tanggal</th>
              <th class="text-center">Suplier</th>
              <th class="text-center">No Faktur</th>
              <th class="text-center">Tgl Faktur</th>
              <th class="text-center">Ditambahkan Ke Stok</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Form -->
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
    // const table = new DataTable('#penjualan-table', {
    //   processing: true,
    //   serverSide: true,
    //   autoWidth: false,
    //   destroy: true,
    //   ajax: route('api.farmasi.penjualan.dt'),
    //   order: [
    //     [
    //       2, 'desc'
    //     ]
    //   ],
    //   columns: [{
    //       data: 'DT_RowIndex',
    //       name: 'DT_RowIndex',
    //       orderable: false,
    //       searchable: false,
    //       sClass: 'text-center',
    //       width: '5%'
    //     },
    //     {
    //       data: 'nomor',
    //       name: 'nomor',
    //       sClass: 'text-center'
    //     },
    //     {
    //       data: 'tanggal',
    //       name: 'tanggal',
    //       sClass: 'text-center'
    //     },
    //     {
    //       data: 'suplier.name',
    //       name: 'suplier.name',
    //       sClass: 'text-start'
    //     },
    //     {
    //       data: 'no_faktur',
    //       name: 'no_faktur',
    //       sClass: 'text-center'
    //     },
    //     {
    //       data: 'tgl_faktur',
    //       name: 'tgl_faktur',
    //       sClass: 'text-center'
    //     },
    //     {
    //       data: 'insert_stok',
    //       name: 'insert_stok',
    //       sClass: 'text-center'
    //     },
    //     {
    //       data: 'action',
    //       name: 'action',
    //       sClass: 'text-center',
    //       width: "15%"
    //     },
    //   ]
    // });

    const handleModalCreatePenjualan = (action, title, data = null) => {
      Swal.fire({
        title: 'Buat Penjualan',
        html: `
            <div style="text-align:left">
            <label for="jenis_penjualan" class="form-label">Pilih Jenis Penjualan</label>
                <select id="jenis_penjualan" class="form-select">
                    <option value="">-- Pilih Jenis Penjualan --</optoin>
                    <option value="bebas">Bebas</option>
                    <option value="apotek">Apotek</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Buat Penjualan',
        cancelButtonText: 'Batal',
        focusConfirm: false,
        preConfirm: () => {
          const jenis = document.getElementById('jenis_penjualan').value;
          if (!jenis) {
            Swal.showValidationMessage('Silakan pilih jenis penjualan');
            return false;
          }
          return {
            jenis_penjualan: jenis
          };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = route('farmasi.penjualan.create', {
            jenis: result.value.jenis_penjualan
          })
        }
      });
    }
  </script>
@endpush
