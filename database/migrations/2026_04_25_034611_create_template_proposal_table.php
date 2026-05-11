<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_proposals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen');
            $table->string('versi', 20);
            $table->string('kategori', 50);   // Biomedis | Sosial | Umum
            $table->string('file_path');       // path di storage
            $table->string('file_name');       // nama file asli
            $table->string('file_type', 10);   // pdf | docx
            $table->bigInteger('file_size')->default(0); // bytes
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_proposals');
    }
};