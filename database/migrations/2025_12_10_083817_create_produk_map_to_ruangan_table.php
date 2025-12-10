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
        Schema::create('produk_map_to_ruangan', function (Blueprint $table) {
            $table->foreignUuid('produk_id');
            $table->foreignUuid('ruangan_id');
            $table->decimal('tarif', 16, 2)->comment('tarif rawat inap per ruangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_map_to_ruangan');
    }
};
