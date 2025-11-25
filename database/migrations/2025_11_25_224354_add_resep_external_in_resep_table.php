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
        Schema::table('resep', function (Blueprint $table) {
            $table->enum('asal_resep', ['IN', 'EX'])->default('IN');
            $table->foreignUuid('kunjungan_id')->nullable(true)->change()->comment('untuk resep external, kunjungan id = null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resep', function (Blueprint $table) {
            $table->dropColumn('asal_resep');
        });
    }
};
