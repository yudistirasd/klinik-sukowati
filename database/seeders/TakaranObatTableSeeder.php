<?php

namespace Database\Seeders;

use App\Models\TakaranObat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TakaranObatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Kapsul'],
            ['name' => 'Tablet'],
            ['name' => 'Sendok Teh'],
            ['name' => 'Takar'],
            ['name' => 'Tetes'],
            ['name' => 'Hisap'],
            ['name' => 'Semprot'],
            ['name' => 'CC'],
            ['name' => 'Unit'],
            ['name' => 'Sachet'],
            ['name' => 'Bungkus'],
            ['name' => 'Ampul'],
            ['name' => 'Vial'],
            ['name' => 'Tube'],
            ['name' => 'Sendok Makan'],
            ['name' => 'Oles Tipis'],
            ['name' => 'ml'],
        ];

        foreach ($data as $value) {
            TakaranObat::updateOrCreate($value);
        }
    }
}
