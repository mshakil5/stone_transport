<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleProduct extends Model
{
    use HasFactory;

    public function images()
    {
        return $this->hasMany(BundleProductImage::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'bundle_product_id');
    }
}
