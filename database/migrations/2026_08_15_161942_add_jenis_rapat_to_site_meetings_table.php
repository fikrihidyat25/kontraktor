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
            $table->enum('jenis_rapat', ['Site Meeting', 'PCM'])->default('Site Meeting')->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_meetings', function (Blueprint $table) {
            $table->dropColumn('jenis_rapat');
        });
    }
};
