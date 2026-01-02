<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('umkms', function (Blueprint $table) {
        $table->decimal('balance', 15, 2)->default(0)->after('description'); // Saldo Kas Toko
    });

    Schema::table('transactions', function (Blueprint $table) {
        $table->decimal('cost_amount', 15, 2)->default(0)->after('amount'); // Total HPP transaksi ini
    });
}

public function down()
{
    Schema::table('umkms', function (Blueprint $table) { $table->dropColumn('balance'); });
    Schema::table('transactions', function (Blueprint $table) { $table->dropColumn('cost_amount'); });
}
};
