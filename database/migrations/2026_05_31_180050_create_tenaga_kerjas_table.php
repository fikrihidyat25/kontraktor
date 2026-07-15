<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenaga_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_harian_id')->constrained('laporan_harians')->onDelete('cascade');
            $table->enum('klasifikasi', ['mandor', 'tukang', 'pembantu_tukang', 'sub_kontraktor', 'lainnya']);
            $table->string('keterangan')->nullable();
            $table->integer('jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenaga_kerjas');
    }
};
