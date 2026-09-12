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
        Schema::table('shop_drawings', function (Blueprint $table) {
            $table->renameColumn('catatan_konsultan', 'catatan_pptk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_drawings', function (Blueprint $table) {
            $table->renameColumn('catatan_pptk', 'catatan_konsultan');
        });
    }
};
