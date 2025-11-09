@props([
    'name' => 'harga',
    'id' => null,
    'value' => null,
    'placeholder' => '0',
])

@php
  $id = $id ?? $name;
  $rawValue = is_null($value) ? old($name, '') : old($name, $value);
@endphp

<div x-data="uangInput({ raw: '{{ $rawValue }}' })" x-init="init()" class="form-group">
  {{-- Input yang terlihat --}}
  <input type="text" x-ref="visible" x-model="display" @input="format()" @blur="onBlur()" @focus="onFocus()" placeholder="{{ $placeholder }}" class="form-control" autocomplete="off" />

  {{-- Input tersembunyi yang dikirim ke server --}}
  <input type="hidden" name="{{ $name }}" x-model="raw">

  @error($name)
    <div class="text-danger mt-1">{{ $message }}</div>
  @enderror
</div>

@push('scripts')
  <script>
    function uangInput({
      raw = ''
    }) {
      return {
        raw: raw,
        display: '',

        init() {
          // Inisialisasi tampilan awal
          if (this.raw !== '') {
            this.display = this.formatNumber(this.raw);
          }
        },

        format() {
          // Bersihkan semua karakter non-digit
          const cleaned = this.display.replace(/\D/g, '');

          // Update raw (nilai mentah)
          this.raw = cleaned;

          // Format ulang tampilan dengan thousand separator
          this.display = this.formatNumber(cleaned);
        },

        onBlur() {
          // Pastikan tetap format seribu saat keluar fokus
          if (this.raw === '') {
            this.display = '';
            return;
          }
          this.display = this.formatNumber(this.raw);
        },

        onFocus() {
          // Saat fokus, tampilkan tanpa koma (opsional, biar gampang edit)
          // this.display = this.raw;
        },

        formatNumber(value) {
          if (!value) return '';
          return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },
      }
    }
  </script>
@endpush
