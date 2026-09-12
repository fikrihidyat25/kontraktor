<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kontraktor', 'konsultan', 'ppk', 'pptk') DEFAULT 'kontraktor'");

        Schema::table('proyeks', function (Blueprint $table) {
            $table->unsignedBigInteger('pptk_id')->nullable()->after('ppk_id');
            $table->foreign('pptk_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyeks', function (Blueprint $table) {
            $table->dropForeign(['pptk_id']);
            $table->dropColumn('pptk_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'kontraktor', 'konsultan', 'ppk') DEFAULT 'kontraktor'");
    }
};
