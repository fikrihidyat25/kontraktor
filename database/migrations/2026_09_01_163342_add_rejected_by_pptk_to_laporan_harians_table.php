<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE laporan_harians MODIFY COLUMN status ENUM('draft', 'submitted', 'verified', 'approved', 'rejected', 'rejected_by_pptk') DEFAULT 'draft'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE laporan_harians MODIFY COLUMN status ENUM('draft', 'submitted', 'verified', 'approved', 'rejected') DEFAULT 'draft'");
    }
};
