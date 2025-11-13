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
        Schema::create('resep', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor');
            $table->date('tanggal');
            $table->foreignUuid('pasien_id');
            $table->foreignUuid('kunjungan_id');
            $table->foreignUuid('dokter_id');
            $table->foreignUuid('verified_by')->nullable();
            $table->enum('status', ['ORDER', 'VERIFIED'])->default('ORDER');
            $table->timestamps();
        });

        DB::unprepared("
            create sequence if not exists noresep_seq
            increment 1
            start 1
            minvalue 1;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_noresep()
                RETURNS TRIGGER
                LANGUAGE PLPGSQL
                AS
            \$\$
            BEGIN
                new.nomor := (select to_char(current_timestamp, 'YYMMDD')||lpad((select nextval('noregistrasi_seq'))::text, 5, '0'));

                RETURN NEW;
            END;
            \$\$
        ");

        DB::unprepared("
        DROP TRIGGER IF EXISTS set_noresep ON resep;
        CREATE TRIGGER set_noresep
            BEFORE INSERT
            ON resep
            FOR EACH ROW
            EXECUTE PROCEDURE generate_noresep();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
