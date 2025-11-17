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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor');
            $table->enum('jenis', ['resep_in', 'resep_ex', 'ecer']);
            $table->date('tanggal');
            $table->foreignUuid('kunjungan_id')->nullable();
            $table->foreignUuid('resep_id')->nullable();
            $table->enum('status', ['lunas', 'belum'])->default('belum');
            $table->foreignUuid('created_by');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared("
            create sequence if not exists no_penjualan_seq
            increment 1
            start 1
            minvalue 1;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_no_penjualan()
                RETURNS TRIGGER
                LANGUAGE PLPGSQL
                AS
            \$\$
            BEGIN
                new.nomor := (select to_char(current_timestamp, 'YYMMDD')||'J'||lpad((select nextval('no_penjualan_seq'))::text, 5, '0'));

                RETURN NEW;
            END;
            \$\$
        ");

        DB::unprepared("
        DROP TRIGGER IF EXISTS set_no_penjualan ON penjualan;
        CREATE TRIGGER set_no_penjualan
            BEFORE INSERT
            ON penjualan
            FOR EACH ROW
            EXECUTE PROCEDURE generate_no_penjualan();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
