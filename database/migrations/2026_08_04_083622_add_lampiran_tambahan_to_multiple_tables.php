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
        $tables = [
            'laporan_mingguans',
            'serah_terimas',
            'laporan_bulanans',
            'laporan_harians',
            'dokumen_proyeks',
            'site_meetings',
            'kerja_tambah_kurangs',
            'permintaan_pembayarans'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->string('lampiran_tambahan')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'laporan_mingguans',
            'serah_terimas',
            'laporan_bulanans',
            'laporan_harians',
            'dokumen_proyeks',
            'site_meetings',
            'kerja_tambah_kurangs',
            'permintaan_pembayarans'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('lampiran_tambahan');
            });
        }
    }
};
