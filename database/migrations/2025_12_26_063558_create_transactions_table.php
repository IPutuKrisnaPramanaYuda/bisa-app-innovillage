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
        Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('umkm_id')->constrained()->onDelete('cascade'); // <-- Pastikan ada
        $table->enum('type', ['IN', 'OUT']); 
        $table->foreignId('product_id')->nullable()->constrained();
        $table->integer('quantity');
        $table->decimal('amount', 12, 2);
        $table->date('date');
        $table->string('description')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
