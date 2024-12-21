<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function orderDetails()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryMan()
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    public function cancelledOrder()
    {
        return $this->hasOne(CancelledOrder::class);
    }

    public function orderReturns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function systemLoses()
    {
        return $this->hasMany(SystemLose::class, 'order_id');
    }

    public function bundleProduct()
    {
        return $this->belongsTo(BundleProduct::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getReceivedAmountAttribute()
    {
        return $this->hasMany(OrderDueCollection::class, 'order_id')
                    ->sum('received_amount');
    }

}
