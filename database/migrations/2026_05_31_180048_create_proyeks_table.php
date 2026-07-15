<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proyeks', function (Blueprint $table) {
            $table->id();
            $table->string('nama_proyek');
            $table->string('nomor_kontrak')->nullable();
            $table->text('lokasi');
            $table->decimal('nilai_kontrak', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->foreignId('kontraktor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('konsultan_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('ppk_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['aktif', 'selesai', 'dihentikan'])->default('aktif');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proyeks');
    }
};
