<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RiwayatKamar extends Model
{
    use HasUuids;

    protected $table = 'riwayat_kamar';
    public $guarded = [];
}
