@if ($resep->isEmpty())
  <tr>
    <td colspan="9" class="text-center">No prescriptions found.</td>
  </tr>
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
  @foreach ($resep as $key => $row)
    <!-- Resep 1: Verified -->
    <div class="card resep-card shadow-sm mb-3">
      <div class="resep-header p-3" data-bs-toggle="collapse" data-bs-target="#{{ $row->id }}">
        <div class="row align-items-center">
          <div class="col-auto">
            <i class="ti ti-chevron-down chevron-icon text-muted"></i>
          </div>
          <div class="col">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
              <span class="badge bg-dark text-dark-fg px-2 py-1"><i class="ti ti-prescription"></i>{{ $row->nomor }}</span>
              @if ($row->metode_penulisan == 'manual')
                {{-- Manual: Warna Warning (Kuning/Oranye) menandakan perlu perhatian khusus saat dibaca --}}
                <span class="badge bg-warning-subtle text-warning rounded-pill px-2 py-1">
                  <i class="ti ti-writing me-1"></i>Tulis Manual
                </span>
              @else
                <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                  <i class="ti ti-pill me-1"></i>Pilih Obat
                </span>
              @endif
              @if ($row->status == 'VERIFIED')
                <span class="badge badge-verified rounded-pill px-2 py-1">
                  <i class="ti ti-circle-check"></i>Verified
                </span>
              @else
                <span class="badge badge-order rounded-pill px-2 py-1">
                  <i class="ti ti-clock"></i>Order
                </span>
              @endif
            </div>
            <div class="d-flex flex-wrap gap-3 text-muted">
              <span><i class="ti ti-user me-1"></i>{{ $row->dokter->name }}</span>
              <span><i class="ti ti-calendar me-1"></i>{{ $row->tanggal }}</span>
              @if ($row->metode_penulisan == 'master_obat')
                <span><i class="ti ti-pill me-1"></i>{{ $row->items->count() }} Item</span>
              @endif
            </div>
          </div>
        </div>
      </div>
      <div class="collapse {{ $key > 0 && $row->status == 'VERIFIED' ? '' : 'show' }}" id="{{ $row->id }}">
        <div class="card-body p-0 border-top">
          @if ($row->metode_penulisan == 'manual')
            <div class="m-3">
              <label for="" class="form-label">Resep Manual Dokter</label>
              <fieldset class="form-fieldset">
                {!! $row->resep_detail_manual !!}
              </fieldset>
            </div>
          @endif
          <table class="table table-obat">
            <thead>
              <tr>
                <th class="ps-3" style="width:40px">#</th>
                <th class="text-center" style="width:5%">Jenis Resep</th>
                <th class="text-center" style="width:20%">Nama Obat / Racikan</th>
                <th class="text-center">Signa</th>
                <th class="text-center">Hari</th>
                <th class="text-center">Jumlah</th>
                <th class="text-center">Aturan & Cara Pakai</th>
                <th class="text-center">Keterangan</th>
                <th>Aksi</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($row->items as $item)
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
                      {{ $item->jumlah_racikan }} {{ $item->kemasan_racikan }}
                    @endif
                  </td>
                  <td>
                    {{ $item->aturan_pakai }}<br>
                    {{ $item->waktu_pemberian_obat }}<br>
                    {{ $item->kondisi_pemberian }}
                  </td>
                  <td class="text-muted fst-italic">{{ $item->catatan ?? '-' }} </td>
                  <td>
                    @if ($row->status == 'ORDER')
                      <button type='button' class='btn btn-danger btn-icon' onclick="confirmDelete(`{{ route('api.pemeriksaan.destroy.resep-detail', ['resep' => $row->id, 'receipt_number' => $item->receipt_number]) }}`, resepObat)">
                        <i class='ti ti-trash'></i>
                      </button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @endforeach
@endif
