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
            // Status: pending (belum bayar), paid (lunas), shipped (dikirim), done (selesai)
        $table->enum('status', ['pending', 'paid', 'shipped', 'done'])->default('pending')->after('amount');
        $table->string('proof_of_payment')->nullable()->after('status'); // Foto struk
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
        $table->dropColumn(['status', 'proof_of_payment']);
        });
    }
};
