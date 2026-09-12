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
        Schema::table('kerja_tambah_kurangs', function (Blueprint $table) {
            $table->string('jenis_ktk')->nullable();
            $table->decimal('nilai_estimasi', 15, 2)->nullable();
            $table->text('catatan_konsultan')->nullable();
            $table->string('dokumen_pendukung')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerja_tambah_kurangs', function (Blueprint $table) {
            $table->dropColumn(['jenis_ktk', 'nilai_estimasi', 'catatan_konsultan', 'dokumen_pendukung']);
        });
    }
};
