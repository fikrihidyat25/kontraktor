<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peralatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_harian_id')->constrained('laporan_harians')->onDelete('cascade');
            $table->string('jenis_alat');
            $table->integer('jumlah')->default(1);
            $table->enum('kondisi', ['baik', 'rusak_ringan', 'rusak_berat', 'tidak_beroperasi'])->default('baik');
            $table->decimal('jam_operasi', 6, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};
