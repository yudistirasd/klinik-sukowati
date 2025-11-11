<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('asesmen_medis', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pasien_id');
            $table->foreignUuid('kunjungan_id');
            $table->foreignUuid('created_by');
            $table->text('keluhan_utama')->nullable();
            $table->text('penyakit_dahulu')->nullable();
            $table->text('penyakit_sekarang')->nullable();
            $table->text('keadaan_umum')->nullable();
            $table->text('diagnosis_sementara')->nullable();
            $table->text('indikasi_medis')->nullable();
            $table->string('tindak_lanjut')->nullable();
            $table->string('tindak_lanjut_ket')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asesmen_medis');
    }
};
