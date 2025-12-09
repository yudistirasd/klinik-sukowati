<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class StokOpnameDetail extends Model
{
    use HasUuids;

    protected $table = 'stok_opname_detail';
    public $guarded = [];
}
