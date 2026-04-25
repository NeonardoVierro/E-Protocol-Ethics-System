<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->enum('file_type', ['proposal_document', 'supporting_document', 'revision_document']);
            $table->string('original_name');
            $table->string('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('proposal_id');
            $table->index('file_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_files');
    }
};