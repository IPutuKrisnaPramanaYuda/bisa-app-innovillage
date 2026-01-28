<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Atribut ini memastikan 'computed_stock' selalu muncul di JSON (untuk AI & API)
    protected $appends = ['computed_stock']; 

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }

    // --- PERBAIKAN UTAMA DISINI ---
    // Kita gunakan belongsToMany agar langsung terhubung ke InventoryItem
    public function ingredients()
    {
        return $this->belongsToMany(InventoryItem::class, 'product_ingredients')
                    ->withPivot('amount') // Ambil kolom 'amount' dari tabel penghubung
                    ->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // --- LOGIKA HITUNG STOK OTOMATIS (BOM) ---
    public function getComputedStockAttribute()
    {
        // 1. Jika produk TIDAK PUNYA resep, gunakan stok manual (jika ada)
        if ($this->ingredients->isEmpty()) {
            return $this->stock ?? 0;
        }

        $minStock = 9999999; // Angka awal yg sangat besar
        $hasIngredient = false;

        foreach ($this->ingredients as $item) {
            $hasIngredient = true;
            
            // $item adalah InventoryItem (Gula, Kopi, dll)
            $stokGudang = $item->stock; 
            
            // $item->pivot->amount adalah jumlah yang dibutuhkan di resep
            $butuh = $item->pivot->amount; 

            // Hindari error pembagian dengan nol
            if ($butuh <= 0) $butuh = 0.01; 

            // Hitung: Stok Gudang / Kebutuhan
            // Contoh: 1000 gram / 15 gram = 66 porsi
            $bisaBikin = floor($stokGudang / $butuh);

            // Cari nilai terkecil (Limiting Factor)
            // Misal: Kopi cukup buat 100 gelas, tapi Gula cuma cukup buat 10 gelas.
            // Maka stok produk adalah 10.
            if ($bisaBikin < $minStock) {
                $minStock = $bisaBikin;
            }
        }

        // Jika tidak ada bahan valid, stok 0
        if ($minStock == 9999999) return 0;

        return $minStock;
    }
}