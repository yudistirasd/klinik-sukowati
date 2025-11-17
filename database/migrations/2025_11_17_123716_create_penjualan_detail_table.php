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
        Schema::create('penjualan_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('penjualan_id');
            $table->foreignUuid('produk_id');
            $table->foreignUuid('produk_stok_id');
            $table->foreignUuid('kunjungan_id')->nullable();
            $table->foreignUuid('resep_id')->nullable();
            $table->foreignUuid('resep_detail_id')->nullable();
            $table->decimal('harga_beli', 16, 2);
            $table->decimal('harga_jual', 16, 2);
            $table->decimal('keuntungan', 16, 2);
            $table->decimal('qty', 16, 2);
            $table->decimal('total', 16, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan_detail');
    }
};
