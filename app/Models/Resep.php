<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Resep extends Model
{
    use HasUuids;

    protected $table = 'resep';
    public $guarded = [];
}
