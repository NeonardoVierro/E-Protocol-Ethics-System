<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->string('nama_peneliti')->nullable()->after('user_id');
            $table->string('asal_instansi')->nullable()->after('nama_peneliti');
        });
    }

    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            $table->dropColumn(['nama_peneliti', 'asal_instansi']);
        });
    }
};
