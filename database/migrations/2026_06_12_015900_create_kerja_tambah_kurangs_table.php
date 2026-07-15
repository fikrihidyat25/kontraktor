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
        Schema::create('kerja_tambah_kurangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kontraktor_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_surat_pengajuan');
            $table->date('tanggal_pengajuan');
            $table->enum('jenis_ktk', ['tambah', 'kurang']);
            $table->text('deskripsi_pekerjaan');
            $table->decimal('nilai_estimasi', 15, 2);
            $table->enum('status', ['diajukan', 'diverifikasi_konsultan', 'disetujui_ppk', 'ditolak'])->default('diajukan');
            $table->text('catatan_konsultan')->nullable();
            $table->text('catatan_ppk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kerja_tambah_kurangs');
    }
};
