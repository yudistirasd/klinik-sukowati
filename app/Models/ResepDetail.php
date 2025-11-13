<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ResepDetail extends Model
{
    use HasUuids;

    protected $table = 'resep_detail';
    public $guarded = [];
}
