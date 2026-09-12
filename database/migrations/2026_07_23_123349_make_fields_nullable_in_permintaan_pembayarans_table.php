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
        Schema::table('permintaan_pembayarans', function (Blueprint $table) {
            $table->string('nomor_tagihan')->nullable()->change();
            $table->integer('termin_ke')->nullable()->change();
            $table->decimal('persentase_kemajuan', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_pembayarans', function (Blueprint $table) {
            $table->string('nomor_tagihan')->nullable(false)->change();
            $table->integer('termin_ke')->nullable(false)->change();
            $table->decimal('persentase_kemajuan', 5, 2)->nullable(false)->change();
        });
    }
};
