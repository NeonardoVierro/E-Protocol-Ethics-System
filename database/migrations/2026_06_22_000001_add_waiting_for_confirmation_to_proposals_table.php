<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ALTER ENUM tidak mendukung menambahkan nilai baru tanpa mendefinisikan ulang seluruh enum.
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

    public function down(): void
    {
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM(
            'new_proposal',
            'in_process',
            'on_review',
            'revised',
            'approved',
            'waiting_for_publish',
            'published',
            'rejected'
        ) NOT NULL DEFAULT 'new_proposal'");
    }
};
