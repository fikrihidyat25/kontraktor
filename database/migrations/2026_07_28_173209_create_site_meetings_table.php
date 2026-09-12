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
        Schema::create('site_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained()->onDelete('cascade');
            $table->foreignId('konsultan_id')->constrained('users')->onDelete('cascade');
            $table->integer('pertemuan_ke');
            $table->date('tanggal_rapat');
            $table->text('agenda_rapat')->nullable();
            $table->string('dokumen_berita_acara');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_meetings');
    }
};
