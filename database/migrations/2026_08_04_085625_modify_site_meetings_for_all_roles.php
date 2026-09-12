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
        Schema::table('site_meetings', function (Blueprint $table) {
            $table->dropForeign(['konsultan_id']);
            $table->renameColumn('konsultan_id', 'created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_meetings', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->renameColumn('created_by', 'konsultan_id');
            $table->foreign('konsultan_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
