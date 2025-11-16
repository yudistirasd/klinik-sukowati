<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class LogPerubahanHarga extends Model
{
    use HasUuids;

    protected $table = 'log_perubahan_harga';
    public $guarded = [];
}
