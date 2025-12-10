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
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->foreignUuid('ruangan_last_id')->nullable()->comment('untuk rawat inap, jika pindah ruang maka update ini');
            $table->foreignUuid('tempat_tidur_last_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropColumn(['ruangan_last_id', 'tempat_tidur_last_id']);
        });
    }
};
