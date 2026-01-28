<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('testimonials', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Jika user login
        $table->string('name'); // Nama (Manual atau dari User)
        $table->string('email')->nullable(); // Email (untuk gravatar/kontak)
        $table->text('message'); // Isi Komentar
        $table->string('role')->default('Pengunjung'); // Label (Pengunjung / Warga)
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
