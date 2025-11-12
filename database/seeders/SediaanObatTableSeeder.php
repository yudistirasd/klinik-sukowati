<?php

namespace Database\Seeders;

use App\Models\SediaanObat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SediaanObatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => '-'],
            ['name' => 'BTL'],
            ['name' => 'TABLET'],
            ['name' => 'SIRUP'],
            ['name' => 'DROP'],
            ['name' => 'INFUSE'],
            ['name' => 'INJEKSI'],
            ['name' => 'SUPPOSITORIA'],
            ['name' => 'TRANSDERMAL PATCH'],
            ['name' => 'CAPSUL'],
            ['name' => 'CREAM'],
            ['name' => 'EMULSI GEL'],
            ['name' => 'INJEKSI SPINAL'],
            ['name' => 'LARUTAN'],
            ['name' => 'SPRAY'],
            ['name' => 'TETES MATA'],
            ['name' => 'SALEP MATA'],
            ['name' => 'RECTAL TUBE'],
            ['name' => 'KAPSUL'],
            ['name' => 'SUSPENSI'],
            ['name' => 'SALEP'],
            ['name' => 'SYRUP'],
            ['name' => 'OVULA'],
            ['name' => 'AEROSOL'],
            ['name' => 'RESPULES'],
            ['name' => 'TURBUHELR'],
            ['name' => 'NEBULE'],
            ['name' => 'HISAP'],
            ['name' => 'RESPULE'],
            ['name' => 'INHALER'],
            ['name' => 'DISKUS'],
            ['name' => 'TURBUHALER'],
            ['name' => 'NEBULIZER'],
            ['name' => 'SWINGHALER'],
            ['name' => 'SERBUK'],
            ['name' => 'TETES TELINGA'],
            ['name' => 'KAPLET'],
            ['name' => 'SALEP KULIT'],
            ['name' => 'CAIRAN'],
            ['name' => 'GEL'],
            ['name' => 'OINTMENT'],
            ['name' => 'OINMENT'],
            ['name' => 'SOLUTION'],
            ['name' => 'TETES HIDUNG'],
            ['name' => 'NASAL SPRAY'],
            ['name' => 'GRANUL'],
            ['name' => 'SOFTCAPSUL'],
            ['name' => 'ROLLER GEL'],
            ['name' => 'EMULSI'],
            ['name' => 'SUPENSI'],
            ['name' => 'INSULIN'],
            ['name' => 'OINT'],
            ['name' => 'INFUS'],
            ['name' => 'TABLET KUNYAH'],
            ['name' => 'TETS MATA'],
        ];

        foreach ($data as $item) {
            SediaanObat::updateOrCreate($item);
        }
    }
}
