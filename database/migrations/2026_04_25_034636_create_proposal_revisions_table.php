<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->integer('revision_number');
            $table->text('revision_note')->nullable();
            $table->date('requested_date')->nullable();
            $table->date('submitted_date')->nullable();
            $table->enum('status', ['requested', 'in_progress', 'submitted', 'accepted', 'rejected'])->default('requested');
            $table->foreignId('file_id')->nullable()->constrained('proposal_files')->onDelete('set null');
            $table->timestamps();
            
            $table->unique(['proposal_id', 'revision_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_revisions');
    }
};