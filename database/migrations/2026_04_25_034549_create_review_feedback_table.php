<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained()->onDelete('cascade');
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->text('feedback_text')->nullable();
            $table->enum('recommendation', ['approved', 'revision', 'rejected'])->nullable();
            $table->string('file_path')->nullable(); // untuk upload file feedback
            $table->string('original_name')->nullable();
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            $table->index('review_id');
            $table->index('proposal_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_feedback');
    }
};