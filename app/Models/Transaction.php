<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    // Pastikan 'quantity' ada di sini
   protected $fillable = [
        'umkm_id',
        'buyer_id',
        'product_id',
        'type',
        'amount',
        
        'cost_amount', // <--- WAJIB ADA DI SINI
        
        'quantity',
        'date',
        'description',
        'status',
        // ...
    ];

    // ... relasi lainnya biarkan saja ...
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}