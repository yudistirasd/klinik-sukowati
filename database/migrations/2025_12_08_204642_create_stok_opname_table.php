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
        Schema::create('stok_opname', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nomor');
            $table->date('tanggal');
            $table->foreignUuid('created_by');
            $table->enum('status', ['process', 'done'])->default('process');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared("
            create sequence if not exists no_stok_opname_seq
            increment 1
            start 1
            minvalue 1;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_no_stok_opname()
                RETURNS TRIGGER
                LANGUAGE PLPGSQL
                AS
            \$\$
            BEGIN
                new.nomor := (select to_char(current_timestamp, 'YYMMDD')||'B'||lpad((select nextval('no_stok_opname_seq'))::text, 5, '0'));

                RETURN NEW;
            END;
            \$\$
        ");

        DB::unprepared("
        DROP TRIGGER IF EXISTS set_no_stok_opname ON stok_opname;
        CREATE TRIGGER set_no_stok_opname
            BEFORE INSERT
            ON stok_opname
            FOR EACH ROW
            EXECUTE PROCEDURE generate_no_stok_opname();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname');
    }
};
