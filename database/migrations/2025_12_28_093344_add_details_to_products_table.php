<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            
            // 1. Cek: Apakah kolom 'image' SUDAH ADA? Jika BELUM, baru buat.
            if (!Schema::hasColumn('products', 'image')) {
                $table->string('image')->nullable()->after('name');
            }

            // 2. Cek: Apakah kolom 'cost_price' SUDAH ADA? Jika BELUM, baru buat.
            if (!Schema::hasColumn('products', 'cost_price')) {
                $table->decimal('cost_price', 15, 2)->default(0)->after('price');
            }
        });
    }

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn(['image', 'cost_price']);
    });
}
};
