<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('inventory_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('umkm_id')->constrained()->onDelete('cascade');
        $table->string('name'); // Nama Bahan/Alat
        $table->enum('category', ['bahan', 'alat']); // Kategori
        $table->integer('stock'); // Jumlah Stok
        $table->string('unit'); // Satuan (ml, gram, pcs, lembar)
        $table->decimal('price_per_unit', 15, 2); // Harga per 1 satuan (Penting untuk HPP)
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('inventory_items');
}
};
