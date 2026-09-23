<?php

namespace App\Models;

use App\Models\product;
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;
    protected $guarded = [];
public function product()
{
    return $this->belongsTo(Product::class);
}

public function addons()
{
    return $this->hasMany(OrderItemAddon::class,'order_item_id');
}
}
