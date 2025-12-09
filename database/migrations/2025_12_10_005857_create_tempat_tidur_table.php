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
        Schema::create('tempat_tidur', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ruangan_id');
            $table->string('name');
            $table->enum('status', ['isi', 'kosong'])->default('kosong');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tempat_tidur');
    }
};
