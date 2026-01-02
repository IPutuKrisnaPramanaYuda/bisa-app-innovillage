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
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
        
        // --- INI YANG HILANG DI TEMPAT ANDA ---
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        // --------------------------------------

        $table->text('message'); // Pesan User
        $table->text('response'); // Balasan AI
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_sessions');
    }
};
