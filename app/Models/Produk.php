<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Produk extends Model
{

    use HasUuids, SoftDeletes;

    protected $table = 'produk';
    public $guarded = [];

    public function scopeTindakan($query)
    {
        return $query->where('jenis', 'tindakan');
    }

    public function scopeObat($query)
    {
        return $query->where('jenis', 'obat');
    }

    public function scopeLaborat($query)
    {
        return $query->where('jenis', 'laborat');
    }

    /**
     * The ruangan that belong to the Produk
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function ruangan(): BelongsToMany
    {
        return $this->belongsToMany(Ruangan::class, 'produk_map_to_ruangan', 'produk_id', 'ruangan_id')
            ->withPivot('tarif');
    }
}
