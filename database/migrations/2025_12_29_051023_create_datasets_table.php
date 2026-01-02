<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Siapa kontributornya
            $table->string('title');        // Judul Jurnal/Dokumen
            $table->text('description')->nullable();
            $table->string('file_path');    // Lokasi file di folder storage
            $table->string('file_type');    // pdf, docx, txt
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // Moderasi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};