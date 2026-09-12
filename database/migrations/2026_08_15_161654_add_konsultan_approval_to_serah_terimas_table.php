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
        Schema::table('serah_terimas', function (Blueprint $table) {
            $table->string('surat_persetujuan_konsultan')->nullable()->after('dokumen_lampiran');
            $table->enum('status_konsultan', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('status');
            $table->text('catatan_konsultan')->nullable()->after('status_konsultan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serah_terimas', function (Blueprint $table) {
            $table->dropColumn(['surat_persetujuan_konsultan', 'status_konsultan', 'catatan_konsultan']);
        });
    }
};
