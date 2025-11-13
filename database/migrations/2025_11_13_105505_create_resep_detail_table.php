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
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('resep_id');
            $table->foreignUuid('produk_id');
            $table->string('signa');
            $table->integer('frekuensi');
            $table->float('unit_dosis');
            $table->integer('lama_hari');
            $table->float('qty');
            $table->foreignId('takaran_id');
            $table->foreignId('aturan_pakai_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_detail');
    }
};
