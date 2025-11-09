<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{

    use HasUuids, SoftDeletes;

    protected $table = 'produk';
    public $guarded = [];

    public function scopeTindakan($query)
    {
        return $query->where('jenis', 'tindakan');
    }
}
