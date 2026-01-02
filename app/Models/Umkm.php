<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Mengizinkan semua kolom diisi kecuali ID

    // Relasi balik: UMKM dimiliki User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    // Relasi: UMKM punya banyak Produk
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relasi: UMKM punya banyak Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}