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
        Schema::table('laporan_harians', function (Blueprint $table) {
            $table->json('dokumentasi')->nullable();
        });
        Schema::table('laporan_mingguans', function (Blueprint $table) {
            $table->json('dokumentasi')->nullable();
        });
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->json('dokumentasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_harians', function (Blueprint $table) {
            $table->dropColumn('dokumentasi');
        });
        Schema::table('laporan_mingguans', function (Blueprint $table) {
            $table->dropColumn('dokumentasi');
        });
        Schema::table('laporan_bulanans', function (Blueprint $table) {
            $table->dropColumn('dokumentasi');
        });
    }
};
