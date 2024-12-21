<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleProductImage extends Model
{
    use HasFactory;

    public function bundleProduct()
    {
        return $this->belongsTo(BundleProduct::class);
    }
}
