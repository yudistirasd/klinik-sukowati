<?php

namespace Database\Seeders;

use App\Models\JenisObat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'BHP'],
            ['name' => 'ALKES'],
            ['name' => 'OBAT'],
        ];

        foreach ($data as $item) {
            JenisObat::updateOrCreate($item);
        }
    }
}
