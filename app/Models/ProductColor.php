<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
