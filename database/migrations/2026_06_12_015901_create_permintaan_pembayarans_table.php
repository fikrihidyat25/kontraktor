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
        Schema::create('permintaan_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kontraktor_id')->constrained('users')->cascadeOnDelete();
            $table->string('nomor_tagihan');
            $table->date('tanggal_pengajuan');
            $table->integer('termin_ke');
            $table->decimal('nilai_tagihan', 15, 2);
            $table->decimal('persentase_kemajuan', 5, 2);
            $table->enum('status', ['diajukan', 'diperiksa_konsultan', 'disetujui_ppk', 'ditolak'])->default('diajukan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_pembayarans');
    }
};
