<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'new_proposal',
            'in_process',
            'on_review',
            'revised',
            'approved',
            'waiting_for_confirmation',
            'ready_for_chair',
            'with_chair',
            'waiting_for_publish',
            'published',
            'rejected'
        ) NOT NULL DEFAULT 'new_proposal'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'new_proposal',
            'in_process',
            'on_review',
            'revised',
            'approved',
            'waiting_for_confirmation',
            'waiting_for_publish',
            'published',
            'rejected'
        ) NOT NULL DEFAULT 'new_proposal'");
    }
};
