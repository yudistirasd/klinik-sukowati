<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembelianDetail extends Model
{
    use HasUuids;

    protected $table = 'pembelian_detail';
    public $guarded = [];

    /**
     * Get the pembelian that owns the PembelianDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pembelian(): BelongsTo
    {
        return $this->belongsTo(Pembelian::class);
    }

    /**
     * Get the produk that owns the PembelianDetail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class);
    }
}
