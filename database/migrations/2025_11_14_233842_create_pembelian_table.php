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
        Schema::create('pembelian', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor');
            $table->date('tanggal');
            $table->foreignUuid('suplier_id');
            $table->foreignUuid('created_by');
            $table->enum('insert_stok', ['sudah', 'belum'])->default('belum');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared("
            create sequence if not exists no_pembelian_seq
            increment 1
            start 1
            minvalue 1;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_no_pembelian()
                RETURNS TRIGGER
                LANGUAGE PLPGSQL
                AS
            \$\$
            BEGIN
                new.nomor := (select to_char(current_timestamp, 'YYMMDD')||'B'||lpad((select nextval('no_pembelian_seq'))::text, 5, '0'));

                RETURN NEW;
            END;
            \$\$
        ");

        DB::unprepared("
        DROP TRIGGER IF EXISTS set_no_pembelian ON pembelian;
        CREATE TRIGGER set_no_pembelian
            BEFORE INSERT
            ON pembelian
            FOR EACH ROW
            EXECUTE PROCEDURE generate_no_pembelian();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian');
    }
};
