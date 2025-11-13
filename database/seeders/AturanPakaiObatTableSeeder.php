<?php

namespace Database\Seeders;

use App\Models\AturanPakaiObat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AturanPakaiObatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Sebelum Makan'],
            ['name' => 'Sesudah Makan'],
            ['name' => 'Saat Makan'],
            ['name' => 'Obat Luar'],
            ['name' => 'Tetes Mata'],
            ['name' => 'Tetes Telinga'],
            ['name' => 'Serahkan Dokter'],
            ['name' => 'Masukan Alat / Hisap'],
            ['name' => '2 Jam Sesudah Makan'],
            ['name' => 'Pagi Sebelum Jam 6'],
            ['name' => 'Obat Suntik'],
            ['name' => 'Kunyah'],
            ['name' => 'Sesudah Makan Obat Harus habis'],
            ['name' => 'Pagi (Sesudah Makan)'],
            ['name' => 'Siang (Sesudah Makan)'],
            ['name' => 'Sore (Sesudah Makan)'],
            ['name' => 'Malam (Sesudah Makan)'],
            ['name' => 'Oleskan Tipis'],
            ['name' => 'Pagi (Sebelum Makan)'],
            ['name' => 'Sore (Sebelum Makan)'],
            ['name' => 'Bila Perlu'],
        ];

        foreach ($data as $value) {
            AturanPakaiObat::updateOrCreate($value);
        }
    }
}
