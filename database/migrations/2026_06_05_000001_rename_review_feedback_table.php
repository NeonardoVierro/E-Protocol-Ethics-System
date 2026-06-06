<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('review_feedback') && ! Schema::hasTable('review_feedbacks')) {
            Schema::rename('review_feedback', 'review_feedbacks');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('review_feedbacks') && ! Schema::hasTable('review_feedback')) {
            Schema::rename('review_feedbacks', 'review_feedback');
        }
    }
};
