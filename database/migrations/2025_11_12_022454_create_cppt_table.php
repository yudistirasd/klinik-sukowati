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
        Schema::create('cppt', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pasien_id');
            $table->foreignUuid('kunjungan_id');
            $table->foreignUuid('created_by');
            $table->string('jenis_user');
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('asesmen')->nullable();
            $table->text('plan')->nullable();
            $table->text('edukasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cppt');
    }
};
