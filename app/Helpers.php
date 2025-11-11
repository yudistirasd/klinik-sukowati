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
    function formatUang($nominal)
    {
        return number_format($nominal);
    }
}
