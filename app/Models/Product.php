<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Pastikan ini ada agar bisa diupdate
    protected $guarded = []; 

    // --- BAGIAN PENTING (JANGAN LEWATKAN) ---
    // Ini menyuruh Laravel: "Tolong selalu bawa hasil hitungan stok setiap kali panggil produk"
    protected $appends = ['computed_stock']; 
    // ----------------------------------------

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }

    public function ingredients()
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // LOGIKA HITUNG STOK OTOMATIS
    public function getComputedStockAttribute()
    {
        // 1. Jika tidak ada bahan baku (resep kosong), kembalikan 0
        if ($this->ingredients->count() == 0) {
            return 0;
        }

        $minStock = 9999999; // Angka awal yg sangat besar

        foreach ($this->ingredients as $ing) {
            // Ambil stok bahan dari gudang
            $inventoryItem = $ing->inventory;

            // Jika bahan dihapus/hilang, anggap stok 0
            if (!$inventoryItem) return 0;

            $stokGudang = $inventoryItem->stock; // Misal 1000
            $butuh = $ing->amount; // Misal 10

            if ($butuh <= 0) $butuh = 1; // Hindari pembagian 0

            // Hitung: 1000 / 10 = 100
            $bisaBikin = floor($stokGudang / $butuh);

            // Cari nilai terkecil (Limiting Factor)
            if ($bisaBikin < $minStock) {
                $minStock = $bisaBikin;
            }
        }

        // Jika tidak ada perubahan (looping gagal), return 0
        if ($minStock == 9999999) return 0;

        return $minStock;
    }
}