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
        Schema::table('site_meetings', function (Blueprint $table) {
            $table->text('dokumentasi')->nullable()->after('dokumen_berita_acara');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_meetings', function (Blueprint $table) {
            $table->dropColumn('dokumentasi');
        });
    }
};
