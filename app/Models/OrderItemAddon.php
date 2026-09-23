<?php

namespace App\Models;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItemAddon extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemAddonFactory> */
    use HasFactory;
    protected $guarded = [];
public function addon()
{
    return $this->belongsTo(Product::class,'addon_id');
}
}
