<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pasien extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'pasien';
    protected $appends = ['usia', 'jenis_kelamin_text '];
    public $guarded = [];

    /**
     * Hitung Usia dinamis (berdasarkan tanggal tertentu atau hari ini)
     *
     * @param  string|null  $tanggalKunjungan
     * @return string|null
     */
    public function getUsia($tanggalKunjungan = null)
    {
        if (!$this->tanggal_lahir) {
            return null;
        }

        $tglLahir = Carbon::parse($this->tanggal_lahir);
        $tglAcuan = $tanggalKunjungan
            ? Carbon::parse($tanggalKunjungan)
            : Carbon::now();

        // Hindari error kalau tanggal kunjungan < tanggal lahir
        if ($tglAcuan->lt($tglLahir)) {
            return '0 hari';
        }

        $diff = $tglLahir->diff($tglAcuan);

        $parts = [];
        if ($diff->y > 0) {
            $parts[] = "{$diff->y} tahun";
        }
        if ($diff->m > 0) {
            $parts[] = "{$diff->m} bulan";
        }
        if ($diff->d > 0) {
            $parts[] = "{$diff->d} hari";
        }

        return implode(' ', $parts) ?: '0 hari';
    }

    /**
     * Accessor untuk menampilkan jenis kelamin dalam bentuk teks lengkap
     *
     * @return string|null
     */
    public function getJenisKelaminTextAttribute()
    {
        return match ($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => null,
        };
    }

    /**
     * Accessor default -> Usia berdasarkan hari ini
     */
    public function getUsiaAttribute()
    {
        return $this->getUsia();
    }

    /**
     * Get the user that owns the Pasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }

    /**
     * Get the kabupaten that owns the Pasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }

    /**
     * Get the kecamatan that owns the Pasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class);
    }

    /**
     * Get the kelurahan that owns the Pasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kelurahan(): BelongsTo
    {
        return $this->belongsTo(Kelurahan::class);
    }

    /**
     * Get the agama that owns the Pasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agama(): BelongsTo
    {
        return $this->belongsTo(Agama::class);
    }

    /**
     * Get the pekerjaan that owns the Pasien
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class);
    }
}
