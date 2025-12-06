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
            return '-';
        }

        $tglLahir = Carbon::parse($this->tanggal_lahir);
        $tglAcuan = $tanggalKunjungan
            ? Carbon::parse($tanggalKunjungan)
            : Carbon::now();

        $diff = $tglLahir->diff($tglAcuan);

        // --- LOGIKA BARU ---
        // 1. ≥ 1 tahun → tampil TAHUN saja
        if ($diff->y >= 1) {
            return "{$diff->y} th";
        }

        // 2. < 1 tahun, tetapi ≥ 1 bulan → tampil BULAN saja
        if ($diff->m >= 1) {
            return "{$diff->m} bln";
        }

        // 3. < 1 bulan → tampil HARI saja
        return "{$diff->d} hr";
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
