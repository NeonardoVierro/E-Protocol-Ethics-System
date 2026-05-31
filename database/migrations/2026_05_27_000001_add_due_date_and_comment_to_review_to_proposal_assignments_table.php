<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposal_assignments', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('notes');
            $table->text('comment_to_review')->nullable()->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('proposal_assignments', function (Blueprint $table) {
            $table->dropColumn(['comment_to_review', 'due_date']);
        });
    }
};
