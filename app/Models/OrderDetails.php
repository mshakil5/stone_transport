<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function buyOneGetOne()
    {
        return $this->belongsTo(BuyOneGetOne::class, 'buy_one_get_ones_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

}
