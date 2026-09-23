<?php

namespace App\Models;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    protected $guarded = [];
public function items()
{
    return $this->hasMany(OrderItem::class);
}

public function customer()
{
    return $this->belongsTo(customer::class);
}

public function promo()
{
    return $this->belongsTo(promo::class);
}
}
