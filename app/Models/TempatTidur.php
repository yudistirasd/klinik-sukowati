<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TempatTidur extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'tempat_tidur';
    public $guarded = [];
}
