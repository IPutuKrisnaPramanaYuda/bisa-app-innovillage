<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // 🔥 GANTI INI BIAR GAK RIBET 🔥
    // Kita buka akses semua kolom, biar database yang memvalidasi
    protected $guarded = []; 

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}