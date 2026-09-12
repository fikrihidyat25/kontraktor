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
            $table->integer('pembayaran_ke')->default(1)->after('kontraktor_id');
            $table->decimal('progres_tagihan', 5, 2)->nullable()->after('pembayaran_ke');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permintaan_pembayarans', function (Blueprint $table) {
            $table->dropColumn(['pembayaran_ke', 'progres_tagihan']);
        });
    }
};
