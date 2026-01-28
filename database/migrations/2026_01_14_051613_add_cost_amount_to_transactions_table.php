<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            
            // 1. Cek dulu apakah kolom 'cost_amount' SUDAH ADA?
            if (!Schema::hasColumn('transactions', 'cost_amount')) {
                $table->decimal('cost_amount', 12, 2)->default(0)->after('amount');
            }

            // 2. Cek kolom 'status'
            if (!Schema::hasColumn('transactions', 'status')) {
                $table->string('status')->default('paid')->after('description'); 
            }

            // 3. Cek kolom 'buyer_id'
            if (!Schema::hasColumn('transactions', 'buyer_id')) {
                $table->foreignId('buyer_id')->nullable()->constrained('users')->nullOnDelete()->after('umkm_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Hapus kolom hanya jika ada (biar aman saat rollback)
            if (Schema::hasColumn('transactions', 'cost_amount')) {
                $table->dropColumn('cost_amount');
            }
            if (Schema::hasColumn('transactions', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('transactions', 'buyer_id')) {
                $table->dropForeign(['buyer_id']);
                $table->dropColumn('buyer_id');
            }
        });
    }
};