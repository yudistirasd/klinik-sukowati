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
        Schema::create('produk_stok', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('produk_id');
            $table->foreignUuid('pembelian_id')->nullable();
            $table->foreignUuid('pembelian_detail_id')->nullable();
            $table->date('tanggal_stok');
            $table->string('barcode');
            $table->date('expired_date')->nullable();
            $table->decimal('harga_beli');
            $table->decimal('harga_jual');
            $table->decimal('keuntungan');
            $table->decimal('masuk', 16, 2)->default(0);
            $table->decimal('keluar', 16, 2)->default(0);
            $table->decimal('ready', 16, 2)->default(0);
            $table->foreignUuid('created_by');
            $table->timestamps();

            $table->unique(
                ['produk_id', 'pembelian_id', 'pembelian_detail_id'],
                'produk_stok_unique_pembelian'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_stok');
    }
};
