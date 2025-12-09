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
        Schema::create('stok_opname_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stok_opname_id');
            $table->foreignUuid('produk_id');
            $table->string('barcode');
            $table->date('expired_date')->nullable();
            $table->decimal('harga_beli', 16, 2)->default(0);
            $table->decimal('harga_jual_resep', 16, 2)->default(0);
            $table->decimal('harga_jual_bebas', 16, 2)->default(0);
            $table->decimal('harga_jual_apotek', 16, 2)->default(0);
            $table->decimal('qty_system', 16, 2)->default(0);
            $table->decimal('qty_real', 16, 2)->default(0);
            $table->decimal('qty_selisih', 16, 2)->default(0);
            $table->string('status_stok');
            $table->string('alasan');
            $table->text('alasan_lainnya')->nullable();
            $table->jsonb('barang_stok_backup')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname_detail');
    }
};
