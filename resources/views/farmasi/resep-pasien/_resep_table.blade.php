@if (empty($resep))
  <div class="text-center">No prescriptions found.</div>
@else
  <style>
    body {
      background-color: #f1f5f9;
      min-height: 100vh;
    }

    .resep-card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
      transition: all 0.2s;
    }

    .resep-card:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .resep-header {
      background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
      cursor: pointer;
    }

    .resep-header:hover {
      background: linear-gradient(135deg, #eef2f7 0%, #e2e8f0 100%);
    }

    .resep-header.collapsed .chevron-icon {
      transform: rotate(-90deg);
    }

    .chevron-icon {
      transition: transform 0.2s;
    }

    .badge-verified {
      background-color: #d1fae5;
      color: #047857;
      font-weight: 500;
    }

    .badge-order {
      background-color: #fef3c7;
      color: #b45309;
      font-weight: 500;
    }

    .badge-racikan {
      background-color: #ede9fe;
      color: #7c3aed;
    }

    .badge-non-racikan {
      background-color: #dbeafe;
      color: #2563eb;
    }

    .table-obat {
      font-size: 0.875rem;
      margin-bottom: 0;
    }

    .table-obat thead th {
      background-color: #f8fafc;
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #64748b;
      font-weight: 600;
      border-bottom: 2px solid #e2e8f0;
    }

    .table-obat tbody tr:hover {
      background-color: #f8fafc;
    }

    .composition-list {
      list-style: none;
      padding-left: 0;
      margin: 0;
      font-size: 0.8rem;
    }

    .composition-list li {
      position: relative;
      padding-left: 12px;
      color: #64748b;
    }

    .composition-list li::before {
      content: "•";
      color: #8b5cf6;
      position: absolute;
      left: 0;
    }

    .resep-number {
      font-family: 'Courier New', monospace;
      background: #e0e7ff;
      color: #4338ca;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.8rem;
    }

    .section-title {
      color: #1e3a5f;
    }
  </style>
  <!-- Resep 1: Verified -->
  <div class="card resep-card shadow-sm mb-3">
    <div class="resep-header p-3">
      <div class="row align-items-center">
        <div class="col-auto" data-bs-toggle="collapse" data-bs-target="#resep1">
          <i class="ti ti-chevron-down chevron-icon text-muted"></i>
        </div>
        <div class="col" data-bs-toggle="collapse" data-bs-target="#resep1">
          <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
            <span class="resep-number"><i class="bi bi-file-earmark-medical me-1"></i>{{ $resep->nomor }}</span>
            @if ($resep->status == 'verified')
              <span class="badge badge-verified rounded-pill px-2 py-1">
                <i class="ti ti-circle-check me-1"></i>Verified
              </span>
            @else
              <span class="badge badge-order rounded-pill px-2 py-1">
                <i class="ti ti-clock me-1"></i>Order
              </span>
            @endif
          </div>
          <div class="d-flex flex-wrap gap-3 text-muted">
            <span><i class="ti ti-user me-1"></i>{{ $resep->dokter->name }}</span>
            <span><i class="ti ti-calendar me-1"></i>{{ $resep->tanggal }}</span>
            <span><i class="ti ti-pill me-1"></i>{{ $resep->items->count() }} Item</span>
          </div>
        </div>
        <div class="col-auto d-flex gap-2">
          <button class="btn btn-icon btn-outline-primary" onclick="event.stopPropagation()" x-show="resep.status == 'VERIFIED'"><i class="ti ti-printer"></i></button>
          <button class="btn btn-icon btn-outline-dark" onclick="handleVerifikasiResep(event)"><i class="ti ti-credit-card"></i></button>
        </div>
      </div>
    </div>
    <div class="collapse show" id="resep1">
      <div class="card-body p-0 border-top">
        <table class="table table-obat">
          <thead>
            <tr>
              <th class="ps-3" style="width:2%">#</th>
              <th class="text-center" style="width:5%">Jenis Resep</th>
              <th class="text-center" style="width:20%">Nama Obat / Racikan</th>
              <th class="text-center">Signa</th>
              <th class="text-center">Hari</th>
              <th class="text-center">Jumlah</th>
              <th class="text-center">Harga</th>
              <th class="text-center">Total</th>
              <th class="text-center" style="">Aturan Pakai</th>
              <th>Keterangan</th>
              <th>Aksi</th>
            </tr>
          </thead>

          <tbody>
            @php
              $total = 0;
            @endphp
            @foreach ($resep->items as $item)
              <tr>
                <td class="ps-3 text-center">R/{{ $item->receipt_number }}</td>
                <td>
                  @if ($item->jenis_resep == 'non_racikan')
                    <span class="badge badge-non-racikan"><i class="ti ti-pill me-1"></i>Non Racikan</span>
                  @else
                    <span class="badge badge-racikan"><i class="ti ti-droplet-half me-1"></i>Racikan</span>
                  @endif
                </td>
                @if ($item->jenis_resep == 'non_racikan')
                  <td class="fw-medium">{{ $item->obat ?? '-' }}</td>
                @else
                  <td>
                    <div class="fw-medium">{{ $item->obat }}</div>
                    <ul class="composition-list mt-1">
                      @foreach ($item->komposisi as $komposisi)
                        @if ($item->tipe_racikan == 'dtd')
                          <li class="mb-2">
                            {{ $komposisi->obat ?? '-' }}<br>
                            {{ $komposisi->dosis_per_racikan }} {{ $komposisi->satuan_dosis }} <i class="ti ti-x fs-5"></i> {{ $item->jumlah_racikan }} {{ $item->kemasan_racikan }} -> {{ $komposisi->qty }} {{ $komposisi->sediaan }}
                          </li>
                        @else
                          <li class="mb-2">
                            {{ $komposisi->obat ?? '-' }}<br>
                            {{ $komposisi->total_dosis_obat }} {{ $komposisi->satuan_dosis }} <i class="ti ti-divide fs-5"></i> {{ $item->jumlah_racikan }} {{ $item->kemasan_racikan }} -> {{ $komposisi->qty }} {{ $komposisi->sediaan }}
                          </li>
                        @endif
                      @endforeach
                    </ul>
                    <small class="text-purple text-uppercase" style="color:#7c3aed"><i class="bi bi-box me-1"></i>{{ $item->jumlah_racikan }} {{ $item->kemasan_racikan }}</small>
                  </td>
                @endif
                <td class="text-center">{{ $item->signa }}</td>
                <td class="text-center">{{ $item->lama_hari ?? '-' }}</td>
                <td class="text-uppercase">
                  @if ($item->jenis_resep == 'non_racikan')
                    {{ $item->qty }} {{ $item->sediaan }}
                  @else
                    <ul class="composition-list">
                      @foreach ($item->komposisi as $komposisi)
                        <li class="mb-2 text-black fs-4"> {{ $komposisi->qty }} {{ $komposisi->sediaan }}
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </td>
                <td class="text-end">
                  @if ($item->jenis_resep == 'non_racikan')
                    {{ formatUang($item->harga_jual) }}
                  @else
                    <ul class="composition-list">
                      @foreach ($item->komposisi as $komposisi)
                        <li class="mb-2 text-black fs-4"> {{ formatUang($komposisi->harga_jual) }}
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </td>
                <td class="text-end">
                  @if ($item->jenis_resep == 'non_racikan')
                    @php
                      $total += $item->total;
                    @endphp
                    {{ formatUang($item->total) }}
                  @else
                    <ul class="composition-list">
                      @foreach ($item->komposisi as $komposisi)
                        @php
                          $total += $komposisi->total;
                        @endphp
                        <li class="mb-2 text-black fs-4"> {{ formatUang($komposisi->total) }}
                        </li>
                      @endforeach
                    </ul>
                  @endif
                </td>
                <td>{{ $item->aturan_pakai }}</td>
                <td class="text-muted fst-italic">-</td>
                <td>
                  @if ($resep->status == 'ORDER')
                    <button type='button' class='btn btn-danger btn-icon' onclick="confirmDelete(`{{ route('api.pemeriksaan.destroy.resep-detail', ['resep' => $resep->id, 'receipt_number' => $item->receipt_number]) }}`, handleResepObat.bind(null, '{{ $resep->id }}'))">
                      <i class='ti ti-trash'></i>
                    </button>
                  @endif
                </td>
              </tr>
            @endforeach
            <tr>
              <th colspan="7" class="text-end">Total</th>
              <th class="text-end">{{ formatUang($total) }}</th>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endif
