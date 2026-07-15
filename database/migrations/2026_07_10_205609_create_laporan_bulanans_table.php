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
        Schema::create('laporan_bulanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyeks')->onDelete('cascade');
            $table->foreignId('kontraktor_id')->constrained('users')->onDelete('cascade');
            $table->integer('bulan');
            $table->integer('tahun');
            $table->decimal('bobot_rencana', 5, 2)->default(0);    // % rencana kumulatif
            $table->decimal('bobot_realisasi', 5, 2)->default(0);  // % realisasi kumulatif
            $table->decimal('deviasi', 6, 2)->default(0);          // bisa negatif
            $table->text('ringkasan_kemajuan')->nullable();
            $table->text('kendala')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'approved', 'rejected'])->default('draft');
            $table->string('file_laporan')->nullable();
            $table->text('catatan_konsultan')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_ppk')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_bulanans');
    }
};
