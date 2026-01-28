<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    // 🔥 INI KUNCI AGAR DATA BISA MASUK 🔥
    protected $guarded = []; 

    public function umkm()
    {
        return $this->belongsTo(Umkm::class);
    }
}