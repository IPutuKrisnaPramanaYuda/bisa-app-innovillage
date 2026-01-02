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
        Schema::create('umkms', function (Blueprint $table) {
        $table->id();
        
        // --- BARIS INI YANG HILANG DI DATABASE ANDA ---
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        // ----------------------------------------------

        $table->string('name');
        $table->string('slug')->unique(); // Pastikan ada ini juga untuk link toko
        $table->text('description')->nullable();
        $table->text('address')->nullable();
        $table->string('logo')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }

    
};
