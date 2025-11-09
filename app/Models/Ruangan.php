<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ruangan extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'ruangan';
    public $guarded = [];

    /**
     * Get the departemen that owns the Ruangan
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departemen(): BelongsTo
    {
        return $this->belongsTo(Departemen::class);
    }
}
