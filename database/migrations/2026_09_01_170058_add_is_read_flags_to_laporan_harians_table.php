<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_harians', function (Blueprint $table) {
            $table->boolean('is_read_kontraktor')->default(true);
            $table->boolean('is_read_konsultan')->default(true);
            $table->boolean('is_read_pptk')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('laporan_harians', function (Blueprint $table) {
            $table->dropColumn(['is_read_kontraktor', 'is_read_konsultan', 'is_read_pptk']);
        });
    }
};
