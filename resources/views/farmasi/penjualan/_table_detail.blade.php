@if ($details->count() == 0)
  <tr>
    <td class="text-center" colspan="8">Belum ada data</td>
  </tr>
@else
  @php
    $total = 0;
  @endphp
  @foreach ($details as $key => $item)
    @php
      $total += $item->harga_jual * $item->qty;
    @endphp
    <tr>
      <td class="text-center">{{ $key + 1 }}</td>
      <td>{{ $item->obat }}</td>
      <td class="text-center">{{ $item->barcode }}</td>
      <td class="text-center">{{ $item->expired_date }}</td>
      <td class="text-center">{{ $item->qty }} {{ $item->sediaan }}</td>
      <td class="text-end">{{ formatUang($item->harga_jual) }}</td>
      <td class="text-end">{{ formatUang($item->harga_jual * $item->qty) }}</td>
      <td class="text-end">
        @if ($penjualan->status == 'belum')
          <button type="button" class="btn btn-danger btn-icon" onclick="confirmDelete(`{{ route('api.farmasi.penjualan.detail.destroy', ['penjualan' => $penjualan->id, 'produk' => $item->produk_id]) }}`, resepObat)">
            <i class="ti ti-trash"></i>
          </button>
        @endif
      </td>
    </tr>
  @endforeach
  <tr>
    <th class="text-end" colspan="6">Total</th>
    <th class="text-end">{{ formatUang($total) }}</th>
  </tr>
@endif
