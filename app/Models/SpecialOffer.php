<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

    public function specialOfferDetails()
    {
        return $this->hasMany(SpecialOfferDetails::class);
    }
}
