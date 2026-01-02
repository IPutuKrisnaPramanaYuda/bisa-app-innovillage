<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            // Kolom untuk menyimpan isi teks PDF (LONGTEXT muat novel tebal)
            $table->longText('extracted_text')->nullable()->after('file_path');
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->dropColumn('extracted_text');
        });
    }
};