<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'status',
        'extracted_text', 
    ];

    // Relasi ke User (Kontributor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}