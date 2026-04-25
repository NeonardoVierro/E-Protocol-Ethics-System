<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk hierarki role (opsional, untuk menentukan siapa bisa assign apa)
        Schema::create('role_hierarchy', function (Blueprint $table) {
            $table->id();
            $table->string('role_name');
            $table->integer('level'); // 1=peneliti, 2=reviewer, 3=sekretaris, 4=ketua, 5=admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_hierarchy');
    }
};