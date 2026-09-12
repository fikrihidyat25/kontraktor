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
        Schema::table('laporan_lainnyas', function (Blueprint $table) {
            $table->enum('status', ['menunggu_validasi', 'disetujui', 'ditolak'])->default('menunggu_validasi')->after('uploaded_by');
            $table->text('catatan_pptk')->nullable()->after('status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('catatan_pptk');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
        });
        
        DB::table('laporan_lainnyas')->update(['status' => 'disetujui']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_lainnyas', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_pptk', 'verified_by', 'verified_at']);
        });
    }
};
