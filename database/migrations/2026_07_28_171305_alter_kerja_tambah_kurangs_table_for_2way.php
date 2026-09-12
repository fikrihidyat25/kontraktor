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
            $table->enum('usulan_dari', ['kontraktor', 'ppk'])->default('kontraktor')->after('tanggal_pengajuan');
            $table->dropColumn(['jenis_ktk', 'nilai_estimasi', 'catatan_konsultan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerja_tambah_kurangs', function (Blueprint $table) {
            $table->dropColumn('usulan_dari');
            $table->string('jenis_ktk')->nullable();
            $table->decimal('nilai_estimasi', 15, 2)->nullable();
            $table->text('catatan_konsultan')->nullable();
        });
    }
};
