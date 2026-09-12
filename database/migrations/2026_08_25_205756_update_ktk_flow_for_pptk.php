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
        // Convert to varchar first to avoid truncation during data migration
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE usulan_dari usulan_dari VARCHAR(50) DEFAULT 'kontraktor'");
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE status status VARCHAR(50) DEFAULT 'menunggu_validasi'");

        // Migrate existing data
        DB::table('kerja_tambah_kurangs')->whereIn('status', ['diajukan', 'diverifikasi_konsultan'])->update(['status' => 'menunggu_validasi']);
        DB::table('kerja_tambah_kurangs')->where('status', 'ditolak')->update(['status' => 'revisi']);
        DB::table('kerja_tambah_kurangs')->where('status', 'disetujui_ppk')->update(['status' => 'disetujui']);
        
        DB::table('kerja_tambah_kurangs')->where('usulan_dari', 'ppk')->update(['usulan_dari' => 'kontraktor']);

        // Change enums using raw SQL for MySQL (now safe)
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE usulan_dari usulan_dari ENUM('kontraktor', 'pptk') DEFAULT 'kontraktor'");
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE status status ENUM('menunggu_validasi', 'revisi', 'disetujui') DEFAULT 'menunggu_validasi'");

        Schema::table('kerja_tambah_kurangs', function (Blueprint $table) {
            $table->renameColumn('catatan_ppk', 'catatan_evaluasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kerja_tambah_kurangs', function (Blueprint $table) {
            $table->renameColumn('catatan_evaluasi', 'catatan_ppk');
        });

        // Convert to varchar
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE usulan_dari usulan_dari VARCHAR(50) DEFAULT 'kontraktor'");
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE status status VARCHAR(50) DEFAULT 'diajukan'");

        // Migrate back
        DB::table('kerja_tambah_kurangs')->where('status', 'menunggu_validasi')->update(['status' => 'diajukan']);
        DB::table('kerja_tambah_kurangs')->where('status', 'revisi')->update(['status' => 'ditolak']);
        DB::table('kerja_tambah_kurangs')->where('status', 'disetujui')->update(['status' => 'disetujui_ppk']);
        DB::table('kerja_tambah_kurangs')->where('usulan_dari', 'pptk')->update(['usulan_dari' => 'ppk']);

        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE usulan_dari usulan_dari ENUM('kontraktor', 'ppk') DEFAULT 'kontraktor'");
        DB::statement("ALTER TABLE kerja_tambah_kurangs CHANGE status status ENUM('diajukan', 'diverifikasi_konsultan', 'disetujui_ppk', 'ditolak') DEFAULT 'diajukan'");
    }
};
