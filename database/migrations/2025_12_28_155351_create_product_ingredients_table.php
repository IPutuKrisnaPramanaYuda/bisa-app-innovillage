<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('product_ingredients', function (Blueprint $table) {
        $table->id();
        $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Link ke Produk
        $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade'); // Link ke Bahan Baku
        $table->decimal('amount', 10, 2); // Jumlah pemakaian (misal: 15 gram)
        $table->timestamps();
    });

    // Opsional: Hapus kolom stok di tabel produk agar tidak bingung (atau biarkan tapi abaikan)
    // Schema::table('products', function (Blueprint $table) {
    //     $table->dropColumn('stock'); 
    // });
}

public function down()
{
    Schema::dropIfExists('product_ingredients');
}
};
