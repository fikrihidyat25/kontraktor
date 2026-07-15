<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_harian_id')->constrained('laporan_harians')->onDelete('cascade');
            $table->string('jenis_material');
            $table->string('satuan');
            $table->decimal('kuantitas_datang', 10, 2)->default(0);
            $table->decimal('kuantitas_digunakan', 10, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
