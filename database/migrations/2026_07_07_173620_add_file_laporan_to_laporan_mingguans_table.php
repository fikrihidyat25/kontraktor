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
        Schema::table('laporan_mingguans', function (Blueprint $table) {
            $table->string('file_laporan')->nullable()->after('tanggal_selesai');
            
            // Note: Since bobot_rencana and bobot_realisasi have default(0), we don't strictly need to make them nullable right now,
            // but we can make them nullable to be semantically correct if we wanted. 
            // We will leave them as default(0) for simplicity.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mingguans', function (Blueprint $table) {
            $table->dropColumn('file_laporan');
        });
    }
};
