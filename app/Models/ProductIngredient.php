<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    protected $fillable = ['product_id', 'inventory_item_id', 'amount'];

    public function inventory()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }
}