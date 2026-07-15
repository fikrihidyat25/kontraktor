<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_harians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyeks')->onDelete('cascade');
            $table->foreignId('kontraktor_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('kondisi_cuaca', ['cerah', 'berawan', 'hujan_ringan', 'hujan_lebat'])->default('cerah');
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'submitted', 'verified', 'approved', 'rejected'])->default('draft');
            $table->text('catatan_konsultan')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('catatan_ppk')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_harians');
    }
};
