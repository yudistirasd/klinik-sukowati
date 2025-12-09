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
        Schema::table('produk', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable();
            $table->string('nilai_normal_laki_laki')->nullable();
            $table->string('nilai_normal_perempuan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropColumn(['parent_id', 'nilai_normal_laki_laki', 'nilai_normal_perempuan']);
        });
    }
};
