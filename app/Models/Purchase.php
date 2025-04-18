<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchaseHistory()
    {
        return $this->hasMany(PurchaseHistory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function purchaseHistoryCount()
    {
        $purchase = self::withCount('purchaseHistory')
        ->get();
        return $purchase;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function motherVessel()
    {
        return $this->belongsTo(MotherVassel::class, 'mother_vassels_id', 'id');
    }
}
