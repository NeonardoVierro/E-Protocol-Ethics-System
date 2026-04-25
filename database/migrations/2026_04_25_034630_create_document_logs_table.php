<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ethics_document_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('proposal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('activity', [
                'upload', 'download', 'view', 'sign', 'publish', 
                'archive', 'delete', 'update', 'assign', 'verify'
            ]);
            $table->string('ip_address')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('activity');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_logs');
    }
};