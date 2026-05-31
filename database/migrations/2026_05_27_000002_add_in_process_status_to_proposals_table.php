<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE proposals MODIFY status ENUM('new_proposal', 'in_process', 'on_review', 'revised', 'approved', 'rejected', 'waiting_for_publish', 'published') DEFAULT 'new_proposal'");
        }
        // For PostgreSQL
        elseif (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TYPE proposals_status_enum ADD VALUE 'in_process' BEFORE 'on_review'");
            DB::statement("ALTER TYPE proposals_status_enum ADD VALUE 'waiting_for_publish' AFTER 'rejected'");
            DB::statement("ALTER TYPE proposals_status_enum ADD VALUE 'published' AFTER 'waiting_for_publish'");
        }
        // For SQLite - no enum support, so we skip
    }

    public function down(): void
    {
        // For MySQL
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE proposals MODIFY status ENUM('new_proposal', 'on_review', 'revised', 'approved', 'rejected') DEFAULT 'new_proposal'");
        }
        // For PostgreSQL - cannot easily remove enum values in PostgreSQL
        // For SQLite - no enum support, so we skip
    }
};
