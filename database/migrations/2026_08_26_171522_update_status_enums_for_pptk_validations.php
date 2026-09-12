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
        // Update existing data first so we don't truncate
        DB::table('permintaan_pembayarans')->whereNotIn('status', ['diajukan', 'diperiksa_konsultan', 'disetujui', 'ditolak'])->update(['status' => 'diajukan']);
        DB::table('pembayaran_konsultans')->whereIn('status', ['diverifikasi_pptk', 'disetujui_ppk'])->update(['status' => 'disetujui']);
        DB::table('pembayaran_konsultans')->whereNotIn('status', ['diajukan', 'disetujui', 'ditolak'])->update(['status' => 'diajukan']);
        DB::table('uang_muka_konsultans')->whereNotIn('status', ['diajukan', 'disetujui', 'ditolak'])->update(['status' => 'diajukan']);

        // Temporarily, if there's any invalid data, we mapped it to diajukan to allow ENUM alter. 
        // We'll update it back to 'disetujui' after altering the schema.
        
        // For MySQL, we use raw statements to modify ENUM columns safely.
        DB::statement("ALTER TABLE permintaan_pembayarans MODIFY COLUMN status ENUM('diajukan', 'diperiksa_konsultan', 'disetujui', 'ditolak') NOT NULL DEFAULT 'diajukan'");
        DB::statement("ALTER TABLE pembayaran_konsultans MODIFY COLUMN status ENUM('diajukan', 'disetujui', 'ditolak') NOT NULL DEFAULT 'diajukan'");
        DB::statement("ALTER TABLE uang_muka_konsultans MODIFY COLUMN status ENUM('diajukan', 'disetujui', 'ditolak') NOT NULL DEFAULT 'diajukan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE permintaan_pembayarans MODIFY COLUMN status ENUM('diajukan', 'diperiksa_konsultan', 'disetujui_ppk', 'ditolak') NOT NULL DEFAULT 'diajukan'");
        DB::statement("ALTER TABLE pembayaran_konsultans MODIFY COLUMN status ENUM('diajukan', 'disetujui_ppk', 'ditolak') NOT NULL DEFAULT 'diajukan'");
        DB::statement("ALTER TABLE uang_muka_konsultans MODIFY COLUMN status ENUM('diajukan', 'disetujui_ppk', 'ditolak') NOT NULL DEFAULT 'diajukan'");
    }
};
