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
            $table->foreignId('ethics_document_id')->nullable()->constrained('ethics_documents')->nullOnDelete();
            $table->foreignId('proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('activity', [
                'upload',
                'download',
                'view',
                'sign',
                'publish',
                'archive',
                'delete',
                'update',
                'assign',
                'verify',
                'sent_to_admin',
            ]);

            $table->string('ip_address', 45)->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['proposal_id', 'activity']);
            $table->index(['ethics_document_id', 'activity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_logs');
    }
};