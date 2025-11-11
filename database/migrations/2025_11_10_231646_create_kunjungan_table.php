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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('noregistrasi');
            $table->dateTime('tanggal_registrasi');
            $table->enum('jenis_layanan', ['RJ', 'RI']);
            $table->string('jenis_pembayaran');
            $table->foreignUuid('pasien_id')->references('id')->on('pasien');
            $table->foreignUuid('ruangan_id')->references('id')->on('ruangan');
            $table->foreignUuid('dokter_id')->references('id')->on('users')->comment('');
            $table->foreignUuid('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('icd10_id');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared("
            create sequence if not exists noregistrasi_seq
            increment 1
            start 1
            minvalue 1;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_noregistrasi()
                RETURNS TRIGGER
                LANGUAGE PLPGSQL
                AS
            \$\$
            BEGIN
                new.noregistrasi := (select to_char(current_timestamp, 'YYMMDD')||lpad((select nextval('noregistrasi_seq'))::text, 5, '0'));

                RETURN NEW;
            END;
            \$\$
        ");

        DB::unprepared("
        DROP TRIGGER IF EXISTS set_noregistrasi ON kunjungan;
        CREATE TRIGGER set_noregistrasi
            BEFORE INSERT
            ON kunjungan
            FOR EACH ROW
            EXECUTE PROCEDURE generate_noregistrasi();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
