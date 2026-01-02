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
        Schema::table('transactions', function (Blueprint $table) {
        // Kolom untuk menyimpan ID Pembeli
        $table->foreignId('buyer_id')->nullable()->constrained('users')->onDelete('cascade')->after('id');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
        $table->dropForeign(['buyer_id']);
        $table->dropColumn('buyer_id');
    });
    }
};
