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
        Schema::create('log_perubahan_harga', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('stok_id');
            $table->decimal('harga_jual_lama', 16, 2);
            $table->decimal('harga_jual_baru', 16, 2);
            $table->decimal('keuntungan_lama', 16, 2);
            $table->decimal('keuntungan_baru', 16, 2);
            $table->text('keterangan');
            $table->timestamps();
        });

        Schema::table('produk_stok', function (Blueprint $table) {
            $table->foreignUuid('harga_terakhir_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_perubahan_harga');

        Schema::table('produk_stok', function (Blueprint $table) {
            $table->dropColumn('harga_terakhir_id');
        });
    }
};
