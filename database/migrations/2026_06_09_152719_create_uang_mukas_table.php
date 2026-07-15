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
        Schema::create('uang_mukas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyeks')->onDelete('cascade');
            $table->foreignId('kontraktor_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->decimal('nilai_pengajuan', 15, 2);
            $table->string('surat_permohonan')->nullable();
            $table->enum('status', ['menunggu_persetujuan', 'disetujui', 'ditolak'])->default('menunggu_persetujuan');
            $table->text('catatan_ppk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_mukas');
    }
};
