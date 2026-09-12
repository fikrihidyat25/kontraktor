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
        Schema::create('pembayaran_konsultans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained()->onDelete('cascade');
            $table->foreignId('konsultan_id')->constrained('users')->onDelete('cascade');
            $table->string('termin');
            $table->decimal('nilai_pembayaran', 15, 2);
            $table->string('surat_permohonan');
            $table->string('laporan_kemajuan');
            $table->json('lampiran_lainnya')->nullable();
            $table->enum('status', ['diajukan', 'diverifikasi_pptk', 'disetujui', 'ditolak'])->default('diajukan');
            $table->text('catatan_pptk')->nullable();
            $table->text('catatan_ppk')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null'); // PPTK
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null'); // PPK
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_konsultans');
    }
};
