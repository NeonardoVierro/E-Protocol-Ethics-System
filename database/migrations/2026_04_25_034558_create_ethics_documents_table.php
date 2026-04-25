<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ethics_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->string('document_number')->unique();
            $table->foreignId('ketua_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'signed', 'published', 'archived'])->default('draft');
            $table->string('file_path');
            $table->string('original_name');
            $table->date('signed_date')->nullable();
            $table->date('published_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('document_number');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ethics_documents');
    }
};