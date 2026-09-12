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
        Schema::table('dokumen_proyeks', function (Blueprint $table) {
            $table->enum('status', ['menunggu_validasi', 'disetujui', 'ditolak'])->default('menunggu_validasi')->after('uploaded_by');
            $table->text('catatan_validasi')->nullable()->after('status');
        });
        
        // Asumsi data yang sudah ada kita anggap disetujui (opsional)
        DB::table('dokumen_proyeks')->update(['status' => 'disetujui']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dokumen_proyeks', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan_validasi']);
        });
    }
};
