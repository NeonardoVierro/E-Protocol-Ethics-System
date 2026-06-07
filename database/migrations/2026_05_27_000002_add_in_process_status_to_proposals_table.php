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

            // Ensure proposals has ketua_id and nomor_ec columns
            if (!Schema::hasColumn('proposals', 'ketua_id')) {
                Schema::table('proposals', function (Blueprint $table) {
                    $table->foreignId('ketua_id')->nullable()->constrained('users')->onDelete('set null');
                });
            }

            if (!Schema::hasColumn('proposals', 'nomor_ec')) {
                Schema::table('proposals', function (Blueprint $table) {
                    $table->string('nomor_ec', 100)->nullable()->unique();
                });
            }

            // Update document_logs activity enum to include 'decision'
            DB::statement("ALTER TABLE document_logs MODIFY activity ENUM('upload', 'download', 'view', 'sign', 'publish', 'archive', 'delete', 'update', 'assign', 'verify', 'decision')");
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

            // Remove nomor_ec column if exists
            if (Schema::hasColumn('proposals', 'nomor_ec')) {
                Schema::table('proposals', function (Blueprint $table) {
                    $table->dropUnique(['nomor_ec']);
                    $table->dropColumn('nomor_ec');
                });
            }

            // Remove ketua_id column if exists
            if (Schema::hasColumn('proposals', 'ketua_id')) {
                Schema::table('proposals', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('ketua_id');
                });
            }

            // Revert document_logs activity enum
            DB::statement("ALTER TABLE document_logs MODIFY activity ENUM('upload', 'download', 'view', 'sign', 'publish', 'archive', 'delete', 'update', 'assign', 'verify')");
        }
        // For PostgreSQL - cannot easily remove enum values in PostgreSQL
        // For SQLite - no enum support, so we skip
    }
};
