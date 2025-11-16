<?php

namespace Database\Seeders;

use App\Models\SatuanKemasanObat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatuanKemasanObatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'BOX'],
            ['name' => 'DUS'],
            ['name' => 'PACK / PAKET'],
            ['name' => 'KOTAK'],
        ];

        foreach ($data as $value) {
            SatuanKemasanObat::updateOrCreate($value);
        }
    }
}
