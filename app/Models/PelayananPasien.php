<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelayananPasien extends Model
{
    use HasUuids;

    protected $table = 'pelayanan_pasien';
    public $guarded = [];

    /**
     * Get the produk that owns the PelayananPasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
