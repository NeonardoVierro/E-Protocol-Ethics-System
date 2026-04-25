<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // peneliti
            $table->string('title');
            $table->text('description');
            $table->enum('status', [
                'new_proposal', 
                'on_review', 
                'revised', 
                'approved', 
                'rejected'
            ])->default('new_proposal');
            $table->enum('review_type', ['exempted', 'expedited', 'full_board'])->nullable();
            $table->foreignId('sekretaris_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('submission_date')->nullable();
            $table->date('review_date')->nullable();
            $table->date('decision_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk pencarian
            $table->index('status');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
