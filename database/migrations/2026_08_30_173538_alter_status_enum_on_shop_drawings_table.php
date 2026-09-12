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
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE shop_drawings MODIFY COLUMN status ENUM('diajukan', 'diverifikasi_konsultan', 'disetujui', 'ditolak') DEFAULT 'diajukan'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE shop_drawings MODIFY COLUMN status ENUM('diajukan', 'disetujui', 'ditolak') DEFAULT 'diajukan'");
    }
};
