<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('realisasi_biayas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_harian_id')->constrained('laporan_harians')->onDelete('cascade');
            $table->string('divisi_pekerjaan');
            $table->decimal('nilai_realisasi', 15, 2)->default(0);
            $table->decimal('bobot_fisik', 5, 2)->default(0); // persentase %
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_biayas');
    }
};
