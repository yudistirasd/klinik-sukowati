<?php

use Illuminate\Support\Collection;

if (! function_exists('roles')) {
    function roles(): Collection
    {
        return collect([
            (object) [
                'id' => 'admin',
                'name' => 'Administrator',
                'nakes' => false
            ],
            (object) [
                'id' => 'dokter',
                'name' => 'Dokter',
                'nakes' => true
            ],
            (object) [
                'id' => 'perawat',
                'name' => 'Perawat',
                'nakes' => true
            ],
            (object) [
                'id' => 'apoteker',
                'name' => 'Apoteker',
                'nakes' => true
            ],
            (object) [
                'id' => 'loket',
                'name' => 'Petugas Loket',
                'nakes' => false
            ]
        ]);
    }
}

if (! function_exists('jenisPembayaran')) {
    function jenisPembayaran(): Collection
    {
        return collect(['UMUM']);
    }
}

if (! function_exists('jenisLayanan')) {
    function jenisLayanan(): Collection
    {
        return collect([
            (object) [
                'id' => 'RJ',
                'name' => 'Rawat Jalan'
            ]
        ]);
    }
}

if (!function_exists('formatUang')) {
    function formatUang($nominal, $digit = false)
    {
        return number_format($nominal, $digit ? 2 : 0);
    }
}


if (! function_exists('minifyHtml')) {

    function minifyHtml(string $html): string
    {
        // Daftar tag yang tidak boleh diutak-atik
        $sensitiveTags = ['pre', 'textarea', 'script', 'style'];

        $placeholders = [];
        $i = 0;

        // 1. Extract tag sensitif & ganti dengan placeholder
        foreach ($sensitiveTags as $tag) {
            $regex = "/<{$tag}.*?>.*?<\\\/{$tag}>/si";

            $html = preg_replace_callback($regex, function ($match) use (&$placeholders, &$i) {
                $key = "%%SENSITIVE_TAG_" . $i . "%%";
                $placeholders[$key] = $match[0];
                $i++;
                return $key;
            }, $html);
        }

        // 2. Minify HTML aman (tidak menyentuh JS/CSS)
        // - hapus komentar HTML
        $html = preg_replace('/<!--(?!\[if).*?-->/', '', $html);

        // - hapus whitespace berlebihan (multi-spasi → satu)
        $html = preg_replace('/\s+/', ' ', $html);

        // - hapus spasi antar tag
        $html = preg_replace('/>\s+</', '><', $html);

        // - trim final
        $html = trim($html);

        // 3. Restore kembali tag sensitif
        foreach ($placeholders as $key => $original) {
            $html = str_replace($key, $original, $html);
        }

        return $html;
    }
}

if (! function_exists('formatTanggal')) {
    function formatTanggal($tanggal, $withTime = false)
    {
        $format = $withTime ? 'd M Y H:i:s' : 'd M Y';
        return \Carbon\Carbon::parse($tanggal)->format($format);
    }
}

if (! function_exists('tipeRacikan')) {
    function tipeRacikan($tipe)
    {
        $tipeRacikan = [
            'dtd' => 'DTD',
            'non_dtd' => 'Non DTD'
        ];

        return $tipeRacikan[$tipe];
    }
}

if (! function_exists('waktuPemberianObat')) {
    function waktuPemberianObat(): Collection
    {
        return collect([
            'Pagi',
            'Siang',
            'Sore',
            'Malam'
        ]);
    }
}

// untuk hitung usia pasien jika query dari non eloquent model
if (!function_exists('hitungUsiaPasien')) {
    function hitungUsiaPasien($tanggal_lahir, $tanggalKunjungan = null)
    {
        if (!$tanggal_lahir) {
            return '-';
        }

        $tglLahir = \Carbon\Carbon::parse($tanggal_lahir);
        $tglAcuan = $tanggalKunjungan
            ? \Carbon\Carbon::parse($tanggalKunjungan)
            : \Carbon\Carbon::now();

        $diff = $tglLahir->diff($tglAcuan);

        // --- LOGIKA BARU ---
        // 1. ≥ 1 tahun → tampil TAHUN saja
        if ($diff->y >= 1) {
            return "{$diff->y} Th";
        }

        // 2. < 1 tahun, tetapi ≥ 1 bulan → tampil BULAN saja
        if ($diff->m >= 1) {
            return "{$diff->m} Bln";
        }

        // 3. < 1 bulan → tampil HARI saja
        return "{$diff->d} Hr";
    }
}


if (! function_exists('metodePembayaran')) {
    function metodePembayaran(): Collection
    {
        return collect([
            'Tunai',
            'Transfer',
            'Qris',
        ]);
    }
}

if (! function_exists('kelasRawat')) {
    function kelasRawat(): Collection
    {
        return collect([
            'Kelas VIP',
            'Kelas Utama',
            'Kelas 1',
            'Kelas 2'
        ]);
    }
}
