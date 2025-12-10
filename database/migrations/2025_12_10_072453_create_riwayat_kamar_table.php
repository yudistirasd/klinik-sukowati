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
        Schema::create('riwayat_kamar', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kunjungan_id');
            $table->foreignUuid('ruangan_id');
            $table->foreignUuid('tempat_tidur_id');
            $table->timestamp('tgl_masuk');
            $table->timestamp('tgl_keluar')->nullable();
            $table->decimal('tarif', 16, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_kamar');
    }
};
