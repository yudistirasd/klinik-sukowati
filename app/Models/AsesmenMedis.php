<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AsesmenMedis extends Model
{
    use HasUuids;

    protected $table = 'asesmen_medis';
    public $guarded = [];
}
