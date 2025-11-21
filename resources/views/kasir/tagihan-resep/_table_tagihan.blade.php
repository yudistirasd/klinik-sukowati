@if ($tindakan->count() == 0)
  <tr>
    <td class="text-center" colspan="3">Belum ada tindakan</td>
  </tr>
@else
  @php
    $total = 0;
  @endphp
  @foreach ($tindakan as $key => $item)
    @php
      $total += $item->harga;
    @endphp
    <tr>
      <td class="text-center">{{ $key + 1 }}</td>
      <td>{{ $item->produk->name }}</td>
      <td class="text-end">{{ formatUang($item->harga) }}</td>
    </tr>
  @endforeach
  <tr>
    <th class="text-end" colspan="2">Total</th>
    <th class="text-end">{{ formatUang($total) }}</th>
  </tr>
@endif
