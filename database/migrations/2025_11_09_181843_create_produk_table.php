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
        Schema::create('produk', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('jenis');
            $table->bigInteger('tarif')->default(0);
            $table->mediumInteger('dosis')->nullable();
            $table->string('satuan')->nullable();
            $table->string('ihs_id')->nullable();
            $table->string('ihs_bza_code')->nullable();
            $table->string('ihs_bza_display')->nullable();
            $table->string('ihs_kfa_pov_code')->nullable();
            $table->string('ihs_kfa_pov_display')->nullable();
            $table->string('ihs_kfa_poa_code')->nullable();
            $table->string('ihs_kfa_poa_display')->nullable();
            $table->string('ihs_kfa_form_code')->nullable();
            $table->string('ihs_kfa_form_display')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
