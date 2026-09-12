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
        Schema::table('serah_terimas', function (Blueprint $table) {
            $table->enum('status_pptk', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('catatan_konsultan');
            $table->text('catatan_pptk')->nullable()->after('status_pptk');
            $table->string('surat_persetujuan_ppk')->nullable()->after('catatan_ppk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('serah_terimas', function (Blueprint $table) {
            $table->dropColumn(['status_pptk', 'catatan_pptk', 'surat_persetujuan_ppk']);
        });
    }
};
