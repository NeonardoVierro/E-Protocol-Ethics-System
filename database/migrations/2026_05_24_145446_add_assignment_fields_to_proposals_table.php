<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom nomor_ec & ketua_id di proposals
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('nomor_ec')->nullable()->after('sekretaris_id');
            $table->foreignId('ketua_id')->nullable()->constrained('users')->nullOnDelete()->after('nomor_ec');
        });

        // 2. Ubah enum status proposals — tambah waiting_for_publish & published
        // MySQL tidak support ALTER ENUM langsung, pakai MODIFY
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'new_proposal',
            'on_review',
            'revised',
            'approved',
            'rejected',
            'waiting_for_publish',
            'published'
        ) NOT NULL DEFAULT 'new_proposal'");

        // 3. Tambah sent_at di proposal_assignments
        Schema::table('proposal_assignments', function (Blueprint $table) {
            $table->timestamp('sent_at')->nullable()->after('notes');
            // null  = dipilih tapi belum dikirim
            // filled = sudah dikirim
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropForeign(['ketua_id']);
            $table->dropColumn(['nomor_ec', 'ketua_id']);
        });

        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'new_proposal',
            'on_review',
            'revised',
            'approved',
            'rejected'
        ) NOT NULL DEFAULT 'new_proposal'");

        Schema::table('proposal_assignments', function (Blueprint $table) {
            $table->dropColumn('sent_at');
        });
    }
};