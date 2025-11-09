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

if (!function_exists('formatUang')) {
    function formatUang($nominal)
    {
        return number_format($nominal);
    }
}
