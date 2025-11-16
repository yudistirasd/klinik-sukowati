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
        Schema::create('pembelian_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('pembelian_id');
            $table->foreignUuid('produk_id');
            $table->string('barcode');
            $table->boolean('barcode_generated')->default('N');
            $table->date('expired_date')->nullable();
            $table->mediumInteger('jumlah_kemasan');
            $table->string('satuan_kemasan');
            $table->mediumInteger('isi_per_kemasan');
            $table->mediumInteger('qty')->comment('untuk satuan terkecil obat');
            $table->decimal('harga_beli_kemasan', 16, 2);
            $table->decimal('harga_beli_satuan', 16, 2)->comment('untuk satuan terkecil obat');
            $table->decimal('harga_jual_satuan', 16, 2)->comment('untuk satuan terkecil obat');
            $table->decimal('keuntungan_satuan', 16, 2);
            $table->integer('margin');
            $table->decimal('total', 16, 2);
            $table->timestamps();
        });

        DB::unprepared("
            create sequence if not exists barcode_seq
            increment 1
            start 1
            minvalue 1;
        ");

        DB::unprepared("
            CREATE OR REPLACE FUNCTION generate_barcode()
                RETURNS TRIGGER
                LANGUAGE PLPGSQL
                AS
            \$\$
            BEGIN

               CASE
                    WHEN new.barcode IS NULL THEN
                        new.barcode := lpad((select nextval('barcode_seq'))::text, 6, '0');
                        new.barcode_generated := 'Y';
                    ELSE
                END CASE;

                RETURN NEW;
            END;
            \$\$
        ");

        DB::unprepared("
        DROP TRIGGER IF EXISTS set_barcode ON pembelian_detail;
        CREATE TRIGGER set_barcode
            BEFORE INSERT
            ON pembelian_detail
            FOR EACH ROW
            EXECUTE PROCEDURE generate_barcode();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_detail');
    }
};
