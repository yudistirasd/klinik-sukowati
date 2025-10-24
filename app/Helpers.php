<?php

use Illuminate\Support\Collection;

if (! function_exists('roles')) {
    function roles(): Collection
    {
        return collect([
            'roles' => [
                'admin' => 'Administrator',
                'perawat' => 'Perawat',
                'dokter' => 'Dokter',
                'apoteker' => 'Apoteker',
            ],
        ]);
    }
}
